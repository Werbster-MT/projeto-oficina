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

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/0c23645969.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-danger p-3">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Oficina Auto</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class='nav-item'>
                            <a class='nav-link' href='dashboard.php'>Serviços</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='adicionar_servico.php'>Adicionar Serviço</a>
                        </li>
                        <li class='nav-item'>
                            <a href="#" class="nav-link active">Alterar Serviço</a>
                        </li>
                    </ul>
                    <div class="navbar-nav">
                        <span class="nav-item">
                            <a href="" class="nav-link">Meus Dados</a>
                        </span>
                        <span class="nav-item">
                            <a class="nav-link" href="logout.php">Sair</a>
                        </span>
                    </div>
                </div>
            </div>
        </nav>
    </header>
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
</body>
</html>