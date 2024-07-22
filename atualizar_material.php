<?php
// Inicia a sessão se ainda não tiver sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a sessão está vazia, redireciona para index.php se estiver
if (empty($_SESSION)) {
    header("Location: index.php");
    exit();
}

// Inclui o arquivo de configuração do banco de dados
include_once "includes/config/banco.php";

// Verifica se a solicitação é do tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $id_material = $_POST['id_material'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['preco'];

    $quantidade = $quantidade >= 0 ? $quantidade : 0; 

    // Prepara a query de atualização dos dados do material
    $query_update = "UPDATE material 
                     SET nome = ?, descricao = ?, quantidade = ?, preco = ? 
                     WHERE id_material = ?";
    $stmt = $banco->prepare($query_update);
    $stmt->bind_param('ssidi', $nome, $descricao, $quantidade, $preco, $id_material);

    // Executa a query e define a mensagem de status com base no resultado
    if ($stmt->execute()) {
        $_SESSION['statusMessage'] = "Material atualizado com sucesso!";
        $_SESSION['statusType'] = "success";
    } else {
        $_SESSION['statusMessage'] = "Erro ao atualizar material: " . $stmt->error;
        $_SESSION['statusType'] = "danger";
    }

    // Redireciona para a página de alteração do material após a tentativa de atualização
    header("Location: alterar_material.php?id_material=$id_material");
    exit();
}
?>