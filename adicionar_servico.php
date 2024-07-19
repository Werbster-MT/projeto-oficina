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
    $nome_servico = $_POST["nome_servico"];
    $descricao_servico = $_POST["descricao_servico"];
    $data_inicio = $_POST["data_inicio"];
    $data_fim = $_POST["data_fim"];
    $total = $_POST["total"];
    $materiais = $_POST["materiais"] ?? [];
    $quantidades = $_POST["quantidades"] ?? [];
    $precos = $_POST["precos"] ?? [];
    $user = $_SESSION["usuario"];

    // Inicia uma transação
    $banco->begin_transaction();
    try {
        // Inserir o serviço
        $query_servico = "INSERT INTO servico (nome, descricao, data_inicio, data_fim, total, usuario) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $banco->prepare($query_servico);
        $stmt->bind_param("ssssds", $nome_servico, $descricao_servico, $data_inicio, $data_fim, $total, $user);
        $stmt->execute();

        $id_servico = $banco->insert_id;

        if (!empty($materiais)) {
            // Inserir os materiais associados ao serviço e atualizar o estoque
            $query_material = "INSERT INTO servico_material (id_servico, id_material, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt_material = $banco->prepare($query_material);

            $query_update_estoque = "UPDATE material SET quantidade = quantidade - ? WHERE id_material = ?";
            $stmt_update_estoque = $banco->prepare($query_update_estoque);

            foreach ($materiais as $index => $id_material) {
                $quantidade = $quantidades[$index];
                $preco = $precos[$index];
                $subtotal = $quantidade * $preco;

                // Inserir na tabela servico_material
                $stmt_material->bind_param("iiidd", $id_servico, $id_material, $quantidade, $preco, $subtotal);
                $stmt_material->execute();

                // Atualizar o estoque
                $stmt_update_estoque->bind_param("ii", $quantidade, $id_material);
                $stmt_update_estoque->execute();
            }
        }

        // Commit da transação
        $banco->commit();
        $_SESSION['statusMessage'] = "Serviço adicionado com sucesso!";
        $_SESSION['statusType'] = "success";
    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        $banco->rollback();
        $_SESSION['statusMessage'] = "Erro ao adicionar serviço: " . $e->getMessage();
        $_SESSION['statusType'] = "danger";
    }

    header("Location: adicionar_servico.php");
    exit();
}
?>

<?php 
$currentPage = 'adicionar_servico';
require_once "includes/templates/header.php";
?>

<div class="container mt-5 mb-5">
    <h2>Adicionar Serviço</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nome_servico" class="form-label">Nome do Serviço</label>
            <input type="text" class="form-control" id="nome_servico" name="nome_servico" required>
        </div>
        <div class="mb-3">
            <label for="descricao_servico" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao_servico" name="descricao_servico" required></textarea>
        </div>
        <div class="mb-3">
            <label for="data_inicio" class="form-label">Data Início</label>
            <input type="date" class="form-control" id="data_inicio" name="data_inicio" required>
        </div>
        <div class="mb-3">
            <label for="data_fim" class="form-label">Data Fim</label>
            <input type="date" class="form-control" id="data_fim" name="data_fim" required>
        </div>
        <div class="mb-3">
            <label for="total" class="form-label">Total</label>
            <input type="number" class="form-control" id="total" name="total" step="0.01" required>
        </div>
        <div id="materiais-container">
            <!-- Seção de materiais será adicionada dinamicamente -->
        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="btn-add-material">Adicionar Material</button>
        </div>
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Salvar Serviço</button>
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
    });

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
        }
    });
</script>

<!-- Template Oculto -->
<div id="material-template" class="row mb-3 material-item" style="display: none;">
    <div class="col-md-4">
        <label for="materiais" class="form-label">Material</label>
        <select class="form-select" name="materiais[]">
            <option value="">Selecione um material</option>
            <?php
                $query_materiais = "SELECT id_material, nome FROM material";
                $res_materiais = $banco->query($query_materiais);

                while ($material = $res_materiais->fetch_assoc()) {
                    echo "<option value='{$material['id_material']}'>{$material['nome']}</option>";
                }
            ?>
        </select>
    </div>
    <div class="col-md-3">
        <label for="quantidades" class="form-label">Quantidade</label>
        <input type="number" class="form-control" name="quantidades[]" step="1">
    </div>
    <div class="col-md-3">
        <label for="precos" class="form-label">Preço Unitário</label>
        <input type="number" class="form-control" name="precos[]" step="0.01">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="button" class="btn btn-danger btn-remove-material">Remover</button>
    </div>
</div>

<?php include_once "includes/templates/footer.php"; ?>
