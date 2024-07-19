<?php
// Verifica o status da sessão e inicia a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o tipo de usuário está definido na sessão. Se não estiver, redireciona para a página de login
if (empty($_SESSION['tipo'])) {
    header("Location: index.php");
    exit();
} else {
    // Se o tipo de usuário estiver definido, armazena em variáveis
    $tipo = $_SESSION['tipo'];
    $usuario = $_SESSION['usuario'];
}

// Define a página atual para fins de navegação ou estilo
$currentPage = 'servicos';

// Inclui o cabeçalho da página
require_once "includes/templates/header.php";

// Inclui a configuração do banco de dados
include_once "includes/config/banco.php";

// Monta a consulta SQL baseada no tipo de usuário (admin ou comum)
if ($tipo == 'admin') {
    // Administrador: visualizar todos os serviços
    $query_servicos = "SELECT
                        s.id_servico,
                        s.nome AS nome_servico,
                        s.descricao AS descricao_servico,
                        s.data_inicio,
                        s.data_fim,
                        s.total,
                        m.id_material,
                        m.nome AS nome_material,
                        m.descricao AS descricao_material,
                        sm.quantidade,
                        sm.preco_unitario,
                        sm.subtotal,
                        u.nome AS nome_usuario
                    FROM
                        servico s
                    LEFT JOIN servico_material sm ON s.id_servico = sm.id_servico
                    LEFT JOIN material m ON sm.id_material = m.id_material
                    JOIN usuario u ON s.usuario = u.usuario";
} else {
    // Usuário comum: visualizar apenas os seus serviços
    $query_servicos = "SELECT
                        s.id_servico,
                        s.nome AS nome_servico,
                        s.descricao AS descricao_servico,
                        s.data_inicio,
                        s.data_fim,
                        s.total,
                        m.id_material,
                        m.nome AS nome_material,
                        m.descricao AS descricao_material,
                        sm.quantidade,
                        sm.preco_unitario,
                        sm.subtotal,
                        u.nome AS nome_usuario
                    FROM
                        servico s
                    LEFT JOIN servico_material sm ON s.id_servico = sm.id_servico
                    LEFT JOIN material m ON sm.id_material = m.id_material
                    JOIN usuario u ON s.usuario = u.usuario
                    WHERE
                        u.usuario = ?";
}

// Prepara a consulta SQL
$stmt = $banco->prepare($query_servicos);

// Se o usuário não for administrador, adiciona o parâmetro de usuário na consulta
if ($tipo != 'admin') {
    $stmt->bind_param('s', $usuario);
}

// Executa a consulta
$stmt->execute();
$res = $stmt->get_result();
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Serviços</h2>
    <div class="table-responsive">
        <table id="servicosTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Serviço</th>
                    <th>Nome do Serviço</th>
                    <th>Usuário</th>
                    <th>Descrição do Serviço</th>
                    <th>Data Início</th>
                    <th>Data Fim</th>
                    <th>Nome do Material</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res->num_rows == 0): ?>
                    <!-- Caso não haja registros, exibe uma linha com a mensagem "Nenhum registro encontrado" -->
                    <tr>
                        <td colspan="11" class="text-center">Nenhum registro encontrado</td>
                    </tr>
                <?php else: ?>
                    <!-- Loop para exibir os dados retornados pela consulta -->
                    <?php while ($row = $res->fetch_object()): ?>
                        <tr>
                            <td><?= $row->id_servico ?></td>
                            <td><?= $row->nome_servico ?></td>
                            <td><?= $row->nome_usuario?></td>
                            <td><?= $row->descricao_servico ?></td>
                            <td><?= $row->data_inicio ?></td>
                            <td><?= $row->data_fim ?></td>
                            <td><?= $row->nome_material ?></td>
                            <td><?= $row->quantidade ?></td>
                            <td>R$<?= number_format($row->preco_unitario, 2, ',', '.') ?></td>
                            <td>R$<?= number_format($row->subtotal, 2, ',', '.') ?></td>
                            <td>R$<?= number_format($row->total, 2, ',', '.') ?></td>
                            <td>
                                <a href="alterar_servico.php?id_servico=<?= $row->id_servico ?>" class="btn btn-warning">Editar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Inicializa o DataTable com tradução para o português
$(document).ready(function() {
    $('#servicosTable').DataTable({
        "language": {
            "url": "assets/js/pt-BR.json" // URL para o arquivo de tradução
        }
    });
});
</script>

<?php
// Inclui o rodapé da página
require_once "includes/templates/footer.php";
?>
