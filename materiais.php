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

$currentPage = 'materiais';
require_once "includes/templates/header.php";
include_once "includes/config/banco.php";

$query_materiais = "SELECT * FROM material";
$res_materiais = $banco->query($query_materiais);
?>

<main class="container mt-5 mb-5">
    <table id="materiaisTable" class="table table-bordered table-striped">
        <h2 class="mb-4">Materiais</h2>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Quantidade</th>
                <th>Preço</th>
                <th>Habilitado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($material = $res_materiais->fetch_assoc()): ?>
                <tr>
                    <td><?= $material['id_material'] ?></td>
                    <td><?= $material['nome'] ?></td>
                    <td><?= $material['descricao'] ?></td>
                    <td><?= $material['quantidade'] ?></td>
                    <td>R$<?= number_format($material['preco'], 2, ',', '.') ?></td>
                    <td><?php 
                        if ($material['quantidade'] == 0) {
                            echo "Não";
                        } else {
                            echo "Sim";
                        }
                    ?>
                    </td>
                    <td>
                        <a href="alterar_material.php?id_material=<?= $material['id_material'] ?>" class="btn btn-warning">Editar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<script>
$(document).ready(function() {
    $('#materiaisTable').DataTable(
        {"language": {
                "url": "assets/js/pt-BR.json"
            }
        }
    );
});
</script>

<?php require_once "includes/templates/footer.php"; ?>
