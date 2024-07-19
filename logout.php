<?php 
    // Inicia a sessão. Isso é necessário para acessar e manipular variáveis de sessão.
    session_start();

    // Verifica se a variável de sessão 'usuario' está definida.
    // Se não estiver, redireciona o usuário para a página de login.
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    // Remove a variável de sessão 'usuario'
    unset($_SESSION['usuario']);

    // Remove a variável de sessão 'tipo'
    unset($_SESSION['tipo']);

    // Destrói todas as variáveis de sessão e a própria sessão
    session_destroy();

    // Redireciona o usuário para a página de login após a saída
    header("Location: index.php"); 
    exit;
?>