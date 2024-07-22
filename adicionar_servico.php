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

// Recupera mensagens de status da sessão, se existirem
$statusMessage = isset($_SESSION['statusMessage']) ? $_SESSION['statusMessage'] : '';
$statusType = isset($_SESSION['statusType']) ? $_SESSION['statusType'] : '';
unset($_SESSION['statusMessage'], $_SESSION['statusType']);

// Verifica se a solicitação é do tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
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

    // Inicia uma transação
    $banco->begin_transaction();
    try {
        $estoqueSuficiente = true;

        // Verificar se há quantidade suficiente de cada material no estoque
        foreach ($materiais as $index => $id_material) {
            if (empty($id_material) || empty($quantidades[$index])) { 
                continue;
            }
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
            // Inserir o serviço na tabela `servico`
            $query_servico = "INSERT INTO servico (nome, descricao, data_inicio, data_fim, total, usuario) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $banco->prepare($query_servico);
            $stmt->bind_param("ssssds", $nome_servico, $descricao_servico, $data_inicio, $data_fim, $total, $user);
            $stmt->execute();

            $id_servico = $banco->insert_id;

            if (!empty($materiais)) {
                // Inserir os materiais associados ao serviço e atualizar o estoque
                $query_material = "INSERT INTO servico_material (id_servico, id_material, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
                $stmt_material = $banco->prepare($query_material);

                $query_update_estoque = "UPDATE material SET quantidade = quantidade - ? WHERE id_material = ?";
                $stmt_update_estoque = $banco->prepare($query_update_estoque);

                foreach ($materiais as $index => $id_material) {
                    if (empty($id_material) || empty($quantidades[$index]) || empty($precos[$index])) {
                        continue;
                    }
                    $quantidade = $quantidades[$index];
                    $preco = $precos[$index];
                    $subtotal = $quantidade * $preco;

                    // Inserir na tabela `servico_material`
                    $stmt_material->bind_param("iiidd", $id_servico, $id_material, $quantidade, $preco, $subtotal);
                    $stmt_material->execute();

                    // Atualizar o estoque
                    $stmt_update_estoque->bind_param("ii", $quantidade, $id_material);
                    $stmt_update_estoque->execute();
                }
            }

            // Commit da transação
            $banco->commit();
            $_SESSION['statusMessage'] = "Serviço adicionado com sucesso!";
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
        $_SESSION['statusMessage'] = "Erro ao adicionar serviço: " . $e->getMessage();
        $_SESSION['statusType'] = "danger";
    }

    header("Location: adicionar_servico.php");
    exit();
}
?>

