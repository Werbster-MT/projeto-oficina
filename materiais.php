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
    // Se o tipo de usuário estiver definido, armazena em uma variável
    $tipo = $_SESSION['tipo'];
}

// Define a página atual para fins de navegação ou estilo
$currentPage = 'materiais';

// Inclui o cabeçalho da página
require_once "includes/templates/header.php";

// Inclui a configuração do banco de dados
include_once "includes/config/banco.php";

// Consulta todos os materiais do banco de dados
$query_materiais = "SELECT * FROM material";
$res_materiais = $banco->query($query_materiais);
?>

<main class="container mt-5 mb-5">
    <div class="table-responsive">
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
                        <td>
                            <?php
                            // Verifica se a quantidade do material é zero e define a coluna "Habilitado" como "Não", caso contrário, "Sim"
                            if ($material['quantidade'] == 0) {
                                echo "Não";
                            } else {
                                echo "Sim";
                            }
                            ?>
                        </td>
                        <td>
                            <!-- Link para editar o material, passando o ID do material como parâmetro na URL -->
                            <a href="alterar_material.php?id_material=<?= $material['id_material'] ?>" class="btn btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
// Inicializa o DataTable com tradução para o português
$(document).ready(function() {
    $('#materiaisTable').DataTable({
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