<?php
include '../../config/database.php';

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Verificar se o usuário já existe
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "<p>Usuário já existe.</p>";
    echo "<a href='register.php'>Tentar novamente</a>";
} else {
    // Inserir novo usuário
    $stmt = $pdo->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);
    
    echo "<p>Usuário cadastrado com sucesso! <a href='../login/login.php'>Faça login</a></p>";
}
?>
