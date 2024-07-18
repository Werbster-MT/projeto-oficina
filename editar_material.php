<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once "includes/config/banco.php";

if (isset($_GET["id"])) {
    $id_material = $_GET["id"];
    $query = "SELECT * FROM material WHERE id_material = ?";
    $stmt = $banco->prepare($query);
    $stmt->bind_param("i", $id_material);
    $stmt->execute();
    $result = $stmt->get_result();
    $material = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_material = $_POST["id_material"];
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $quantidade = $_POST["quantidade"];
    $preco = $_POST["preco"];

    $query = "UPDATE material SET nome = ?, descricao = ?, quantidade = ?, preco = ? WHERE id_material = ?";
    $stmt = $banco->prepare($query);
    $stmt->bind_param("ssiid", $nome, $descricao, $quantidade, $preco, $id_material);
    $stmt->execute();

    header("Location: materiais.php");
}
?>

<?php 
$currentPage = 'editar_material';
require_once "includes/templates/header.php"; 
?>

<div class="container mt-5">
    <h2>Editar Material</h2>
    <form method="POST">
        <input type="hidden" name="id_material" value="<?= $material['id_material'] ?>">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Material</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?= $material['nome'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" required><?= $material['descricao'] ?></textarea>
        </div>
        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" class="form-control" id="quantidade" name="quantidade" value="<?= $material['quantidade'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="preco" class="form-label">Preço Unitário</label>
            <input type="number" class="form-control" id="preco" name="preco" step="0.01" value="<?= $material['preco'] ?>" required>
        </div>
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </div>
    </form>
</div>

<?php require_once "includes/templates/footer.php"; ?>
