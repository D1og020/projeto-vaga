<?php
include '../../templates/header.php';
include '../../config/database.php';

// Buscar produtos cadastrados
$stmtProdutos = $pdo->query("SELECT * FROM produtos");
$produtos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);

// Buscar clientes cadastrados
$stmtClientes = $pdo->query("SELECT * FROM clientes");
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = !empty($_POST['cliente_id']) ? $_POST['cliente_id'] : null;
    $produtos_venda = $_POST['produtos'];
    $quantidades = $_POST['quantidades'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $valor_total = $_POST['valor_total'];
    $parcelas = $_POST['parcelas'];
    $datas_vencimento = $_POST['datas_vencimento'];

    // Inserir a venda
    $parcela_convert = intval($_POST['num_parcelas']);
    $stmt = $pdo->prepare("INSERT INTO vendas (cliente_id, forma_pagamento, valor_total, quantidade_parcelas) VALUES (?, ?, ?, ?)");
    $stmt->execute([$cliente_id, $forma_pagamento, $valor_total, $parcela_convert]);
    $venda_id = $pdo->lastInsertId();

    // Inserir os produtos da venda
    foreach ($produtos_venda as $produto_id) {
        $quantidade = $quantidades[$produto_id];

        // Atualizar o estoque do produto
        $stmtEstoque = $pdo->prepare("SELECT quantidade FROM produtos WHERE id = ?");
        $stmtEstoque->execute([$produto_id]);
        $produtoEstoque = $stmtEstoque->fetch(PDO::FETCH_ASSOC);

        if ($produtoEstoque['quantidade'] < $quantidade) {
            die("Erro: a quantidade solicitada do produto {$produto_id} excede o estoque disponível.");
        }

        // Inserir na tabela venda_produtos
        $stmtProdutoVenda = $pdo->prepare("INSERT INTO venda_produtos (venda_id, produto_id, quantidade) VALUES (?, ?, ?)");
        $stmtProdutoVenda->execute([$venda_id, $produto_id, $quantidade]);

        // Atualizar o estoque no banco de dados
        $novoEstoque = $produtoEstoque['quantidade'] - $quantidade;
        $stmtUpdateEstoque = $pdo->prepare("UPDATE produtos SET quantidade = ? WHERE id = ?");
        $stmtUpdateEstoque->execute([$novoEstoque, $produto_id]);
    }

    // Inserir parcelas
    foreach ($parcelas as $index => $parcela_valor) {
        $data_vencimento = $datas_vencimento[$index];
        $stmtParcela = $pdo->prepare("INSERT INTO parcelas (venda_id, data_vencimento, valor) VALUES (?, ?, ?)");
        $stmtParcela->execute([$venda_id, $data_vencimento, $parcela_valor]);
    }

    // Redirecionar após a venda
    header("Location: ../sales/add_sale.php");
}
?>

<div class="text-center">
    <h2>Registrar Venda</h2>

    <form method="POST" id="saleForm">
        
        <!-- Cliente -->
        <label>Cliente: </label>
        <select name="cliente_id" required>
            <option value="">Selecione um cliente</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nome']) ?></option>
            <?php endforeach; ?>
        </select><br>
        <br>
        
        <!-- Produtos da venda com quantidades e estoque disponível -->
        <label>Itens da Venda (Selecione os produtos e quantidades):</label><br>
        <div id="produtosContainer">
            <?php foreach ($produtos as $produto): ?>
                <div>
                    <input type="checkbox" name="produtos[<?= $produto['id'] ?>]" value="<?= $produto['id'] ?>" data-preco="<?= $produto['preco'] ?>" data-estoque="<?= $produto['quantidade'] ?>" class="produto-checkbox">
                    <?= $produto['nome'] ?> - R$ <?= number_format($produto['preco'], 2, ',', '.') ?> (Estoque: <?= $produto['quantidade'] ?>)
                    <input type="number" name="quantidades[<?= $produto['id'] ?>]" value="1" min="1" max="<?= $produto['quantidade'] ?>" class="produto-quantidade" style="width: 50px;" disabled>
                </div>
            <?php endforeach; ?>
        </div>
        <br>
        
        <!-- Forma de pagamento -->
        <label>Forma de Pagamento:</label>
        <select name="forma_pagamento" required>
            <option value="Cartão">Cartão</option>
            <option value="Dinheiro">Dinheiro</option>
            <option value="Boleto">Boleto</option>
        </select><br>
        <br>   
        
        <!-- Valor Total -->
        <label>Valor Total:</label>
        <input type="number" name="valor_total" id="valor_total" readonly><br>
        <br>
        
        <!-- Parcelas -->
        <label>Número de Parcelas:</label>
        <input type="number" id="num_parcelas" name="num_parcelas" min="1" value="1" required><br>
         
        <br>
        <div id="parcelasContainer">
            <!-- As parcelas e datas de vencimento serão geradas aqui -->
        </div>
        <br>
        
        <button type="submit" class="btn btn-success">Cadastrar Venda</button>
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

    // Percorre cada checkbox de produto e, se estiver selecionado, adiciona seu preço ao total multiplicado pela quantidade
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

// Função para gerar as parcelas personalizadas
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
numParcelasInput.addEventListener('input', gerarParcelas);

// Atualizar o valor total ao carregar a página
atualizarValorTotal();
</script>

<?php include '../../templates/footer.php'; ?>
