<?php
include '../../templates/header.php';
include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade']; // Novo campo para quantidade

    // Inserir o produto no banco de dados com a quantidade
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, quantidade) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $preco, $quantidade]);

    // echo "<p>Produto cadastrado com sucesso!</p>";
}
?>

<div class="text-center">
    <h2>Cadastrar Produto</h2>
    <br>
    <form method="POST">
        <label>Nome do Produto:</label>
        <input type="text" name="nome" required>
        <br>
        <br>
        <label>Preço do Produto:</label>
        <input type="number" step="0.01" name="preco" required>
        <br>
        <br>
        <label>Quantidade do Produto:</label>
        <input type="number" name="quantidade" min="1" required>
        <br>
        <br>
        <button type="submit" class="btn btn-success">Cadastrar Produto</button>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>
