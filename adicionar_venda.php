<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION)) {
    header("Location: index.php");
    exit();
}
include_once "includes/config/banco.php";

$statusMessage = isset($_SESSION['statusMessage']) ? $_SESSION['statusMessage'] : '';
$statusType = isset($_SESSION['statusType']) ? $_SESSION['statusType'] : '';
unset($_SESSION['statusMessage'], $_SESSION['statusType']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST["data"];
    $total = $_POST["total"];
    $materiais = $_POST["materiais"];
    $quantidades = $_POST["quantidades"];
    $precos = $_POST["precos"];
    $user = $_SESSION["usuario"];

    $banco->begin_transaction();
    try {
        $estoqueSuficiente = true;

        // Verificar se há quantidade suficiente de cada material no estoque
        foreach ($materiais as $index => $id_material) {
            $quantidade = $quantidades[$index];
            $query_verifica_estoque = "SELECT quantidade FROM material WHERE id_material = ?";
            $stmt_verifica_estoque = $banco->prepare($query_verifica_estoque);
            $stmt_verifica_estoque->bind_param("i", $id_material);
            $stmt_verifica_estoque->execute();
            $stmt_verifica_estoque->bind_result($quantidade_estoque);
            $stmt_verifica_estoque->fetch();
            $stmt_verifica_estoque->close();

            if ($quantidade > $quantidade_estoque) {
                $estoqueSuficiente = false;
                break;
            }
        }

        if ($estoqueSuficiente) {
            // Inserir a venda
            $query_venda = "INSERT INTO venda (data, total, usuario) VALUES (?, ?, ?)";
            $stmt = $banco->prepare($query_venda);
            $stmt->bind_param("sds", $data, $total, $user);
            $stmt->execute();

            $id_venda = $banco->insert_id;

            // Inserir os materiais associados à venda e atualizar o estoque
            $query_material = "INSERT INTO venda_material (id_venda, id_material, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt_material = $banco->prepare($query_material);

            $query_update_estoque = "UPDATE material SET quantidade = quantidade - ? WHERE id_material = ?";
            $stmt_update_estoque = $banco->prepare($query_update_estoque);

            foreach ($materiais as $index => $id_material) {
                $quantidade = $quantidades[$index];
                $preco = $precos[$index];
                $subtotal = $quantidade * $preco;

                // Inserir na tabela venda_material
                $stmt_material->bind_param("iiidd", $id_venda, $id_material, $quantidade, $preco, $subtotal);
                $stmt_material->execute();

                // Atualizar o estoque
                $stmt_update_estoque->bind_param("ii", $quantidade, $id_material);
                $stmt_update_estoque->execute();
            }

            // Commit da transação
            $banco->commit();
            $_SESSION['statusMessage'] = "Venda adicionada com sucesso!";
            $_SESSION['statusType'] = "success";
        } else {
            // Rollback da transação em caso de estoque insuficiente
            $banco->rollback();
            $_SESSION['statusMessage'] = "Erro: Quantidade insuficiente em estoque para um ou mais materiais.";
            $_SESSION['statusType'] = "danger";
        }
    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        $banco->rollback();
        $_SESSION['statusMessage'] = "Erro ao adicionar venda: " . $e->getMessage();
        $_SESSION['statusType'] = "danger";
    }

    header("Location: adicionar_venda.php");
    exit();
}
?>

<?php 
$currentPage = 'adicionar_venda';
require_once "includes/templates/header.php";
?>

