<?php 
    session_start();
    if(empty($_SESSION)) {
        header("Location: index.php"); 
    } else {
        $tipo = $_SESSION['tipo'];
    }

    // Defina a página atual para a navegação correta
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

    // Inclua o header
    require_once "includes/templates/header.php";
?>  

<!-- Main -->
<div class="container mt-5 mb-5">
    <?php 
        if($currentPage === 'servicos' || $currentPage === 'vendas' || $currentPage === 'materiais'){ 
            echo "<h1 class='main-title'>Bem vindo, {$_SESSION['nome']}!</h1>";
        }
    ?>
    
    <?php 
        if($tipo == "vendedor") {
            // Código para vendedor
        } elseif($tipo == "almoxarifado") {
            // Código para almoxarifado
            if($currentPage == "adicionar_material") {
                require_once "adicionar_material.php";
            } else {
                require_once "materiais.php";
            }

        } elseif($tipo == "mecanico") {
            if ($currentPage == 'adicionar_servico') {
                require_once "adicionar_servico.php";
            } else {
                require_once "servicos.php";
            }
        } elseif ($tipo === "admin") {
            // Código para admin
        }
    ?>
</div>

<!-- Footer -->
<?php require_once "includes/templates/footer.php"; ?>
</body>
</html>
