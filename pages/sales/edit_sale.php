<?php
include '../../templates/header.php';
include '../../config/database.php';

// Função para obter produtos da venda
function getProdutosVenda($pdo, $venda_id) {
    $stmt = $pdo->prepare("
        SELECT produto_id
        FROM venda_produtos
        WHERE venda_id = ?
    ");
    $stmt->execute([$venda_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Função para obter clientes
function getClientes($pdo) {
    $stmt = $pdo->query("SELECT id, nome FROM clientes");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obter a venda a ser editada
if (!isset($_GET['id'])) {
    header("Location: list_sales.php");
    exit();
}

$sale_id = $_GET['id'];
$stmt = $pdo->prepare("
    SELECT vendas.*, clientes.nome AS cliente_nome 
    FROM vendas 
    LEFT JOIN clientes ON vendas.cliente_id = clientes.id
    WHERE vendas.id = ?
");
$stmt->execute([$sale_id]);
$venda = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venda) {
    header("Location: list_sales.php");
    exit();
}

// Buscar produtos cadastrados
$stmtProdutos = $pdo->query("SELECT * FROM produtos");
$produtos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);

// Buscar clientes
$clientes = getClientes($pdo);

// Lógica para atualizar a venda
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $quantidade_parcelas = $_POST['quantidade_parcelas'];
    
    // Atualizar a venda
    $stmtUpdate = $pdo->prepare("
        UPDATE vendas SET cliente_id = ?, forma_pagamento = ?, quantidade_parcelas = ? WHERE id = ?
    ");
    $stmtUpdate->execute([$cliente_id, $forma_pagamento, $quantidade_parcelas, $sale_id]);

    // Remover produtos antigos
    $stmtDeleteProdutos = $pdo->prepare("DELETE FROM venda_produtos WHERE venda_id = ?");
    $stmtDeleteProdutos->execute([$sale_id]);

    // Inserir produtos atualizados
    if (isset($_POST['produtos'])) {
        foreach ($_POST['produtos'] as $produto_id) {
            $stmtProdutoVenda = $pdo->prepare("INSERT INTO venda_produtos (venda_id, produto_id) VALUES (?, ?)");
            $stmtProdutoVenda->execute([$sale_id, $produto_id]);
        }
    }

    header("Location: list_sales.php");
    exit();
}
?>

<div class="text-center">
    <h2>Editar Venda</h2>

    <form method="POST">
        <label>Cliente:</label>
        <select name="cliente" required>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id'] ?>" <?= $cliente['id'] == $venda['cliente_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cliente['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <br>
        <label>Forma de Pagamento:</label>
        <select name="forma_pagamento" required>
            <option value="Cartão" <?= $venda['forma_pagamento'] == 'Cartão' ? 'selected' : '' ?>>Cartão</option>
            <option value="Dinheiro" <?= $venda['forma_pagamento'] == 'Dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
            <option value="Boleto" <?= $venda['forma_pagamento'] == 'Boleto' ? 'selected' : '' ?>>Boleto</option>
        </select><br>
        <br>
        <label>Quantidade de Parcelas:</label>
        <!-- htmlspecialchars — Converte caracteres especiais em entidades HTML -->
        <input type="number" name="quantidade_parcelas" value="<?= htmlspecialchars($venda['quantidade_parcelas']) ?>" min="1" required><br>

        <br>
        <label>Itens da Venda (Selecione os produtos):</label><br>
        <select name="produtos[]" multiple required>
            <?php foreach ($produtos as $produto): ?>
                <option value="<?= $produto['id'] ?>" <?= in_array($produto['id'], getProdutosVenda($pdo, $sale_id)) ? 'selected' : '' ?>>
                    <?= $produto['nome'] ?> - R$ <?= $produto['preco'] ?>
                </option>
            <?php endforeach; ?>
        </select><br>
         
        <br>
        <button type="submit" class="btn btn-success">Atualizar Venda</button>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>
