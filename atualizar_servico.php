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
    $id_servico = $_POST["id_servico"];
    $nome_servico = $_POST["nome_servico"];
    $descricao_servico = $_POST["descricao_servico"];
    $data_inicio = $_POST["data_inicio"];
    $data_fim = $_POST["data_fim"];
    $valor_mao_obra = $_POST["valor_mao_obra"];
    $total = $_POST["total"];
    $materiais = isset($_POST["materiais"]) ? $_POST["materiais"] : [];
    $quantidades = isset($_POST["quantidades"]) ? $_POST["quantidades"] : [];
    $precos = isset($_POST["precos"]) ? $_POST["precos"] : [];
    $user = $_SESSION["usuario"];

    $banco->begin_transaction();
    try {
        // Restaurar os materiais antigos ao estoque
        $query_get_old_materials = "SELECT id_material, quantidade FROM servico_material WHERE id_servico = ?";
        $stmt_get_old_materials = $banco->prepare($query_get_old_materials);
        $stmt_get_old_materials->bind_param("i", $id_servico);
        $stmt_get_old_materials->execute();
        $res_old_materials = $stmt_get_old_materials->get_result();

        while ($old_material = $res_old_materials->fetch_assoc()) {
            $query_restore_estoque = "UPDATE material SET quantidade = quantidade + ? WHERE id_material = ?";
            $stmt_restore_estoque = $banco->prepare($query_restore_estoque);
            $stmt_restore_estoque->bind_param("ii", $old_material['quantidade'], $old_material['id_material']);
            $stmt_restore_estoque->execute();
        }

        $estoqueSuficiente = true;

        // Verificar se há quantidade suficiente de cada material no estoque
        foreach ($materiais as $index => $id_material) {
            $quantidade = $quantidades[$index];
            $query_verifica_estoque = "SELECT quantidade FROM material WHERE id_material = ?";
            $stmt_verifica_estoque = $banco->prepare($query_verifica_estoque);
            $stmt_verifica_estoque->bind_param("i", $id_material);
            $stmt_verifica_estoque->execute();
            $stmt_verifica_estoque->bind_result($quantidade_estoque);
            $stmt_verifica_estoque->fetch();
            $stmt_verifica_estoque->close();

            if ($quantidade > $quantidade_estoque) {
                $estoqueSuficiente = false;
                break;
            }
        }

        if ($estoqueSuficiente) {
            // Atualizar o serviço
            $query_servico = "UPDATE servico SET nome = ?, descricao = ?, data_inicio = ?, data_fim = ?, total = ? WHERE id_servico = ?";
            $stmt = $banco->prepare($query_servico);
            $stmt->bind_param("ssssdi", $nome_servico, $descricao_servico, $data_inicio, $data_fim, $total, $id_servico);
            $stmt->execute();

            // Deletar os materiais antigos
            $query_delete_material = "DELETE FROM servico_material WHERE id_servico = ?";
            $stmt_delete_material = $banco->prepare($query_delete_material);
            $stmt_delete_material->bind_param("i", $id_servico);
            $stmt_delete_material->execute();

            if (!empty($materiais)) {
                // Inserir os novos materiais associados ao serviço e atualizar o estoque
                $query_material = "INSERT INTO servico_material (id_servico, id_material, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
                $stmt_material = $banco->prepare($query_material);

                $query_update_estoque = "UPDATE material SET quantidade = quantidade - ? WHERE id_material = ?";
                $stmt_update_estoque = $banco->prepare($query_update_estoque);

                foreach ($materiais as $index => $id_material) {
                    $quantidade = $quantidades[$index];
                    $preco = $precos[$index];
                    $subtotal = $quantidade * $preco;

                    // Inserir na tabela servico_material
                    $stmt_material->bind_param("iiidd", $id_servico, $id_material, $quantidade, $preco, $subtotal);
                    $stmt_material->execute();

                    // Atualizar o estoque
                    $stmt_update_estoque->bind_param("ii", $quantidade, $id_material);
                    $stmt_update_estoque->execute();
                }
            }

            // Commit da transação
            $banco->commit();
            $_SESSION['statusMessage'] = "Serviço atualizado com sucesso!";
            $_SESSION['statusType'] = "success";
        } else {
            // Rollback da transação em caso de estoque insuficiente
            $banco->rollback();
            $_SESSION['statusMessage'] = "Erro: Quantidade insuficiente em estoque para um ou mais materiais.";
            $_SESSION['statusType'] = "danger";
        }
    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        $banco->rollback();
        $_SESSION['statusMessage'] = "Erro ao atualizar serviço: " . $e->getMessage();
        $_SESSION['statusType'] = "danger";
    }

    header("Location: alterar_servico.php?id_servico=" . $id_servico);
    exit();
}
?>