<?php 
// Define a página atual e inclui o cabeçalho
$currentPage = 'adicionar_servico';
require_once "includes/templates/header.php";
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Adicionar Serviço</h2>
    <form method="POST">
        <!-- Campo para o nome do serviço -->
        <div class="mb-3">
            <label for="nome_servico" class="form-label">Nome do Serviço</label>
            <input type="text" class="form-control" id="nome_servico" name="nome_servico" required>
        </div>
        <!-- Campo para a descrição do serviço -->
        <div class="mb-3">
            <label for="descricao_servico" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao_servico" name="descricao_servico" required></textarea>
        </div>
        <!-- Campo para a data de início do serviço -->
        <div class="mb-3">
            <label for="data_inicio" class="form-label">Data Início</label>
            <input type="date" class="form-control" id="data_inicio" name="data_inicio" required>
        </div>
        <!-- Campo para a data de término do serviço -->
        <div class="mb-3">
            <label for="data_fim" class="form-label">Data Fim</label>
            <input type="date" class="form-control" id="data_fim" name="data_fim" required>
        </div>
        <!-- Campo para o valor da mão de obra -->
        <div class="mb-3">
            <label for="valor_mao_obra" class="form-label">Valor da Mão de Obra</label>
            <input type="number" class="form-control" id="valor_mao_obra" name="valor_mao_obra" step="0.01" required>
        </div>
        <!-- Contêiner para adicionar materiais -->
        <div id="materiais-container">
            <div class="row align-items-end material-item">
                <div class="col-md-4 mb-3">
                    <label for="materiais" class="form-label">Material</label>
                    <select class="form-select material-select" name="materiais[]">
                        <option value="">Selecione um material</option>
                        <?php
                            // Recupera a lista de materiais do banco de dados
                            $query_materiais = "SELECT id_material, nome, preco FROM material";
                            $res_materiais = $banco->query($query_materiais);

                            while ($material = $res_materiais->fetch_assoc()) {
                                echo "<option value='{$material['id_material']}' data-preco='{$material['preco']}'>{$material['nome']}</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="quantidades" class="form-label">Quantidade</label>
                    <input type="number" class="form-control quantidade-input" name="quantidades[]" step="1">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="precos" class="form-label">Preço Unitário</label>
                    <input type="number" class="form-control preco-input" name="precos[]" step="0.01" readonly>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="subtotais" class="form-label">Subtotal</label>
                    <input type="number" class="form-control subtotal-input" step="0.01" readonly>
                </div>
                <div class="col-md-2 d-flex align-items-end mb-3">
                    <button type="button" class="btn btn-responsive btn-danger btn-remove-material">Remover</button>
                </div>
            </div>
        </div>
        <!-- Botão para adicionar mais materiais -->
        <div class="mb-3">
            <button type="button" class="btn btn-responsive btn-success" id="btn-add-material">Adicionar Material</button>
        </div>
        <!-- Campo para o total do serviço -->
        <div class="mb-5">
            <label for="total" class="form-label">Total</label>
            <input type="number" class="form-control" id="total" name="total" step="0.01" readonly required>
        </div>
        <!-- Botão para submeter o formulário -->
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-responsive btn-primary">Salvar Serviço</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal para exibir mensagens de status -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statusModalLabel">Status da Operação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?= $statusMessage ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-<?= $statusType ?>" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($statusMessage)): ?>
            var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
            statusModal.show();
            <?php unset($_SESSION['statusMessage']); unset($_SESSION['statusType']); ?>
        <?php endif; ?>

        // Adiciona novo material ao contêiner
        document.getElementById('btn-add-material').addEventListener('click', function() {
            var container = document.getElementById('materiais-container');
            var template = document.getElementById('material-template').cloneNode(true);
            template.style.display = 'flex';
            template.removeAttribute('id');
            container.appendChild(template);
        });

        // Remove material do contêiner
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btn-remove-material')) {
                e.target.closest('.material-item').remove();
                updateTotal();
            }
        });

        // Atualiza preço e subtotal quando um material é selecionado
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('material-select')) {
                var selectedOption = e.target.options[e.target.selectedIndex];
                var preco = selectedOption.getAttribute('data-preco');
                var quantidadeInput = e.target.closest('.material-item').querySelector('.quantidade-input');
                var precoInput = e.target.closest('.material-item').querySelector('.preco-input');
                precoInput.value = preco;
                quantidadeInput.dispatchEvent(new Event('input'));
            }
        });

        // Atualiza subtotal e total quando a quantidade ou valor da mão de obra muda
        document.addEventListener('input', function(e) {
            if (e.target && (e.target.classList.contains('quantidade-input') || e.target.id === 'valor_mao_obra')) {
                var quantidade = parseFloat(e.target.value);
                var precoInput = e.target.closest('.material-item') ? e.target.closest('.material-item').querySelector('.preco-input') : null;
                var subtotalInput = e.target.closest('.material-item') ? e.target.closest('.material-item').querySelector('.subtotal-input') : null;
                var preco = precoInput ? parseFloat(precoInput.value) : 0;
                var subtotal = quantidade * preco;
                if (subtotalInput) {
                    subtotalInput.value = subtotal.toFixed(2);
                }
                updateTotal();
            }
        });

        // Função para atualizar o total do serviço
        function updateTotal() {
            var total = 0;
            document.querySelectorAll('.subtotal-input').forEach(function(subtotalInput) {
                total += parseFloat(subtotalInput.value) || 0;
            });
            var valor_mao_obra = parseFloat(document.getElementById('valor_mao_obra').value) || 0;
            if (valor_mao_obra < 0) {
                total = 0;
            } else {
                total += valor_mao_obra;
            }
            document.getElementById('total').value = total.toFixed(2);
        }

        // Validação para impedir valor negativo na mão de obra
        document.getElementById('valor_mao_obra').addEventListener('input', function(e) {
            if (parseFloat(e.target.value) < 0) {
                e.target.value = 0;
            }
            updateTotal();
        });
    });
</script>

<!-- Template Oculto para adicionar novos materiais -->
<div id="material-template" class="row material-item" style="display: none;">
    <div class="col-md-4 mb-3">
        <label for="materiais" class="form-label">Material</label>
        <select class="form-select material-select" name="materiais[]">
            <option value="">Selecione um material</option>
            <?php
                // Recupera a lista de materiais do banco de dados
                $query_materiais = "SELECT id_material, nome, preco FROM material";
                $res_materiais = $banco->query($query_materiais);

                while ($material = $res_materiais->fetch_assoc()) {
                    echo "<option value='{$material['id_material']}' data-preco='{$material['preco']}'>{$material['nome']}</option>";
                }
            ?>
        </select>
    </div>
    <div class="col-md-2 mb-3">
        <label for="quantidades" class="form-label">Quantidade</label>
        <input type="number" class="form-control quantidade-input" name="quantidades[]" step="1">
    </div>
    <div class="col-md-2 mb-3">
        <label for="precos" class="form-label">Preço Unitário</label>
        <input type="number" class="form-control preco-input" name="precos[]" step="0.01" readonly>
    </div>
    <div class="col-md-2 mb-3">
        <label for="subtotais" class="form-label">Subtotal</label>
        <input type="number" class="form-control subtotal-input" step="0.01" readonly>
    </div>
    <div class="col-md-2 d-flex align-items-end mb-3">
        <button type="button" class="btn btn-responsive btn-danger btn-remove-material">Remover</button>
    </div>
</div>

<?php include_once "includes/templates/footer.php"; ?>
