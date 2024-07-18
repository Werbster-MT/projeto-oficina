<?php
    session_start();
    if (empty($_SESSION)) {
        header("Location: index.php");
    }
    $currentPage = "alterar_venda";
    include_once "includes/config/banco.php";

    $id_venda = $_GET['id_venda'] ?? null;

    if (!$id_venda) {
        header("Location: vendas.php");
        exit();
    }

    $query_venda = "SELECT
                        v.id_venda,
                        v.data,
                        v.total,
                        u.nome as nome_usuario
                    FROM
                        venda v
                    JOIN usuario u ON v.usuario = u.usuario
                    WHERE v.id_venda = ?";
    $stmt = $banco->prepare($query_venda);
    $stmt->bind_param('i', $id_venda);
    $stmt->execute();
    $res = $stmt->get_result();
    $venda = $res->fetch_object();

    // Query para obter os materiais da venda
    $query_materiais = "SELECT
                            vm.id_material,
                            m.nome,
                            vm.quantidade,
                            vm.preco_unitario,
                            vm.subtotal
                        FROM
                            venda_material vm
                        JOIN material m ON vm.id_material = m.id_material
                        WHERE vm.id_venda = ?";
    $stmt = $banco->prepare($query_materiais);
    $stmt->bind_param('i', $id_venda);
    $stmt->execute();
    $materiais = $stmt->get_result();

    require_once "includes/templates/header.php";
?>

<main class="container mt-5 mb-5">
    <h2>Editar Venda</h2>
    <form method="POST" action="atualizar_venda.php">
        <input type="hidden" name="id_venda" value="<?= $venda->id_venda ?>">
        <div class="mb-3">
            <label for="data" class="form-label">Data da Venda</label>
            <input type="datetime-local" class="form-control" id="data" name="data" value="<?= date('Y-m-d\TH:i', strtotime($venda->data)) ?>">
        </div>
        <div id="materiaisContainer">
            <?php while ($material = $materiais->fetch_assoc()): ?>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Materiais</label>
                        <input type="hidden" name="materiais[]" value="<?= $material['id_material'] ?>">
                        <input type="text" class="form-control" value="<?= $material['nome'] ?>" disabled>
                    </div>
                    <div class="col-md-3">
                        <label for="quantidades">Quantidade</label>
                        <input type="number" class="form-control" name="quantidades[]" value="<?= $material['quantidade'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="precos">Preço Unitário</label>
                        <input type="number" class="form-control" name="precos[]" value="<?= $material['preco_unitario'] ?>" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger" onclick="removeMaterial(this)">Remover</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <button type="button" class="btn btn-primary" onclick="addMaterial()">Adicionar Material</button>
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</main>

<script>
    function addMaterial() {
        var container = document.getElementById('materiaisContainer');
        var div = document.createElement('div');
        div.className = 'row mb-3';
        div.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control" name="materiais[]" placeholder="Nome do Material" required>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="quantidades[]" placeholder="Quantidade" required>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="precos[]" placeholder="Preço Unitário" step="0.01" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger" onclick="removeMaterial(this.parentNode.parentNode)">Remover</button>
            </div>`;
        container.appendChild(div);
    }

    function removeMaterial(element) {
        element.remove();
    }
</script>

<?php include_once "includes/templates/footer.php"; ?>
