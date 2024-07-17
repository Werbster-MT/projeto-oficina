<?php 
    if(empty($_SESSION)) {
        header("Location: index.php"); 
    }
    $tipo = $_SESSION['tipo']; 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
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
                                            <a class='nav-link' href='#'>Serviços</a>
                                        </li>
                                    ";
                                    echo "
                                        <li class='nav-item'>
                                            <a class='nav-link' href='#'>Adicionar Serviços</a>
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
