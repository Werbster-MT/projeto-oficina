<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once "includes/config/banco.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $quantidade = $_POST["quantidade"];
    $preco = $_POST["preco"];

    $query = "INSERT INTO material (nome, descricao, quantidade, preco) VALUES (?, ?, ?, ?)";
    $stmt = $banco->prepare($query);
    $stmt->bind_param("ssii", $nome, $descricao, $quantidade, $preco);

    if ($stmt->execute()) {
        $statusMessage = "Material adicionado com sucesso!";
        $statusType = "success";
    } else {
        $statusMessage = "Erro ao adicionar material: " . $stmt->error;
        $statusType = "danger";
    }
}
?>

<?php 
$currentPage = 'adicionar_material';
require_once "includes/templates/header.php"; 
?>

<div class="container mt-5">
    <h2>Adicionar Material</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Material</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" required></textarea>
        </div>
        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" class="form-control" id="quantidade" name="quantidade" required>
        </div>
        <div class="mb-3">
            <label for="preco" class="form-label">Preço Unitário</label>
            <input type="number" class="form-control" id="preco" name="preco" step="0.01" required>
        </div>
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Salvar Material</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
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
    <?php if (!empty($statusMessage)): ?>
    var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    statusModal.show();
    <?php endif; ?>
</script>

<?php require_once "includes/templates/footer.php"; ?>
