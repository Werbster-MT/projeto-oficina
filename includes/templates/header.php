<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$currentPage?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <!-- Data Table -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
<header>
    <!-- Header nav menu -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark bg- p-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Oficina Auto</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                <ul class="navbar-nav">
                    <?php 
                        include 'menu.php';
                        echo renderMenu($_SESSION["tipo"], $currentPage); 
                    ?>
                </ul>
                <div class="navbar-nav">
                    <span class="nav-item">
                        <a href="alterar_usuario.php" class="nav-link">Meus Dados</a>
                    </span>
                    <span class="nav-item">
                        <a class="nav-link" href="logout.php">Sair</a>
                    </span>
                </div>
            </div>
        </div>
    </nav>
</header>
