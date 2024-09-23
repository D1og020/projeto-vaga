<?php
include '../../templates/header.php';
include '../../config/database.php';

// Obter o ID da venda a ser editada
$venda_id = $_GET['id'];

// Buscar a venda e seus detalhes
$stmtVenda = $pdo->prepare("SELECT * FROM vendas WHERE id = ?");
$stmtVenda->execute([$venda_id]);
$venda = $stmtVenda->fetch(PDO::FETCH_ASSOC);

// Buscar produtos cadastrados
$stmtProdutos = $pdo->query("SELECT * FROM produtos");
$produtos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);

// Buscar clientes cadastrados
$stmtClientes = $pdo->query("SELECT * FROM clientes");
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

// Obter produtos da venda
$stmtProdutosVenda = $pdo->prepare("
    SELECT vp.produto_id, vp.quantidade, p.nome, p.preco 
    FROM venda_produtos vp
    JOIN produtos p ON vp.produto_id = p.id
    WHERE vp.venda_id = ?
");
$stmtProdutosVenda->execute([$venda_id]);
$produtosVenda = $stmtProdutosVenda->fetchAll(PDO::FETCH_ASSOC);

// Obter parcelas da venda
$stmtParcelas = $pdo->prepare("SELECT * FROM parcelas WHERE venda_id = ?");
$stmtParcelas->execute([$venda_id]);
$parcelasVenda = $stmtParcelas->fetchAll(PDO::FETCH_ASSOC);

// Lógica de atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = !empty($_POST['cliente_id']) ? $_POST['cliente_id'] : null;
    $produtos_venda = $_POST['produtos'];
    $quantidades = $_POST['quantidades'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $valor_total = $_POST['valor_total'];
    $num_parcelas = intval($_POST['num_parcelas']);
    $parcelas = $_POST['parcelas'];
    $datas_vencimento = $_POST['datas_vencimento'];

    // Atualizar a venda
    $stmtUpdateVenda = $pdo->prepare("
        UPDATE vendas SET cliente_id = ?, forma_pagamento = ?, valor_total = ?, quantidade_parcelas = ?
        WHERE id = ?
    ");
    $stmtUpdateVenda->execute([$cliente_id, $forma_pagamento, $valor_total, $num_parcelas, $venda_id]);

    // Remover produtos existentes da venda
    $stmtDeleteProdutos = $pdo->prepare("DELETE FROM venda_produtos WHERE venda_id = ?");
    $stmtDeleteProdutos->execute([$venda_id]);

    // Inserir novos produtos da venda
    foreach ($produtos_venda as $produto_id => $valor) {
        if ($valor) { // Verifica se o produto está selecionado
            $quantidade = $quantidades[$produto_id];

            // Inserir na tabela venda_produtos
            $stmtProdutoVenda = $pdo->prepare("INSERT INTO venda_produtos (venda_id, produto_id, quantidade) VALUES (?, ?, ?)");
            $stmtProdutoVenda->execute([$venda_id, $produto_id, $quantidade]);
        }
    }

    // Remover parcelas existentes
    $stmtDeleteParcelas = $pdo->prepare("DELETE FROM parcelas WHERE venda_id = ?");
    $stmtDeleteParcelas->execute([$venda_id]);

    // Inserir parcelas
    foreach ($parcelas as $index => $parcela_valor) {
        $data_vencimento = $datas_vencimento[$index];
        $stmtParcela = $pdo->prepare("INSERT INTO parcelas (venda_id, data_vencimento, valor) VALUES (?, ?, ?)");
        $stmtParcela->execute([$venda_id, $data_vencimento, $parcela_valor]);
    }

    // Redirecionar após a atualização
    header("Location: ../sales/list_sales.php");
    exit();
}
?>

<div class="text-center">
    <h2>Editar Venda</h2>

    <form method="POST" id="saleForm">
        <!-- Cliente -->
        <label>Cliente: </label>
        <select name="cliente_id" required>
            <option value="">Selecione um cliente</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id'] ?>" <?= $cliente['id'] == $venda['cliente_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cliente['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- Produtos da venda com quantidades e estoque disponível -->
        <label>Itens da Venda (Selecione os produtos e quantidades):</label><br>
        <div id="produtosContainer">
            <?php foreach ($produtos as $produto): ?>
                <div>
                    <input type="checkbox" name="produtos[<?= $produto['id'] ?>]" value="<?= $produto['id'] ?>" 
                        data-preco="<?= $produto['preco'] ?>" 
                        data-estoque="<?= $produto['quantidade'] ?>" 
                        class="produto-checkbox"
                        <?= in_array($produto['id'], array_column($produtosVenda, 'produto_id')) ? 'checked' : '' ?>
                    >
                    <?= $produto['nome'] ?> - R$ <?= number_format($produto['preco'], 2, ',', '.') ?> (Estoque: <?= $produto['quantidade'] ?>)
                    <input type="number" name="quantidades[<?= $produto['id'] ?>]" value="<?= in_array($produto['id'], array_column($produtosVenda, 'produto_id')) ? 
                        $produtosVenda[array_search($produto['id'], array_column($produtosVenda, 'produto_id'))]['quantidade'] : 0 ?>" 
                        min="0" max="<?= $produto['quantidade'] ?>" class="produto-quantidade" style="width: 50px;">
                </div>
            <?php endforeach; ?>
        </div><br>

        <!-- Forma de pagamento -->
        <label>Forma de Pagamento:</label>
        <select name="forma_pagamento" required>
            <option value="Cartão" <?= $venda['forma_pagamento'] == 'Cartão' ? 'selected' : '' ?>>Cartão</option>
            <option value="Dinheiro" <?= $venda['forma_pagamento'] == 'Dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
            <option value="Boleto" <?= $venda['forma_pagamento'] == 'Boleto' ? 'selected' : '' ?>>Boleto</option>
        </select><br><br>

        <!-- Valor Total -->
        <label>Valor Total:</label>
        <input type="number" name="valor_total" id="valor_total" value="<?= number_format($venda['valor_total'], 2, ',', '.') ?>" readonly><br><br>

        <!-- Parcelas -->
        <label>Número de Parcelas:</label>
        <input type="number" id="num_parcelas" name="num_parcelas" min="1" value="<?= count($parcelasVenda) ?>" required><br>

        <br>
        <div id="parcelasContainer">
            <?php foreach ($parcelasVenda as $index => $parcela): ?>
                <div class="parcela">
                    <input type="text" name="parcelas[]" value="<?= htmlspecialchars($parcela['valor']) ?>" readonly>
                    <label> Data de Vencimento:</label>
                    <input type="date" name="datas_vencimento[]" value="<?= htmlspecialchars($parcela['data_vencimento']) ?>" required>
                </div>
            <?php endforeach; ?>
        </div>
        <br>

        <button type="submit" class="btn btn-success">Atualizar Venda</button>
    </form>
</div>

<script>
// Referências a elementos do DOM
const produtosContainer = document.getElementById('produtosContainer');
const valorTotalInput = document.getElementById('valor_total');
const numParcelasInput = document.getElementById('num_parcelas');
const parcelasContainer = document.getElementById('parcelasContainer');

// Função para atualizar o valor total com base nos produtos selecionados e suas quantidades
function atualizarValorTotal() {
    let total = 0;
    const checkboxes = document.querySelectorAll('.produto-checkbox');
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const quantidadeInput = checkbox.closest('div').querySelector('.produto-quantidade');
            const quantidade = parseInt(quantidadeInput.value);
            const preco = parseFloat(checkbox.getAttribute('data-preco'));
            total += quantidade * preco;
        }
    });
    valorTotalInput.value = total.toFixed(2);
    gerarParcelas();
}

