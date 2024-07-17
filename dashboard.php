<?php 
    session_start();
    if(empty($_SESSION)) {
        header("Location: index.php"); 
    } else {
        $tipo = $_SESSION['tipo'];
    }
?>
<!-- Header -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/0c23645969.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-danger p-3">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Oficina Auto</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                    <ul class="navbar-nav">
                        <?php 
                            switch ($tipo) {
                                case "vendedor":
                                    echo "
                                        <li class='nav-item'>
                                            <a class='nav-link' href='#'>Vendas</a>
                                        </li>
                                    ";                                    
                                    break;
                                case "almoxarifado":
                                    echo "
                                        <li class='nav-item'>
                                            <a class='nav-link' href='#'>Materiais</a>
                                        </li>
                                    ";                                    
                                    break;
                                case "mecanico":
                                    echo "
                                        <li class='nav-item'>
                                            <a class='nav-link active' href='#'>Serviços</a>
                                        </li>
                                    ";
                                    echo "
                                        <li class='nav-item'>
                                            <a class='nav-link' href='adicionar_servico.php'>Adicionar Serviço</a>
                                        </li>
                                    ";                                                
                                    break;
                                case "admin":
                                    echo "
                                        <li class='nav-item'>
                                            <a class='nav-link' href='#'>Vendas</a>
                                        </li>
                                    ";
                                    echo "
                                        <li class='nav-item'>
                                            <a class='nav-link' href='#'>Serviços</a>
                                        </li>
                                    ";
                                    echo "
                                        <li class='nav-item'>
                                            <a class='nav-link' href='#'>Materiais</a>
                                        </li>
                                    ";
                                    echo "
                                        <li class='nav-item'>
                                            <a class='nav-link' href='#'>Cadastrar Usuário</a>
                                        </li>
                                    ";
                            }
                        ?>
                    </ul>
                    <div class="navbar-nav">
                        <span class="nav-item">
                            <a href="" class="nav-link">Meus Dados</a>
                        </span>
                        <span class="nav-item">
                            <a class="nav-link" href="logout.php">Sair</a>
                        </span>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mt-5 mb-5">
        <h1 class="main-title">Bem vindo, <?=$_SESSION['nome'];?>!</h1>
        
        <?php 
            if($tipo == "vendedor") {
                
            }elseif($tipo == "almoxarifado") {

            }elseif($tipo == "mecanico") {
                require_once "servicos.php";
            
            }elseif ($tipo === "admin") {

            }
        ?>
    </div>
    <!-- Footer -->
    <?php require_once "includes/templates/footer.php"?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>