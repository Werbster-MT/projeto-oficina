<?php
    session_start();
    require "includes/config/banco.php";
    require "includes/valida-login.php";

    if (empty($_POST) || empty($_POST['usuario']) || empty($_POST['senha'])) {
        header('Location: index.php?error=empty');
        exit();
    }

    $usuario = $_POST['usuario'] ?? null;
    $senha = $_POST['senha'] ?? null;

    $sql = "SELECT * FROM usuario WHERE nome = ?";
    $stmt = $banco->prepare($sql);
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_object();
        if (testarHash($senha, $row->senha)) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['tipo'] = $row->tipo;
            header('Location: dashboard.php');
        } else {
            header('Location: index.php?error=incorrect_password');
        }
    } else {
        header('Location: index.php?error=user_not_found');
    }
    exit();
?>