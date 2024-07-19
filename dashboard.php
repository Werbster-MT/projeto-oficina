<?php
// Inicia a sessão se ainda não tiver sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a sessão está vazia ou se o usuário não é admin, redireciona para index.php se for o caso
if (empty($_SESSION) || $_SESSION['tipo'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Inclui o arquivo de configuração do banco de dados
include_once "includes/config/banco.php";

// Define a página atual
$currentPage = 'dashboard';

// Define o mês e o ano padrão como o mês e ano atual, ou usa os valores enviados via POST
$mes = isset($_POST['mes']) ? (int)$_POST['mes'] : date('m');
$ano = isset($_POST['ano']) ? $_POST['ano'] : date('Y');

// Array com os nomes dos meses em português
$meses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];

// Consulta para métricas de vendas
$query_vendas = "SELECT COUNT(*) AS total_vendas, SUM(total) AS faturamento_total FROM venda";
$res_vendas = $banco->query($query_vendas);
$vendas = $res_vendas->fetch_assoc();

// Consulta para métricas de serviços
$query_servicos = "SELECT COUNT(*) AS total_servicos, SUM(total) AS faturamento_total_servicos FROM servico";
$res_servicos = $banco->query($query_servicos);
$servicos = $res_servicos->fetch_assoc();

// Consulta para faturamento diário de vendas
$query_faturamento_diario_vendas = "SELECT DATE(data) as data, SUM(total) as faturamento_diario 
                                    FROM venda 
                                    WHERE MONTH(data) = ? AND YEAR(data) = ? 
                                    GROUP BY DATE(data) 
                                    ORDER BY DATE(data)";
$stmt_vendas = $banco->prepare($query_faturamento_diario_vendas);
$stmt_vendas->bind_param('ii', $mes, $ano);
$stmt_vendas->execute();
$res_faturamento_diario_vendas = $stmt_vendas->get_result();

// Consulta para faturamento diário de serviços
$query_faturamento_diario_servicos = "SELECT DATE(data_inicio) as data, SUM(total) as faturamento_diario 
                                      FROM servico 
                                      WHERE MONTH(data_inicio) = ? AND YEAR(data_inicio) = ? 
                                      GROUP BY DATE(data_inicio) 
                                      ORDER BY DATE(data_inicio)";
$stmt_servicos = $banco->prepare($query_faturamento_diario_servicos);
$stmt_servicos->bind_param('ii', $mes, $ano);
$stmt_servicos->execute();
$res_faturamento_diario_servicos = $stmt_servicos->get_result();

// Array para armazenar o faturamento diário de vendas
$faturamento_diario_vendas = [];
while ($row = $res_faturamento_diario_vendas->fetch_assoc()) {
    $faturamento_diario_vendas[] = $row;
}

// Array para armazenar o faturamento diário de serviços
$faturamento_diario_servicos = [];
while ($row = $res_faturamento_diario_servicos->fetch_assoc()) {
    $faturamento_diario_servicos[] = $row;
}

// Inclui o cabeçalho da página
require_once "includes/templates/header.php";
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Dashboard</h2>

    <!-- Formulário para filtrar por mês e ano -->
    <form method="POST" class="mb-4">
        <div class="row d-flex align-items-end mb-4">
            <div class="col-12 col-md-2">
                <label for="mes" class="form-label">Mês</label>
                <select class="form-select" id="mes" name="mes">
                    <?php foreach ($meses as $num => $nome): ?>
                        <option value="<?= $num ?>" <?= $num == $mes ? 'selected' : '' ?>><?= $nome ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label for="ano" class="form-label">Ano</label>
                <select class="form-select" id="ano" name="ano">
                    <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                        <option value="<?= $y ?>" <?= $y == $ano ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-responsive btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Cartões de resumo de vendas e serviços -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Vendas</div>
                <div class="card-body">
                    <h5 class="card-title">Total de Vendas: <?= $vendas['total_vendas'] ?></h5>
                    <p class="card-text">Faturamento Total: R$ <?= number_format($vendas['faturamento_total'], 2, ',', '.') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-header">Serviços</div>
                <div class="card-body">
                    <h5 class="card-title">Total de Serviços: <?= $servicos['total_servicos'] ?></h5>
                    <p class="card-text">Faturamento Total de Serviços: R$ <?= number_format($servicos['faturamento_total_servicos'], 2, ',', '.') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de faturamento diário -->
    <div class="row">
        <div class="col-md-6">
            <h3>Faturamento Diário de Vendas (<?= $meses[(int)$mes] ?> de <?= $ano ?>)</h3>
            <canvas id="vendasChart" width="300" height="150"></canvas>
        </div>
        <div class="col-md-6">
            <h3>Faturamento Diário de Serviços (<?= $meses[(int)$mes] ?> de <?= $ano ?>)</h3>
            <canvas id="servicosChart" width="300" height="150"></canvas>
        </div>
    </div>

    <!-- Resumo do faturamento total do mês -->
    <div class="row mt-5">
        <div class="col-md-6">
            <h4>Faturamento Total do Mês (Vendas)</h4>
            <p>R$ <?= number_format(array_sum(array_column($faturamento_diario_vendas, 'faturamento_diario')), 2, ',', '.') ?></p>
        </div>
        <div class="col-md-6">
            <h4>Faturamento Total do Mês (Serviços)</h4>
            <p>R$ <?= number_format(array_sum(array_column($faturamento_diario_servicos, 'faturamento_diario')), 2, ',', '.') ?></p>
        </div>
    </div>
</div>

<!-- Inclusão do Chart.js para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Gráfico de Faturamento Diário de Vendas
        var ctxVendas = document.getElementById('vendasChart').getContext('2d');
        var vendasChart = new Chart(ctxVendas, {
            type: 'line',
            data: {
                labels: [<?php foreach ($faturamento_diario_vendas as $data) { echo '"' . $data['data'] . '",'; } ?>],
                datasets: [{
                    label: 'Faturamento Diário de Vendas',
                    data: [<?php foreach ($faturamento_diario_vendas as $data) { echo $data['faturamento_diario'] . ','; } ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Faturamento Diário de Serviços
        var ctxServicos = document.getElementById('servicosChart').getContext('2d');
        var servicosChart = new Chart(ctxServicos, {
            type: 'line',
            data: {
                labels: [<?php foreach ($faturamento_diario_servicos as $data) { echo '"' . $data['data'] . '",'; } ?>],
                datasets: [{
                    label: 'Faturamento Diário de Serviços',
                    data: [<?php foreach ($faturamento_diario_servicos as $data) { echo $data['faturamento_diario'] . ','; } ?>],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<?php 
// Inclui o rodapé da página
include_once "includes/templates/footer.php"; 
?>