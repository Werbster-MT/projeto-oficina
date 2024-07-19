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

// Define a página atual
$currentPage = "alterar_material";

// Inclui o arquivo de configuração do banco de dados
include_once "includes/config/banco.php";

// Obtém o ID do material da URL
$id_material = $_GET['id_material'] ?? null;

// Redireciona para a página de materiais se o ID do material não for fornecido
if (!$id_material) {
    header("Location: materiais.php");
    exit();
}

// Prepara a query para selecionar os dados do material
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

// Recupera mensagens de status da sessão, se existirem
$statusMessage = isset($_SESSION['statusMessage']) ? $_SESSION['statusMessage'] : '';
$statusType = isset($_SESSION['statusType']) ? $_SESSION['statusType'] : '';
unset($_SESSION['statusMessage'], $_SESSION['statusType']);

// Inclui o cabeçalho da página
require_once "includes/templates/header.php";
?>

<main class="container mt-5 mb-5">
    <h2>Editar Material</h2>
    <form method="POST" action="atualizar_material.php">
        <!-- Campo oculto para o ID do material -->
        <input type="hidden" name="id_material" value="<?= $material->id_material ?>">
        <!-- Campo para o nome do material -->
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Material</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?= $material->nome ?>" required>
        </div>
        <!-- Campo para a descrição do material -->
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" required><?= $material->descricao ?></textarea>
        </div>
        <!-- Campo para a quantidade do material -->
        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" class="form-control" id="quantidade" name="quantidade" value="<?= $material->quantidade ?>" required>
        </div>
        <!-- Campo para o preço do material -->
        <div class="mb-3">
            <label for="preco" class="form-label">Preço</label>
            <input type="number" step="0.01" class="form-control" id="preco" name="preco" value="<?= $material->preco ?>" required>
        </div>
        <!-- Botão para salvar as alterações -->
        <button type="submit" class="btn btn-responsive btn-primary">Salvar Alterações</button>
    </form>
</main>

<!-- Modal para exibir mensagens de status -->
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
        // Exibe o modal de status se houver uma mensagem de status
        <?php if (!empty($statusMessage)): ?>
            var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
            statusModal.show();
        <?php endif; ?>
    });
</script>

<?php 
// Inclui o rodapé da página
include_once "includes/templates/footer.php"; 
?>