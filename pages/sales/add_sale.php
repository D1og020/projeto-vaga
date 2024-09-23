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
    $forma_pagamento = $_POST['forma_pagamento'];
    $valor_total = $_POST['valor_total'];
    $parcelas = $_POST['parcelas'];
    $datas_vencimento = $_POST['datas_vencimento'];

    // Inserir a venda

    // converto a parcela para int
    $parcela_convert = intval($parcelas);
    
    $stmt = $pdo->prepare("INSERT INTO vendas (cliente_id, forma_pagamento, valor_total, quantidade_parcelas) VALUES (?, ?, ?, ?)");
    $stmt->execute([$cliente_id, $forma_pagamento, $valor_total, $parcela_convert]);
    $venda_id = $pdo->lastInsertId();

    // Inserir os produtos da venda
    foreach ($produtos_venda as $produto_id) {
        $stmtProdutoVenda = $pdo->prepare("INSERT INTO venda_produtos (venda_id, produto_id) VALUES (?, ?)");
        $stmtProdutoVenda->execute([$venda_id, $produto_id]);
    }

    // Inserir parcelas
    foreach ($parcelas as $index => $parcela_valor) {
        $data_vencimento = $datas_vencimento[$index];
        $stmtParcela = $pdo->prepare("INSERT INTO parcelas (venda_id, data_vencimento, valor) VALUES (?, ?, ?)");
        $stmtParcela->execute([$venda_id, $data_vencimento, $parcela_valor]);
    }

    // echo "<p>Venda registrada com sucesso!</p>";
}
?>
<div class="text-center">
    <h2>Registrar Venda</h2>

    <form method="POST" id="saleForm">
        
        <!-- pego meu cliente -->
        <label>Cliente: </label>
        <select name="cliente_id" required>
            <option value="">Selecione um cliente</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nome']) ?></option>
            <?php endforeach; ?>
        </select><br>
        <br>
        <!-- pego os itens de compra -->
        <label>Itens da Venda (Selecione os produtos):</label><br>
        <select name="produtos[]" id="produtos" multiple required>
            <?php foreach ($produtos as $produto): ?>
                <option value="<?= $produto['id'] ?>" data-preco="<?= $produto['preco'] ?>"><?= $produto['nome'] ?> - R$ <?= $produto['preco'] ?></option>
            <?php endforeach; ?>
        </select><br>
        <br>
        <!-- pego a forma de pagamento -->
        <label>Forma de Pagamento:</label>
        <select name="forma_pagamento" required>
            <option value="Cartão">Cartão</option>
            <option value="Dinheiro">Dinheiro</option>
            <option value="Boleto">Boleto</option>
        </select><br>
        <br>   
        <label>Valor Total:</label>
        <input type="number" name="valor_total" id="valor_total" readonly><br>
        <br>       
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
// Função para calcular o valor total com base nos produtos selecionados

// pego as variaveis
const produtosSelect = document.getElementById('produtos');
const valorTotalInput = document.getElementById('valor_total');
const numParcelasInput = document.getElementById('num_parcelas');
const parcelasContainer = document.getElementById('parcelasContainer');

// Função para atualizar o valor total
function atualizarValorTotal() {
    let total = 0;
    Array.from(produtosSelect.selectedOptions).forEach(option => {
        total += parseFloat(option.getAttribute('data-preco'));
    });
    valorTotalInput.value = total.toFixed(2);
    gerarParcelas();
}

// função para gerar as parcelas personalidas
function gerarParcelas() {

    // capturo os variaveis
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

// Atualizar o valor total e as parcelas ao selecionar produtos ou mudar o número de parcelas
produtosSelect.addEventListener('change', atualizarValorTotal);
numParcelasInput.addEventListener('input', gerarParcelas);

atualizarValorTotal(); // Atualiza ao carregar a página
</script>

<?php include '../../templates/footer.php'; ?>
