?><?php 
    // Adicionar no início de todos os arquivos PHP sensíveis
    session_start();
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    session_start();
    unset($_SESSION['usuario']);
    unset($_SESSION['tipo']);
    session_destroy();
    header("Location: index.php"); 
    exit;
?>