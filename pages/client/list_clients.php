<?php
include '../../config/database.php';
include '../../templates/header.php';

$stmt = $pdo->query("SELECT * FROM clientes");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <h2>Clientes Cadastrados</h2>
</div>

<table border="1" style="margin: auto;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Documento</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?= $cliente['id'] ?></td>
                <td><?= htmlspecialchars($cliente['nome']) ?></td>
                <td><?= htmlspecialchars($cliente['documento']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../../templates/footer.php'; ?>
