<?php
// Inicia a sessão se ainda não tiver sido iniciada
session_start();

// Inclui o arquivo de configuração do banco de dados e o de validação de login
require "includes/config/banco.php";
require "includes/valida-login.php";

// Verifica se o método POST está vazio ou se os campos de usuário e senha estão vazios
if (empty($_POST) || empty($_POST['usuario']) || empty($_POST['senha'])) {
    // Redireciona para a página de login com um parâmetro de erro indicando que os campos estão vazios
    header('Location: index.php?error=empty');
    exit();
}

// Obtém os valores do formulário de login
$usuario = $_POST['usuario'] ?? null;
$senha = $_POST['senha'] ?? null;

// Prepara a consulta SQL para buscar o usuário no banco de dados
$sql = "SELECT * FROM usuario WHERE usuario = ?";
$stmt = $banco->prepare($sql);
$stmt->bind_param('s', $usuario);
$stmt->execute();
$res = $stmt->get_result();

// Verifica se a consulta retornou algum resultado
if ($res->num_rows > 0) {
    // Obtém os dados do usuário
    $row = $res->fetch_object();

    // Verifica se a senha fornecida corresponde ao hash armazenado no banco de dados
    if (testarHash($senha, $row->senha)) {
        // Define as variáveis de sessão com os dados do usuário
        $_SESSION['usuario'] = $usuario;
        $_SESSION['nome'] = $row->nome;
        $_SESSION['tipo'] = $row->tipo;

        // Redireciona o usuário para a página correspondente ao seu tipo de acesso
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
        // Redireciona para a página de login com um parâmetro de erro indicando senha incorreta
        header('Location: index.php?error=incorrect_password');
        exit();
    }
} else {
    // Redireciona para a página de login com um parâmetro de erro indicando usuário não encontrado
    header('Location: index.php?error=user_not_found');
    exit();
}
?>