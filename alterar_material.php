<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION)) {
    header("Location: index.php");
    exit();
}
$currentPage = "alterar_material";
include_once "includes/config/banco.php";

$id_material = $_GET['id_material'] ?? null;

if (!$id_material) {
    header("Location: materiais.php");
    exit();
}

$query_material = "SELECT
                        m.id_material,
                        m.nome,
                        m.descricao,
                        m.quantidade,
                        m.preco
                    FROM
                        material m
                    WHERE m.id_material = ?";
$stmt = $banco->prepare($query_material);
$stmt->bind_param('i', $id_material);
$stmt->execute();
$res = $stmt->get_result();
$material = $res->fetch_object();

$statusMessage = isset($_SESSION['statusMessage']) ? $_SESSION['statusMessage'] : '';
$statusType = isset($_SESSION['statusType']) ? $_SESSION['statusType'] : '';
unset($_SESSION['statusMessage'], $_SESSION['statusType']);

require_once "includes/templates/header.php";
?>

<main class="container mt-5 mb-5">
    <h2>Editar Material</h2>
    <form method="POST" action="atualizar_material.php">
        <input type="hidden" name="id_material" value="<?= $material->id_material ?>">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Material</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?= $material->nome ?>" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" required><?= $material->descricao ?></textarea>
        </div>
        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" class="form-control" id="quantidade" name="quantidade" value="<?= $material->quantidade ?>" required>
        </div>
        <div class="mb-3">
            <label for="preco" class="form-label">Preço</label>
            <input type="number" step="0.01" class="form-control" id="preco" name="preco" value="<?= $material->preco ?>" required>
        </div>
        <button type="submit" class="btn btn-responsive btn-primary">Salvar Alterações</button>
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
    });
</script>

<?php include_once "includes/templates/footer.php"; ?>
