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
        // Restaurar os materiais antigos ao estoque
        $query_materiais_antigos = "SELECT id_material, quantidade FROM venda_material WHERE id_venda = ?";
        $stmt_materiais_antigos = $banco->prepare($query_materiais_antigos);
        $stmt_materiais_antigos->bind_param("i", $id_venda);
        $stmt_materiais_antigos->execute();
        $res_materiais_antigos = $stmt_materiais_antigos->get_result();

        // Atualiza o estoque para adicionar as quantidades antigas
        while ($material_antigo = $res_materiais_antigos->fetch_assoc()) {
            $query_restore_estoque = "UPDATE material SET quantidade = quantidade + ? WHERE id_material = ?";
            $stmt_restore_estoque = $banco->prepare($query_restore_estoque);
            $stmt_restore_estoque->bind_param("ii", $material_antigo['quantidade'], $material_antigo['id_material']);
            $stmt_restore_estoque->execute();
        }

        // Deletar os materiais antigos associados à venda
        $query_delete_material = "DELETE FROM venda_material WHERE id_venda = ?";
        $stmt_delete_material = $banco->prepare($query_delete_material);
        $stmt_delete_material->bind_param("i", $id_venda);
        $stmt_delete_material->execute();

        if ($total == 0) {
            // Deletar a venda se o total for zero
            $query_deleta_venda = "DELETE FROM venda WHERE id_venda = ?";
            $stmt_deleta_venda = $banco->prepare($query_deleta_venda);
            $stmt_deleta_venda->bind_param("i", $id_venda);
            $stmt_deleta_venda->execute();

            // Commit da transação
            $banco->commit();
            $_SESSION['statusMessage'] = "Venda deletada com sucesso!";
            $_SESSION['statusType'] = "success";
            
            // Remover apenas mensagens de status
            unset($_SESSION['statusMessage']);
            unset($_SESSION['statusType']);
            
            // Redireciona para vendas.php com mensagem de venda deletada
            header("Location: vendas.php?status=success&message=Venda deletada com sucesso!");
            exit();
        } else {
            // Verificar se há quantidade suficiente de cada material no estoque
            $estoqueSuficiente = true;
            foreach ($materiais as $index => $id_material) {
                $quantidade = $quantidades[$index];
                $query_verifica_estoque = "SELECT quantidade FROM material WHERE id_material = ?";
                $stmt_verifica_estoque = $banco->prepare($query_verifica_estoque);
                $stmt_verifica_estoque->bind_param("i", $id_material);
                $stmt_verifica_estoque->execute();
                $stmt_verifica_estoque->bind_result($quantidade_estoque);
                $stmt_verifica_estoque->fetch();
                $stmt_verifica_estoque->close();

                // Verifica se a quantidade desejada é maior que a quantidade em estoque
                if ($quantidade > $quantidade_estoque) {
                    $estoqueSuficiente = false;
                    break;
                }
            }

            if ($estoqueSuficiente) {
                // Atualizar os dados da venda
                $query_venda = "UPDATE venda SET data = ?, total = ?, usuario = ? WHERE id_venda = ?";
                $stmt = $banco->prepare($query_venda);
                $stmt->bind_param("sdsi", $data, $total, $user, $id_venda);
                $stmt->execute();

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
                $_SESSION['statusMessage'] = "Venda atualizada com sucesso!";
                $_SESSION['statusType'] = "success";
            } else {
                // Rollback da transação em caso de estoque insuficiente
                $banco->rollback();
                $_SESSION['statusMessage'] = "Erro: Quantidade insuficiente em estoque para um ou mais materiais.";
                $_SESSION['statusType'] = "danger";
            }
        }
    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        $banco->rollback();
        $_SESSION['statusMessage'] = "Erro ao atualizar venda: " . $e->getMessage();
        $_SESSION['statusType'] = "danger";
    }

    // Redireciona para a página de alteração da venda após a tentativa de atualização
    header("Location: alterar_venda.php?id_venda=" . $id_venda);
    exit();
}
?>