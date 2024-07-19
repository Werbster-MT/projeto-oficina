<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION)) {
    header("Location: index.php");
    exit();
}
include_once "includes/config/banco.php";
include_once "includes/valida-login.php";

$id_usuario = $_SESSION["usuario"] ?? null;

if (!$id_usuario) {
    header("Location: dashboard.php");
    exit();
}

$query_usuario = "SELECT usuario, nome FROM usuario WHERE usuario = ?";
$stmt = $banco->prepare($query_usuario);
$stmt->bind_param('s', $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();

$statusMessage = "";
$statusType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $senha = gerarHash($_POST["senha"], PASSWORD_DEFAULT);

    $query_update = "UPDATE usuario SET nome = ?, senha = ? WHERE usuario = ?";
    $stmt = $banco->prepare($query_update);
    $stmt->bind_param("sss", $nome, $senha, $id_usuario);
    if ($stmt->execute()) {
        $statusMessage = "Dados do usuário atualizados com sucesso!";
        $statusType = "success";
    } else {
        $statusMessage = "Erro ao atualizar os dados do usuário: " . $stmt->error;
        $statusType = "danger";
    }
}
?>

<?php 
$currentPage = 'alterar_usuario';
require_once "includes/templates/header.php";
?>

<div class="container mt-5 mb-5">
    <h2>Alterar Dados do Usuário</h2>
    <form method="POST" action="alterar_usuario.php?usuario=<?= $id_usuario ?>">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="usuario" name="usuario" value="<?= $usuario['usuario'] ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?= $usuario['nome'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Nova Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
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

<?php include_once "includes/templates/footer.php"; ?>