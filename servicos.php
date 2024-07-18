<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['tipo'])) {
    header("Location: index.php");
    exit();
} else {
    $tipo = $_SESSION['tipo'];
}

$currentPage = 'servicos';
require_once "includes/templates/header.php";
include_once "includes/config/banco.php";

$user = $_SESSION["usuario"];

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
                    sm.subtotal
                FROM
                    servico s
                LEFT JOIN servico_material sm ON s.id_servico = sm.id_servico
                LEFT JOIN material m ON sm.id_material = m.id_material
                JOIN usuario u ON s.usuario = u.usuario
                WHERE
                    u.usuario = ?";

$stmt = $banco->prepare($query_servicos);
$bind_types = 's';
$bind_values = [$user];


$stmt->bind_param($bind_types, ...$bind_values);
$stmt->execute();
$res = $stmt->get_result();
?>

<div class="container mt-5 mb-5">
    <table id="servicosTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID Serviço</th>
                <th>Nome do Serviço</th>
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
                <tr>
                    <td colspan="11" class="text-center">Nenhum registro encontrado</td>
                </tr>
            <?php else: ?>
                <?php while ($row = $res->fetch_object()): ?>
                    <tr>
                        <td><?= $row->id_servico ?></td>
                        <td><?= $row->nome_servico ?></td>
                        <td><?= $row->descricao_servico ?></td>
                        <td><?= $row->data_inicio ?></td>
                        <td><?= $row->data_fim ?></td>
                        <td><?= $row->nome_material ?></td>
                        <td><?= $row->quantidade ?></td>
                        <td>R$<?= number_format($row->preco_unitario, 2, ',', '.') ?></td>
                        <td>R$<?= number_format($row->subtotal, 2, ',', '.') ?></td>
                        <td>R$<?= number_format($row->total, 2, ',', '.') ?></td>
                        <td>
                            <a href="alterar_servico.php?id_servico=<?= $row->id_servico ?>" class="btn btn-dark">Editar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#servicosTable').DataTable({
        "language": {
            "url": "assets/js/pt-BR.json"
        },
        "order": [[0, "asc"]], // Define a coluna padrão de ordenação (neste caso, a primeira coluna, ID Serviço)
        "columns": [
            { "orderable": true }, // ID Serviço
            { "orderable": true }, // Nome do Serviço
            { "orderable": true }, // Descrição do Serviço
            { "orderable": true }, // Data Início
            { "orderable": true }, // Data Fim
            { "orderable": true }, // Nome do Material
            { "orderable": true }, // Quantidade
            { "orderable": true }, // Preço Unitário
            { "orderable": true }, // Subtotal
            { "orderable": true }, // Total
            { "orderable": false } // Ações (Desabilita ordenação nesta coluna)
        ]
    });
});
</script>

<?php require_once "includes/templates/footer.php"; ?>