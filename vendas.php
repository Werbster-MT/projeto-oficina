<?php
// Inclui o arquivo de configuração do banco de dados
include_once "includes/config/banco.php";

// Verifica o status da sessão e inicia a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a sessão está vazia. Se estiver, redireciona para a página de login
if (empty($_SESSION)) {
    header("Location: index.php");
    exit();
}

// Define a página atual para fins de navegação ou estilo
$currentPage = "vendas";

// Obtém o nome de usuário e tipo de usuário da sessão
$usuario = $_SESSION["usuario"];
$tipo = $_SESSION["tipo"];

// Consulta para obter vendas e detalhes dos materiais vendidos
if ($tipo == 'admin') {
    // Administrador: visualizar todas as vendas
    $query_vendas = "SELECT 
                        v.id_venda, 
                        v.data, 
                        v.total,
                        vm.quantidade,
                        vm.preco_unitario,
                        vm.subtotal,
                        m.nome AS nome_material,
                        u.nome AS nome_usuario
                    FROM 
                        venda v 
                    INNER JOIN
                        venda_material vm ON v.id_venda = vm.id_venda
                    INNER JOIN
                        material m ON vm.id_material = m.id_material
                    JOIN 
                        usuario u ON v.usuario = u.usuario";
} else {
    // Usuário comum: visualizar apenas as suas vendas
    $query_vendas = "SELECT 
                        v.id_venda, 
                        v.data, 
                        v.total,
                        vm.quantidade,
                        vm.preco_unitario,
                        vm.subtotal,
                        m.nome AS nome_material,
                        u.nome AS nome_usuario
                    FROM 
                        venda v 
                    INNER JOIN
                        venda_material vm ON v.id_venda = vm.id_venda
                    INNER JOIN
                        material m ON vm.id_material = m.id_material
                    JOIN 
                        usuario u ON v.usuario = u.usuario
                    WHERE 
                        u.usuario = ?";
}

// Prepara a consulta SQL
$stmt = $banco->prepare($query_vendas);

// Se o usuário não for administrador, adiciona o parâmetro de usuário na consulta
if ($tipo != 'admin') {
    $stmt->bind_param('s', $usuario);
}

// Executa a consulta
$stmt->execute();
$res = $stmt->get_result();

// Verifica se houve algum erro na execução da consulta
if ($stmt->error) {
    echo "Erro na consulta: " . $stmt->error;
}

// Inclui o cabeçalho da página
require_once "includes/templates/header.php";

// Recupera mensagens de status da URL, se existirem
$statusMessage = isset($_GET['message']) ? $_GET['message'] : '';
$statusType = isset($_GET['status']) ? $_GET['status'] : '';
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Vendas</h2>
    <div class="table-responsive">
        <table id="vendasTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID Venda</th>
                    <th>Data</th>
                    <th>Usuário</th>
                    <th>Material</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                    <th>Total Venda</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop para exibir os dados retornados pela consulta -->
                <?php while ($row = $res->fetch_object()): ?>
                    <tr>
                        <td><?= $row->id_venda ?></td>
                        <td><?= $row->data ?></td>
                        <td><?= $row->nome_usuario ?></td>
                        <td><?= $row->nome_material ?></td>
                        <td><?= $row->quantidade ?></td>
                        <td>R$<?= number_format($row->preco_unitario, 2, ',', '.') ?></td>
                        <td>R$<?= number_format($row->subtotal, 2, ',', '.') ?></td>
                        <td>R$<?= number_format($row->total, 2, ',', '.') ?></td>
                        <td>
                            <a href="alterar_venda.php?id_venda=<?= $row->id_venda ?>" class="btn btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para exibir mensagens de status -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statusModalLabel">Status da Operação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?= htmlspecialchars($statusMessage) ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-<?= htmlspecialchars($statusType) ?>" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>
// Inicializa o DataTable com tradução para o português
$(document).ready(function() {
    $('#vendasTable').DataTable({
        "language": {
            "url": "assets/js/pt-BR.json" // URL para o arquivo de tradução
        }
    });

    // Exibe o modal de status se houver uma mensagem de status
    <?php if (!empty($statusMessage)): ?>
        var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        statusModal.show();
        <?php unset($_SESSION['statusMessage']); unset($_SESSION['statusType']); ?>
    <?php endif; ?>
});
</script>

<!-- Inclui o rodapé da página -->
<?php include_once "includes/templates/footer.php" ?>