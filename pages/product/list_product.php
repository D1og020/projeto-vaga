<?php
include '../../templates/header.php';
include '../../config/database.php';

// Buscar todos os produtos cadastrados
$stmt = $pdo->query("SELECT * FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lógica de exclusão
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmtDelete = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    $stmtDelete->execute([$delete_id]);
    header("Location: list_products.php");
    exit();
}
?>

<style>
    table {
        margin: auto;
        border-collapse: collapse; /* Remove espaçamento entre bordas */
    }
    th, td {
        border: 1px solid #ccc; /* Borda das células */
        padding: 8px; /* Espaçamento interno */
        text-align: left; /* Alinhamento do texto */
    }
    th {
        background-color: #f2f2f2; /* Cor de fundo para os cabeçalhos */
    }
    tr:nth-child(even) {
        background-color: #f9f9f9; /* Cor de fundo alternada para as linhas */
    }
</style>

<div class="text-center">
    <h2>Listagem de Produtos</h2>
</div>
<table border="1" style="margin: auto;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Preço</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produtos as $produto): ?>
            <tr>
                <td><?= $produto['id'] ?></td>
                <td><?= htmlspecialchars($produto['nome']) ?></td>
                <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $produto['id'] ?>">Editar</a>
                    <a href="?delete_id=<?= $produto['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../../templates/footer.php'; ?>
