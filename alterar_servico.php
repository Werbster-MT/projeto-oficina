<?php
    session_start();
    if(empty($_SESSION)) {
        header("Location: index.php"); 
    }
    include_once "includes/config/banco.php";

    $id_servico = $_GET['id_servico'];

    $query_servico = "SELECT
                        s.id_servico,
                        s.nome AS nome_servico,
                        s.descricao AS descricao_servico,
                        s.data_inicio,
                        s.data_fim,
                        s.total,
                        m.id_material,
                        m.nome AS nome_material,
                        m.descricao AS descricao_material,
                        sm.quantidade,
                        sm.preco_unitario,
                        sm.subtotal
                    FROM
                        servico s
                    LEFT JOIN servico_material sm ON s.id_servico = sm.id_servico
                    LEFT JOIN material m ON sm.id_material = m.id_material
                    WHERE s.id_servico = ?";

    $stmt = $banco->prepare($query_servico);
    $stmt->bind_param('i', $id_servico);
    $stmt->execute();
    $res = $stmt->get_result();
    $servico = $res->fetch_object();
?>
    <?php 
    $currentPage = 'alterar_servico';
    require_once "includes/templates/header.php";?>

    <main class="container mt-5 mb-5">
        <h2>Editar Serviço</h2>
        <form method="POST" action="atualizar_servico.php">
            <input type="hidden" name="id_servico" value="<?= $servico->id_servico ?>">
            <div class="mb-3">
                <label for="nome_servico" class="form-label">Nome do Serviço</label>
                <input type="text" class="form-control" id="nome_servico" name="nome_servico" value="<?= $servico->nome_servico ?>">
            </div>
            <div class="mb-3">
                <label for="descricao_servico" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao_servico" name="descricao_servico"><?= $servico->descricao_servico ?></textarea>
            </div>
            <div class="mb-3">
                <label for="data_inicio" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= (new DateTime($servico->data_inicio))->format('Y-m-d') ?>">
            </div>
            <div class="mb-3">
                <label for="data_fim" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= (new DateTime($servico->data_fim))->format('Y-m-d') ?>">
            </div>
            <div class="mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="number" step="0.01" class="form-control" id="total" name="total" value="<?= $servico->total ?>" readonly>
            </div>
            <!-- Adicione campos para editar materiais, se necessário -->
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
    </main>
    <?php include_once "includes/templates/footer.php"?>