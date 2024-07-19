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
    $id_servico = $_POST['id_servico'];
    $nome_servico = $_POST['nome_servico'];
    $descricao_servico = $_POST['descricao_servico'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $total = $_POST['total'];

    $query_update = "UPDATE servico 
                     SET nome = ?, descricao = ?, data_inicio = ?, data_fim = ?, total = ? 
                     WHERE id_servico = ?";
    $stmt = $banco->prepare($query_update);
    $stmt->bind_param('ssssdi', $nome_servico, $descricao_servico, $data_inicio, $data_fim, $total, $id_servico);

    if ($stmt->execute()) {
        $_SESSION['statusMessage'] = "Serviço atualizado com sucesso!";
        $_SESSION['statusType'] = "success";
    } else {
        $_SESSION['statusMessage'] = "Erro ao atualizar serviço: " . $stmt->error;
        $_SESSION['statusType'] = "danger";
    }

    header("Location: alterar_servico.php?id_servico=$id_servico");
    exit();
}
?>
