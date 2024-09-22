<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Vendas</title>
    <link rel="stylesheet" href="/projeto-vaga/assets/style.css"> <!-- Link para o CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <h1 class="text-center" >Sistema de Vendas</h1>
        <div>
    <ul class="nav justify-content-center">
        <li class="nav-item">
            <a class="nav-link active" href="/projeto-vaga/index.php">Início</a>
        </li>
        <!-- CLIENTE -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Clientes
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="/projeto-vaga/pages/client/add_client.php">Cadastrar Clientes</a></li>
                <li><a class="dropdown-item" href="/projeto-vaga/pages/client/list_clients.php">Listar Clientes</a></li>
            </ul>
        </li>
        <!-- CADATROS DE PRODUTOS -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Produtos
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="/projeto-vaga/pages/product/add_product.php">Cadastra Produto</a></li>
                <li><a class="dropdown-item" href="/projeto-vaga/pages/product/list_product.php">Listas Produtos</a></li>
            </ul>
        </li>
        <!-- VENDAS -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Vendas
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="/projeto-vaga/pages/sales/add_sale.php">Registrar Vendas</a></li>
                <li><a class="dropdown-item" href="/projeto-vaga/pages/sales/list_sales.php">Listas Vendas</a></li>
            </ul>
        </li>
        <!-- RELATÓRIO -->
         <!-- 
        PERIODOS(MESES, SEMANAS, DIAS)
         VISUALIZAÇÃO DOS Itens
         GERAR RELATÓRIO 
         -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Relatórios
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="/projeto-vaga/pages/sales/add_sale.php">Registrar Vendas</a></li>
                <li><a class="dropdown-item" href="/projeto-vaga/pages/sales/list_sales.php">Listas Vendas</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="/projeto-vaga/access/logout.php">Sair</a>
        </li>
    </ul>
</div>
    </header>
    <main>
