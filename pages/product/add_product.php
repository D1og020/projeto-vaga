<?php
include '../../templates/header.php';
include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];

    // Inserir o produto no banco de dados
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");
    $stmt->execute([$nome, $preco]);

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
        <button type="submit" class="btn btn-success">Cadastrar Produto</button>
    </form>
</div>
<?php include '../../templates/footer.php'; ?>
