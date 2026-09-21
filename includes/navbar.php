<?php
require_once __DIR__ . '/../classes/Auth.php';
Auth::iniciarSessao();
$rotaAtual = basename($_SERVER['SCRIPT_NAME']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Gestão de Produtos</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $rotaAtual === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $rotaAtual === 'produtos.php' ? 'active' : '' ?>" href="produtos.php">Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $rotaAtual === 'fornecedores.php' ? 'active' : '' ?>" href="fornecedores.php">Fornecedores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $rotaAtual === 'gerenciar.php' ? 'active' : '' ?>" href="gerenciar.php">Atualização (AJAX)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $rotaAtual === 'loja.php' ? 'active' : '' ?>" href="loja.php">Loja</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $rotaAtual === 'cesta.php' ? 'active' : '' ?>" href="cesta.php">Cesta</a>
                </li>
            </ul>
            <span class="navbar-text text-light me-3">
                Olá, <?= htmlspecialchars(Auth::usuarioNome() ?? '') ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Sair</a>
        </div>
    </div>
</nav>
