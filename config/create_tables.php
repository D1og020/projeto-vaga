<!-- ACESSAR A URL http://localhost/projeto-vaga/create_tables.php -->
 <!-- OU EXECUTAR O COMANDO php create_tables.php -->

<?php
include 'database.php';// Conexão com o banco

try {
    // Criar tabela 'clientes'
    $pdo->exec("CREATE TABLE IF NOT EXISTS clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        documento VARCHAR(11) NOT NULL UNIQUE
    )");

    // Criar tabela 'produtos'
    $pdo->exec("CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        preco DECIMAL(10, 2) NOT NULL
    )");

    // Criar tabela 'vendas'
    $pdo->exec("CREATE TABLE IF NOT EXISTS vendas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NOT NULL,
        forma_pagamento VARCHAR(50) NOT NULL,
        valor_total DECIMAL(10, 2) NOT NULL,
        quantidade_parcelas INT NOT NULL,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    )");

    // Criar tabela 'venda_produtos' (relaciona vendas e produtos)
    $pdo->exec("CREATE TABLE IF NOT EXISTS venda_produtos (
        venda_id INT NOT NULL,
        produto_id INT NOT NULL,
        PRIMARY KEY (venda_id, produto_id),
        FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
        FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
    )");

    // Criar tabela 'parcelas'
    $pdo->exec("CREATE TABLE IF NOT EXISTS parcelas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        venda_id INT NOT NULL,
        data_vencimento DATE NOT NULL,
        valor DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE
    )");

    echo "Tabelas criadas com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao criar tabelas: " . $e->getMessage();
}
?>