<div class="container mt-5 mb-5">
    <h2>Adicionar Venda</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="data" class="form-label">Data</label>
            <input type="date" class="form-control" id="data" name="data" required>
        </div>
        <div id="materiais-container">
            <div class="row align-items-end material-item">
                <div class="col-md-4 mb-3">
                    <label for="materiais" class="form-label">Material</label>
                    <select class="form-select material-select" name="materiais[]" required>
                        <option value="">Selecione um material</option>
                        <?php
                            $query_materiais = "SELECT id_material, nome, preco FROM material";
                            $res_materiais = $banco->query($query_materiais);

                            while ($material = $res_materiais->fetch_assoc()) {
                                echo "<option value='{$material['id_material']}' data-preco='{$material['preco']}'>{$material['nome']}</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="quantidades" class="form-label">Quantidade</label>
                    <input type="number" class="form-control quantidade-input" name="quantidades[]" step="1" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="precos" class="form-label">Preço Unitário</label>
                    <input type="number" class="form-control preco-input" name="precos[]" step="0.01" readonly required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="subtotais" class="form-label">Subtotal</label>
                    <input type="number" class="form-control subtotal-input" step="0.01" readonly>
                </div>
                <div class="col-md-2 d-flex align-items-end mb-3">
                    <button type="button" class="btn btn-responsive btn-danger btn-remove-material">Remover</button>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-responsive btn-success" id="btn-add-material">Adicionar Material</button>
        </div>
        <div class="mb-5">
            <label for="total" class="form-label">Total</label>
            <input type="number" class="form-control" id="total" name="total" step="0.01" readonly required>
        </div>
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-responsive btn-primary">Salvar Venda</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statusModalLabel">Status da Operação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?= $statusMessage ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-<?= $statusType ?>" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($statusMessage)): ?>
            var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
            statusModal.show();
        <?php endif; ?>

        document.getElementById('btn-add-material').addEventListener('click', function() {
            var container = document.getElementById('materiais-container');
            var template = document.getElementById('material-template').cloneNode(true);
            template.style.display = 'flex';
            template.removeAttribute('id');
            container.appendChild(template);
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btn-remove-material')) {
                e.target.closest('.material-item').remove();
                updateTotal();
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('material-select')) {
                var selectedOption = e.target.options[e.target.selectedIndex];
                var preco = selectedOption.getAttribute('data-preco');
                var quantidadeInput = e.target.closest('.material-item').querySelector('.quantidade-input');
                var precoInput = e.target.closest('.material-item').querySelector('.preco-input');
                precoInput.value = preco;
                quantidadeInput.dispatchEvent(new Event('input'));
            }
        });

        document.addEventListener('input', function(e) {
            if (e.target && e.target.classList.contains('quantidade-input')) {
                var quantidade = e.target.value;
                var precoInput = e.target.closest('.material-item').querySelector('.preco-input');
                var subtotalInput = e.target.closest('.material-item').querySelector('.subtotal-input');
                var preco = precoInput.value;
                var subtotal = quantidade * preco;
                subtotalInput.value = subtotal.toFixed(2);
                updateTotal();
            }
        });

        function updateTotal() {
            var total = 0;
            document.querySelectorAll('.subtotal-input').forEach(function(subtotalInput) {
                total += parseFloat(subtotalInput.value) || 0;
            });
            document.getElementById('total').value = total.toFixed(2);
        }
    });
</script>

<!-- Template Oculto -->
<div id="material-template" class="row material-item" style="display: none;">
    <div class="col-md-4 mb-3">
        <label for="materiais" class="form-label">Material</label>
        <select class="form-select material-select" name="materiais[]" required>
            <option value="">Selecione um material</option>
            <?php
                $query_materiais = "SELECT id_material, nome, preco FROM material";
                $res_materiais = $banco->query($query_materiais);

                while ($material = $res_materiais->fetch_assoc()) {
                    echo "<option value='{$material['id_material']}' data-preco='{$material['preco']}'>{$material['nome']}</option>";
                }
            ?>
        </select>
    </div>
    <div class="col-md-2 mb-3">
        <label for="quantidades" class="form-label">Quantidade</label>
        <input type="number" class="form-control quantidade-input" name="quantidades[]" step="1" required>
    </div>
    <div class="col-md-2 mb-3">
        <label for="precos" class="form-label">Preço Unitário</label>
        <input type="number" class="form-control preco-input" name="precos[]" step="0.01" readonly required>
    </div>
    <div class="col-md-2 mb-3">
        <label for="subtotais" class="form-label">Subtotal</label>
        <input type="number" class="form-control subtotal-input" step="0.01" readonly>
    </div>
    <div class="col-md-2 d-flex align-items-end mb-3">
        <button type="button" class="btn btn-responsive btn-danger btn-remove-material">Remover</button>
    </div>
</div>

<?php include_once "includes/templates/footer.php"; ?>
