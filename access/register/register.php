<?php
include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar se o usuário já existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        echo "<p>Nome de usuário já está em uso. Tente outro.</p>";
    } else {
        // Criptografar a senha
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Inserir novo usuário no banco de dados
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);

        echo "<p>Cadastro realizado com sucesso! <a href='login.php'>Faça login aqui</a>.</p>";
    }
}
?>

<div class="text-center">
    <h2>Registrar Novo Usuário</h2>
    <form method="POST">
        <label>Usuário:</label>
        <input type="text" name="username" required><br><br>
        <label>Senha:</label>
        <input type="password" name="password" required><br><br>
        <button type="submit">Cadastrar</button>
    </form>
</div>
