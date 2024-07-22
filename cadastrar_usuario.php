<?php
// Inicia a sessão se ainda não tiver sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a sessão está vazia ou se o usuário não é admin, redireciona para index.php se for o caso
if (empty($_SESSION) || $_SESSION['tipo'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Inclui o arquivo de configuração do banco de dados e o de validação de login
include_once "includes/config/banco.php";
include_once "includes/valida-login.php";

// Variáveis para mensagens de status
$statusMessage = "";
$statusType = "";

// Verifica se a solicitação é do tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $usuario = $_POST["usuario"];
    $nome = $_POST["nome"];
    $senha = gerarHash($_POST["senha"]); // Gera o hash da senha
    $tipo = $_POST["tipo"];

    // Prepara a query de inserção dos dados do usuário
    $query = "INSERT INTO usuario (usuario, nome, senha, tipo) VALUES (?, ?, ?, ?)";
    $stmt = $banco->prepare($query);
    $stmt->bind_param("ssss", $usuario, $nome, $senha, $tipo);

    // Executa a query e define a mensagem de status com base no resultado
    try {
        $stmt->execute();
        $statusMessage = "Usuário cadastrado com sucesso!";
        $statusType = "success";
    } catch (Exception $e) {
        $statusMessage = "Erro ao cadastrar usuário: " . $stmt->error;
        $statusType = "danger";
    }
}
?>

<?php 
// Define a página atual e inclui o cabeçalho
$currentPage = 'cadastrar_usuario';
require_once "includes/templates/header.php";
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Cadastrar Novo Usuário</h2>
    <form method="POST" action="cadastrar_usuario.php">
        <!-- Campo para o nome de usuário -->
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <!-- Campo para o nome completo do usuário -->
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <!-- Campo para a senha do usuário -->
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <!-- Campo para selecionar o tipo de usuário -->
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select class="form-select" id="tipo" name="tipo" required>
                <option value="admin">Admin</option>
                <option value="vendedor">Vendedor</option>
                <option value="mecanico">Mecanico</option>
                <option value="almoxarifado">Almoxarifado</option>
            </select>
        </div>
        <!-- Botão para cadastrar o usuário -->
        <div class="row mt-4">
            <div class="col-12">
                <button type="submit" class="btn btn-responsive btn-primary">Cadastrar Usuário</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal para exibir mensagens de status -->
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
    // Script para exibir o modal de status se houver uma mensagem de status
    <?php if (!empty($statusMessage)): ?>
    var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    statusModal.show();
    <?php endif; ?>
</script>

<?php 
// Inclui o rodapé da página
include_once "includes/templates/footer.php"; 
?>