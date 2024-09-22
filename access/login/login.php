<?php
include '../../config/database.php';
session_start();

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="process_login.php" method="POST">
        <label>Usuário:</label>
        <input type="text" name="username" required><br><br>
        <label>Senha:</label>
        <input type="password" name="password" required><br><br>
        <button type="submit">Entrar</button>
    </form>

    <p>Não tem uma conta? <a href="register.php">Cadastre-se aqui</a></p>
</body>
</html>
