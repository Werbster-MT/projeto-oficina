<?php 
    session_start();
    if(empty($_SESSION)) {
        header("Location: index.php"); 
    }
?>
    <!-- Header -->
    <?php 
        include_once "includes/templates/header.php";
    ?>

    <div class="container mt-5 mb-5">
        <h1 class="main-title">Bem vindo, <?=$_SESSION['nome'];?>!</h1>
        <?php 
            switch ($tipo) {
                case "vendedor":
                    break;
                    
                case "almoxarifado":
                    break;

                case "mecanico":
                    include_once "mecanico/servicos.php";
                    break;

                case "admin":
                    break;
            }
        ?>
    </div>

    <!-- Footer -->
    <?php include_once "includes/templates/footer.php"?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>