// Função para gerar e atualizar as parcelas
function gerarParcelas() {
    const numParcelas = parseInt(numParcelasInput.value);
    const valorTotal = parseFloat(valorTotalInput.value);
    parcelasContainer.innerHTML = '';

    if (valorTotal > 0 && numParcelas > 0) {
        const valorParcela = (valorTotal / numParcelas).toFixed(2);
        for (let i = 0; i < numParcelas; i++) {
            const parcelaDiv = document.createElement('div');
            parcelaDiv.className = 'parcela';

            const valorParcelaInput = document.createElement('input');
            valorParcelaInput.type = 'text';
            valorParcelaInput.name = 'parcelas[]';
            valorParcelaInput.value = valorParcela;
            valorParcelaInput.readOnly = true;
            parcelaDiv.appendChild(document.createTextNode(`Parcela ${i + 1}: R$`));
            parcelaDiv.appendChild(valorParcelaInput);

            const dataVencimentoInput = document.createElement('input');
            dataVencimentoInput.type = 'date';
            dataVencimentoInput.name = 'datas_vencimento[]';
            dataVencimentoInput.required = true;

            // Definir a data de vencimento para cada parcela
            const hoje = new Date();
            hoje.setMonth(hoje.getMonth() + i);
            dataVencimentoInput.value = hoje.toISOString().substring(0, 10);

            parcelaDiv.appendChild(document.createTextNode(' Data de Vencimento: '));
            parcelaDiv.appendChild(dataVencimentoInput);

            parcelasContainer.appendChild(parcelaDiv);
        }
    }
}

// Verificar estoque e controlar o campo de quantidade
document.querySelectorAll('.produto-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const quantidadeInput = this.closest('div').querySelector('.produto-quantidade');
        quantidadeInput.disabled = !this.checked;
        atualizarValorTotal();
    });
});

// Atualizar o valor total ao alterar a quantidade
document.querySelectorAll('.produto-quantidade').forEach(input => {
    input.addEventListener('input', atualizarValorTotal);
});

// Gerar parcelas ao mudar o número de parcelas
numParcelasInput.addEventListener('input', function() {
    gerarParcelas();
    atualizarValorTotal();
});

// Atualizar o valor total ao carregar a página
atualizarValorTotal();
</script>

<?php include '../../templates/footer.php'; ?>
