<?php
include '../../config/database.php';
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

// Verificar se o usuário existe
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['username'] = $username;
    header("Location: ../../index.php");
} else {
    header("Location: login.php");
    echo "<p>Usuário ou senha inválidos.</p>";
    echo "<a href='login.php'>Tentar novamente</a>";
}
?>
