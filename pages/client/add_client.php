<?php
include '../../config/database.php';
include '../../templates/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $documento = $_POST['documento'];

    // Remover máscara do CPF antes de salvar no banco
    $documento = preg_replace('/\D/', '', $documento);

    $stmt = $pdo->prepare("INSERT INTO clientes (nome, documento) VALUES (?, ?)");
    $stmt->execute([$nome, $documento]);

    header("Location: list_clients.php");
    exit();
}
?>
<div class="text-center">
    <h2>Cadastrar Cliente</h2>

    <form method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <br>
        <br>
        <label>Documento (CPF):</label>
        <input type="text" name="documento" id="cpf" maxlength="14" required>
        <br>
        <br>
        <button type="submit" class="btn btn-success">Cadastrar Cliente</button>
    </form>
</div>

<script>
// Função para aplicar a máscara de CPF
function mascaraCPF(input) {
    input.value = input.value
        .replace(/\D/g, '') // Remove tudo o que não é dígito
        .replace(/(\d{3})(\d)/, '$1.$2') // Coloca o primeiro ponto
        .replace(/(\d{3})(\d)/, '$1.$2') // Coloca o segundo ponto
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2'); // Coloca o traço no CPF
}

// Aplica a máscara de CPF ao digitar
document.getElementById('cpf').addEventListener('input', function() {
    mascaraCPF(this);
});
</script>

<?php include '../../templates/footer.php'; ?>
