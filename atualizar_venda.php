<?php
session_start();
if (empty($_SESSION)) {
    header("Location: index.php");
    exit();
}
include_once "includes/config/banco.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_venda = $_POST["id_venda"];
    $data = $_POST["data"];
    $total = $_POST["total"];
    $materiais = $_POST["materiais"];
    $quantidades = $_POST["quantidades"];
    $precos = $_POST["precos"];
    $user = $_SESSION["usuario"];

    // Inicia uma transação
    $banco->begin_transaction();
    try {
        // Atualizar a venda
        $query_venda = "UPDATE venda SET data = ?, total = ?, usuario = ? WHERE id_venda = ?";
        $stmt = $banco->prepare($query_venda);
        $stmt->bind_param("sdsi", $data, $total, $user, $id_venda);
        $stmt->execute();

        // Remover os materiais antigos associados à venda
        $query_remove_material = "DELETE FROM venda_material WHERE id_venda = ?";
        $stmt_remove = $banco->prepare($query_remove_material);
        $stmt_remove->bind_param("i", $id_venda);
        $stmt_remove->execute();

        // Inserir os novos materiais associados à venda e atualizar o estoque
        $query_material = "INSERT INTO venda_material (id_venda, id_material, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt_material = $banco->prepare($query_material);

        $query_update_estoque = "UPDATE material SET quantidade = quantidade - ? WHERE id_material = ?";
        $stmt_update_estoque = $banco->prepare($query_update_estoque);

        foreach ($materiais as $index => $id_material) {
            $quantidade = $quantidades[$index];
            $preco = $precos[$index];
            $subtotal = $quantidade * $preco;

            // Inserir na tabela venda_material
            $stmt_material->bind_param("iiidd", $id_venda, $id_material, $quantidade, $preco, $subtotal);
            $stmt_material->execute();

            // Atualizar o estoque
            $stmt_update_estoque->bind_param("ii", $quantidade, $id_material);
            $stmt_update_estoque->execute();
        }

        // Commit da transação
        $banco->commit();
        header("Location: vendas.php?success=1");
    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        $banco->rollback();
        echo "Erro: " . $e->getMessage();
    }
}
?>
