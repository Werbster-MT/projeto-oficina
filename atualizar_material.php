<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION)) {
    header("Location: index.php");
    exit();
}
include_once "includes/config/banco.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_material = $_POST['id_material'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['preco'];

    $query_update = "UPDATE material 
                     SET nome = ?, descricao = ?, quantidade = ?, preco = ? 
                     WHERE id_material = ?";
    $stmt = $banco->prepare($query_update);
    $stmt->bind_param('ssidi', $nome, $descricao, $quantidade, $preco, $id_material);

    if ($stmt->execute()) {
        $_SESSION['statusMessage'] = "Material atualizado com sucesso!";
        $_SESSION['statusType'] = "success";
    } else {
        $_SESSION['statusMessage'] = "Erro ao atualizar material: " . $stmt->error;
        $_SESSION['statusType'] = "danger";
    }

    header("Location: alterar_material.php?id_material=$id_material");
    exit();
}
?>
