<?php
include '../../templates/header.php';
include '../../config/database.php';

// Buscar todas as vendas, incluindo o nome do cliente
$stmt = $pdo->query("
    SELECT vendas.*, clientes.nome AS cliente_nome 
    FROM vendas 
    LEFT JOIN clientes ON vendas.cliente_id = clientes.id
");
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getProdutosVenda($pdo, $venda_id) {
    $stmt = $pdo->prepare("
        SELECT produtos.nome, produtos.preco
        FROM venda_produtos
        JOIN produtos ON venda_produtos.produto_id = produtos.id
        WHERE venda_produtos.venda_id = ?
    ");
    $stmt->execute([$venda_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lógica de exclusão
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmtDelete = $pdo->prepare("DELETE FROM vendas WHERE id = ?");
    $stmtDelete->execute([$delete_id]);
    header("Location: list_sales.php");
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
    <h2>Listagem de Vendas</h2>
</div>
<table border="1" style="margin: auto;">
    <thead>
        <tr>
            <th>ID Venda</th>
            <th>Cliente</th>
            <th>Forma de Pagamento</th>
            <th>Produtos Vendidos</th>
            <th>Valor Total da Venda</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($vendas as $venda): ?>
            <tr>
                <td><?= $venda['id'] ?></td>
                <td><?= $venda['cliente_nome'] ?: 'N/A' ?></td>
                <td><?= $venda['forma_pagamento'] ?></td>
                
                <td>
                    <?php
                    $produtos = getProdutosVenda($pdo, $venda['id']);
                    $total_venda = 0; // Variável para somar o total da venda

                    foreach ($produtos as $produto) {
                        echo $produto['nome'] . ' - R$ ' . number_format($produto['preco'], 2, ',', '.') . '<br>';
                        $total_venda += $produto['preco'];
                    }
                    ?>
                </td>
                
                <td>R$ <?= number_format($total_venda, 2, ',', '.') ?></td>
                
                <td>
                    <a href="edit_sale.php?id=<?= $venda['id'] ?>">Editar</a>
                    <a href="?delete_id=<?= $venda['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta venda?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include '../../templates/footer.php'; ?>
