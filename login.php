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

$sql = "SELECT * FROM usuario WHERE usuario = ?";
$stmt = $banco->prepare($sql);
$stmt->bind_param('s', $usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $row = $res->fetch_object();

    if (testarHash($senha, $row->senha)) {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['nome'] = $row->nome;
        $_SESSION['tipo'] = $row->tipo;

        switch ($_SESSION['tipo']) {
            case "vendedor":
                header('Location: vendas.php');
                break;
            case "almoxarifado":
                header('Location: materiais.php');
                break;
            case "mecanico":
                header('Location: servicos.php');
                break;
            case "admin":
                header('Location: dashboard.php');
                break;
            default:
                header('Location: index.php?error=invalid_type');
                break;
        }
        exit();
    } else {
        header('Location: index.php?error=incorrect_password');
        exit();
    }
} else {
    header('Location: index.php?error=user_not_found');
    exit();
}
?>
