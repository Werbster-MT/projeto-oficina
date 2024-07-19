<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION)) {
    header("Location: index.php");
    exit();
}
$currentPage = "alterar_venda";
include_once "includes/config/banco.php";

$id_venda = $_GET['id_venda'] ?? null;

if (!$id_venda) {
    header("Location: vendas.php");
    exit();
}

$query_venda = "SELECT
                    v.id_venda,
                    v.data,
                    v.total,
                    u.nome as nome_usuario
                FROM
                    venda v
                JOIN usuario u ON v.usuario = u.usuario
                WHERE v.id_venda = ?";
$stmt = $banco->prepare($query_venda);
$stmt->bind_param('i', $id_venda);
$stmt->execute();
$res = $stmt->get_result();
$venda = $res->fetch_object();

// Query para obter os materiais da venda
$query_materiais = "SELECT
                        vm.id_material,
                        m.nome,
                        vm.quantidade,
                        vm.preco_unitario,
                        vm.subtotal
                    FROM
                        venda_material vm
                    JOIN material m ON vm.id_material = m.id_material
                    WHERE vm.id_venda = ?";
$stmt = $banco->prepare($query_materiais);
$stmt->bind_param('i', $id_venda);
$stmt->execute();
$materiais = $stmt->get_result();

$statusMessage = isset($_SESSION['statusMessage']) ? $_SESSION['statusMessage'] : '';
$statusType = isset($_SESSION['statusType']) ? $_SESSION['statusType'] : '';
unset($_SESSION['statusMessage'], $_SESSION['statusType']);

require_once "includes/templates/header.php";
?>

<main class="container mt-5 mb-5">
    <h2>Editar Venda</h2>
    <form method="POST" action="atualizar_venda.php">
        <input type="hidden" name="id_venda" value="<?= $venda->id_venda ?>">
        <div class="mb-3">
            <label for="data" class="form-label">Data da Venda</label>
            <input type="datetime-local" class="form-control" id="data" name="data" value="<?= date('Y-m-d\TH:i', strtotime($venda->data)) ?>" required>
        </div>
        <div class="mb-3">
            <label for="total" class="form-label">Total</label>
            <input type="number" class="form-control" id="total" name="total" step="0.01" value="<?= $venda->total ?>" readonly required>
        </div>
        <div id="materiais-container">
            <?php while ($material = $materiais->fetch_assoc()): ?>
                <div class="row mb-3 material-item">
                    <div class="col-md-4">
                        <label>Materiais</label>
                        <input type="hidden" name="materiais[]" value="<?= $material['id_material'] ?>">
                        <input type="text" class="form-control" value="<?= $material['nome'] ?>" disabled>
                    </div>
                    <div class="col-md-2">
                        <label for="quantidades">Quantidade</label>
                        <input type="number" class="form-control quantidade-input" name="quantidades[]" value="<?= $material['quantidade'] ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label for="precos">Preço Unitário</label>
                        <input type="number" class="form-control preco-input" name="precos[]" value="<?= $material['preco_unitario'] ?>" step="0.01" readonly required>
                    </div>
                    <div class="col-md-2">
                        <label for="subtotais" class="form-label">Subtotal</label>
                        <input type="number" class="form-control subtotal-input" value="<?= $material['subtotal'] ?>" step="0.01" readonly>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-remove-material">Remover</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <button type="button" class="btn btn-primary" id="btn-add-material">Adicionar Material</button>
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</main>

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
<div id="material-template" class="row mb-3 material-item" style="display: none;">
    <div class="col-md-4">
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
    <div class="col-md-2">
        <label for="quantidades" class="form-label">Quantidade</label>
        <input type="number" class="form-control quantidade-input" name="quantidades[]" step="1" required>
    </div>
    <div class="col-md-2">
        <label for="precos" class="form-label">Preço Unitário</label>
        <input type="number" class="form-control preco-input" name="precos[]" step="0.01" readonly required>
    </div>
    <div class="col-md-2">
        <label for="subtotais" class="form-label">Subtotal</label>
        <input type="number" class="form-control subtotal-input" step="0.01" readonly>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="button" class="btn btn-danger btn-remove-material">Remover</button>
    </div>
</div>

<?php include_once "includes/templates/footer.php"; ?>