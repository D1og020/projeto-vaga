<?php
include '../../templates/header.php';
include '../../config/database.php';

// Obter o produto a ser editado
if (!isset($_GET['id'])) {
    header("Location: list_products.php");
    exit();
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$product_id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: list_products.php");
    exit();
}

// Lógica para atualizar o produto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];

    // Atualizar o produto no banco de dados
    $stmtUpdate = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ? WHERE id = ?");
    $stmtUpdate->execute([$nome, $preco, $product_id]);

    // Redirecionar de volta para a página de edição do produto
    header("Location: edit_product.php?id=" . $product_id);
    exit();
}
?>

<div class="text-center">
    <h2>Editar Produto</h2>
    <br>
    <form method="POST">
        <label>Nome do Produto:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
        <br>
        <br>
        <label>Preço do Produto:</label>
        <input type="number" step="0.01" name="preco" value="<?= htmlspecialchars($produto['preco']) ?>" required>
        <br>
        <br>
        <button type="submit" class="btn btn-success">Atualizar Produto</button>
    </form>
</div>
<?php include '../../templates/footer.php'; ?>
