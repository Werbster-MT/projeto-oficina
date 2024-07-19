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
    $id_venda = $_POST['id_venda'];
    $data = $_POST['data'];
    $total = $_POST['total'];
    $materiais = $_POST['materiais'];
    $quantidades = $_POST['quantidades'];
    $precos = $_POST['precos'];

    // Inicia uma transação
    $banco->begin_transaction();
    try {
        // Atualizar a venda
        $query_venda = "UPDATE venda SET data = ?, total = ? WHERE id_venda = ?";
        $stmt = $banco->prepare($query_venda);
        $stmt->bind_param("sdi", $data, $total, $id_venda);
        $stmt->execute();

        // Remover materiais antigos
        $query_delete_materiais = "DELETE FROM venda_material WHERE id_venda = ?";
        $stmt_delete = $banco->prepare($query_delete_materiais);
        $stmt_delete->bind_param("i", $id_venda);
        $stmt_delete->execute();

        // Inserir novos materiais
        $query_material = "INSERT INTO venda_material (id_venda, id_material, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt_material = $banco->prepare($query_material);
        
        foreach ($materiais as $index => $id_material) {
            $quantidade = $quantidades[$index];
            $preco = $precos[$index];
            $subtotal = $quantidade * $preco;
            
            $stmt_material->bind_param("iiidd", $id_venda, $id_material, $quantidade, $preco, $subtotal);
            $stmt_material->execute();
        }

        // Commit da transação
        $banco->commit();
        $_SESSION['statusMessage'] = "Venda atualizada com sucesso!";
        $_SESSION['statusType'] = "success";
    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        $banco->rollback();
        $_SESSION['statusMessage'] = "Erro ao atualizar venda: " . $e->getMessage();
        $_SESSION['statusType'] = "danger";
    }

    header("Location: alterar_venda.php?id_venda=$id_venda");
    exit();
}
?>
