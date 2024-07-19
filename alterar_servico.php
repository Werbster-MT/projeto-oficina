<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION)) {
    header("Location: index.php");
    exit();
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

$statusMessage = isset($_SESSION['statusMessage']) ? $_SESSION['statusMessage'] : '';
$statusType = isset($_SESSION['statusType']) ? $_SESSION['statusType'] : '';
unset($_SESSION['statusMessage'], $_SESSION['statusType']);

$currentPage = "alterar_servicos";
require_once "includes/templates/header.php";
?>

<main class="container mt-5 mb-5">
    <h2>Editar Serviço</h2>
    <form method="POST" action="atualizar_servico.php">
        <input type="hidden" name="id_servico" value="<?= $servico->id_servico ?>">
        <div class="mb-3">
            <label for="nome_servico" class="form-label">Nome do Serviço</label>
            <input type="text" class="form-control" id="nome_servico" name="nome_servico" value="<?= $servico->nome_servico ?>" required>
        </div>
        <div class="mb-3">
            <label for="descricao_servico" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao_servico" name="descricao_servico" required><?= $servico->descricao_servico ?></textarea>
        </div>
        <div class="mb-3">
            <label for="data_inicio" class="form-label">Data Início</label>
            <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= (new DateTime($servico->data_inicio))->format('Y-m-d') ?>" required>
        </div>
        <div class="mb-3">
            <label for="data_fim" class="form-label">Data Fim</label>
            <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= (new DateTime($servico->data_fim))->format('Y-m-d') ?>" required>
        </div>
        <div class="mb-3">
            <label for="valor_mao_obra" class="form-label">Valor da Mão de Obra</label>
            <input type="number" class="form-control" id="valor_mao_obra" name="valor_mao_obra" value="<?= $servico->total ?>" step="0.01" required>
        </div>
        <div id="materiais-container">
            <?php
            do {
                if ($servico->id_material) {
                    ?>
                    <div class="row mb-3 material-item">
                        <div class="col-md-4">
                            <label for="materiais" class="form-label">Material</label>
                            <select class="form-select material-select" name="materiais[]">
                                <option value="<?= $servico->id_material ?>" data-preco="<?= $servico->preco_unitario ?>"><?= $servico->nome_material ?></option>
                                <?php
                                $query_materiais = "SELECT id_material, nome, preco FROM material";
                                $res_materiais = $banco->query($query_materiais);

                                while ($material = $res_materiais->fetch_assoc()) {
                                    echo "<option value='{$material['id_material']}' data-preco='{$material['preco']}'>{$material['nome']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="quantidades" class="form-label">Quantidade</label>
                            <input type="number" class="form-control quantidade-input" name="quantidades[]" value="<?= $servico->quantidade ?>" step="1">
                        </div>
                        <div class="col-md-2">
                            <label for="precos" class="form-label">Preço Unitário</label>
                            <input type="number" class="form-control preco-input" name="precos[]" value="<?= $servico->preco_unitario ?>" step="0.01" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="subtotais" class="form-label">Subtotal</label>
                            <input type="number" class="form-control subtotal-input" value="<?= $servico->subtotal ?>" step="0.01" readonly>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-remove-material">Remover</button>
                        </div>
                    </div>
                    <?php
                }
            } while ($servico = $res->fetch_object());
            ?>
        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="btn-add-material">Adicionar Material</button>
        </div>
        <div class="mb-3">
            <label for="total" class="form-label">Total</label>
            <input type="number" class="form-control" id="total" name="total" step="0.01" readonly required>
        </div>
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </div>
    </form>
</main>

<!-- Modal -->
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
        <?php endif; ?>

        document.getElementById('btn-add-material').addEventListener('click', function() {
            var container = document.getElementById('materiais-container');
            var template = document.getElementById('material-template').cloneNode(true);
            template.style.display = 'flex';
            template.removeAttribute('id');
            container.appendChild(template);
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btn-remove-material')) {
                e.target.closest('.material-item').remove();
                updateTotal();
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('material-select')) {
                var selectedOption = e.target.options[e.target.selectedIndex];
                var preco = selectedOption.getAttribute('data-preco');
                var materialItem = e.target.closest('.material-item');
                if (materialItem) {
                    var quantidadeInput = materialItem.querySelector('.quantidade-input');
                    var precoInput = materialItem.querySelector('.preco-input');
                    if (precoInput) {
                        precoInput.value = preco;
                    }
                    if (quantidadeInput) {
                        quantidadeInput.dispatchEvent(new Event('input'));
                    }
                }
            }
        });

        document.addEventListener('input', function(e) {
            if (e.target && e.target.classList.contains('quantidade-input')) {
                var materialItem = e.target.closest('.material-item');
                if (materialItem) {
                    var precoInput = materialItem.querySelector('.preco-input');
                    var subtotalInput = materialItem.querySelector('.subtotal-input');
                    var preco = parseFloat(precoInput ? precoInput.value : 0);
                    var quantidade = parseFloat(e.target.value || 0);
                    var subtotal = quantidade * preco;
                    if (subtotalInput) {
                        subtotalInput.value = subtotal.toFixed(2);
                    }
                }
                updateTotal();
            } else if (e.target.id === 'valor_mao_obra') {
                updateTotal();
            }
        });

        function updateTotal() {
            var total = 0;
            document.querySelectorAll('.subtotal-input').forEach(function(subtotalInput) {
                total += parseFloat(subtotalInput.value) || 0;
            });
            var valor_mao_obra = parseFloat(document.getElementById('valor_mao_obra').value) || 0;
            total += valor_mao_obra;
            document.getElementById('total').value = total.toFixed(2);
        }

        // Atualizar o valor total ao carregar a página
        updateTotal();
    });
</script>

<!-- Template Oculto -->
<div id="material-template" class="row mb-3 material-item" style="display: none;">
    <div class="col-md-4">
        <label for="materiais" class="form-label">Material</label>
        <select class="form-select material-select" name="materiais[]">
            <option value="">Selecione um material</option>
            <?php
                $query_materiais = "SELECT id_material, nome, preco FROM material";
                $res_materiais = $banco->query($query_materiais);

                while ($material = $res_materiais->fetch_assoc()) {
                    echo "<option value='{$material['id_material']}' data-preco='{$material['preco']}'>{$material['nome']}</option>";
                }
            ?>
        </select>
    </div>
    <div class="col-md-2">
        <label for="quantidades" class="form-label">Quantidade</label>
        <input type="number" class="form-control quantidade-input" name="quantidades[]" step="1">
    </div>
    <div class="col-md-2">
        <label for="precos" class="form-label">Preço Unitário</label>
        <input type="number" class="form-control preco-input" name="precos[]" step="0.01" readonly>
    </div>
    <div class="col-md-2">
        <label for="subtotais" class="form-label">Subtotal</label>
        <input type="number" class="form-control subtotal-input" step="0.01" readonly>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="button" class="btn btn-danger btn-remove-material">Remover</button>
    </div>
</div>

<?php include_once "includes/templates/footer.php"; ?>
