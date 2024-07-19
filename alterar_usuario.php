<?php
// Inicia a sessão se ainda não tiver sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a sessão está vazia, redireciona para index.php se estiver
if (empty($_SESSION)) {
    header("Location: index.php");
    exit();
}

// Inclui o arquivo de configuração do banco de dados e de validação de login
include_once "includes/config/banco.php";
include_once "includes/valida-login.php";

// Obtém o ID do usuário da sessão
$id_usuario = $_SESSION["usuario"] ?? null;

// Redireciona para o dashboard se o ID do usuário não for encontrado na sessão
if (!$id_usuario) {
    header("Location: dashboard.php");
    exit();
}

// Prepara a query para selecionar os dados do usuário
$query_usuario = "SELECT usuario, nome FROM usuario WHERE usuario = ?";
$stmt = $banco->prepare($query_usuario);
$stmt->bind_param('s', $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();

// Variáveis para mensagens de status
$statusMessage = "";
$statusType = "";

// Verifica se a solicitação é do tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $nome = $_POST["nome"];
    $senha = gerarHash($_POST["senha"], PASSWORD_DEFAULT); // Gera o hash da nova senha

    // Prepara a query de atualização dos dados do usuário
    $query_update = "UPDATE usuario SET nome = ?, senha = ? WHERE usuario = ?";
    $stmt = $banco->prepare($query_update);
    $stmt->bind_param("sss", $nome, $senha, $id_usuario);

    // Executa a query e define a mensagem de status com base no resultado
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
// Define a página atual e inclui o cabeçalho
$currentPage = 'alterar_usuario';
require_once "includes/templates/header.php";
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Alterar Dados do Usuário</h2>
    <form method="POST" action="alterar_usuario.php?usuario=<?= $id_usuario ?>">
        <!-- Campo para o usuário (desabilitado para edição) -->
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="usuario" name="usuario" value="<?= $usuario['usuario'] ?>" disabled>
        </div>
        <!-- Campo para o nome do usuário -->
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?= $usuario['nome'] ?>" required>
        </div>
        <!-- Campo para a nova senha -->
        <div class="mb-3">
            <label for="senha" class="form-label">Nova Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <!-- Botão para salvar as alterações -->
        <div class="row mt-4">
          <div class="col-12">
            <button type="submit" class="btn btn-responsive btn-primary">Salvar Alterações</button>
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
