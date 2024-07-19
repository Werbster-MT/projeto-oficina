<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION) || $_SESSION['tipo'] != 'admin') {
    header("Location: index.php");
    exit();
}
include_once "includes/config/banco.php";
include_once "includes/valida-login.php";

$statusMessage = "";
$statusType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $nome = $_POST["nome"];
    $senha = gerarHash($_POST["senha"]);
    $tipo = $_POST["tipo"];

    $query = "INSERT INTO usuario (usuario, nome, senha, tipo) VALUES (?, ?, ?, ?)";
    $stmt = $banco->prepare($query);
    $stmt->bind_param("ssss", $usuario, $nome, $senha, $tipo);
    if ($stmt->execute()) {
        $statusMessage = "Usuário cadastrado com sucesso!";
        $statusType = "success";
    } else {
        $statusMessage = "Erro ao cadastrar usuário: " . $stmt->error;
        $statusType = "danger";
    }
}
?>

<?php 
$currentPage = 'cadastrar_usuario';
require_once "includes/templates/header.php";
?>

<div class="container mt-5 mb-5">
    <h2>Cadastrar Novo Usuário</h2>
    <form method="POST" action="cadastrar_usuario.php">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select class="form-select" id="tipo" name="tipo" required>
                <option value="admin">Admin</option>
                <option value="vendedor">Vendedor</option>
                <option value="mecanico">Mecanico</option>
                <option value="almoxarifado">Almoxarifado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Cadastrar Usuário</button>
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
