<?php
include_once "includes/config/banco.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(empty($_SESSION)) {
    header("Location: index.php");
    exit();
}

$currentPage = "vendas";
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

$stmt = $banco->prepare($query_vendas);

if ($tipo != 'admin') {
    $stmt->bind_param('s', $usuario);
}

$stmt->execute();
$res = $stmt->get_result();

if ($stmt->error) {
    echo "Erro na consulta: " . $stmt->error;
}

require_once "includes/templates/header.php";
?>
<div class="container mt-5 mb-5">
    <h2 class="mb-4">Vendas</h2>
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

<script>
    $(document).ready(function() {
        $('#vendasTable').DataTable({
            "language": {
                "url": "assets/js/pt-BR.json"
            }
        });
    });
</script>

<!-- Footer -->
<?php include_once "includes/templates/footer.php"?>
