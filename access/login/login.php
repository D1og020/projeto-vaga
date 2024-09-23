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
    <link rel="stylesheet" href="../../assents/main.css">
</head>
<body>
     <div class="page">
        <form action="process_login.php" method="POST" class="formLogin">
            <h1>Login</h1>
            <p>Digite os seus dados de acesso no campo abaixo.</p>
            <label>Usuário:</label>
            <input type="text" name="username" required>
            <label>Senha:</label>
            <input type="password" name="password" required>
            <input type="submit" value="Acessar" class="btn" />
            <p>Não tem uma conta? <a href="../register/register.php">Cadastre-se aqui</a></p>
        </form>
    </div>
</body>
</html>
