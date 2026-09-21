<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Produto.php';
require_once __DIR__ . '/classes/Fornecedor.php';
require_once __DIR__ . '/classes/Cesta.php';

Auth::exigirLogin();

$totalProdutos = count(Produto::listarTodos());
$totalFornecedores = count(Fornecedor::listarTodos());
$cesta = Cesta::obterOuCriarAberta(Auth::usuarioId());
$totalItensCesta = $cesta->getQuantidadeItens();

$paginaTitulo = 'Início - Gestão de Produtos';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<div class="container">
    <h2 class="mb-4">Bem-vindo, <?= htmlspecialchars(Auth::usuarioNome()) ?>!</h2>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card text-bg-primary shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Produtos cadastrados</h5>
                    <p class="display-6 mb-0"><?= $totalProdutos ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Fornecedores cadastrados</h5>
                    <p class="display-6 mb-0"><?= $totalFornecedores ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-warning shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Itens na sua cesta</h5>
                    <p class="display-6 mb-0"><?= $totalItensCesta ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-3">
            <a href="produtos.php" class="btn btn-outline-primary w-100 py-3">Cadastrar Produtos</a>
        </div>
        <div class="col-md-3">
            <a href="fornecedores.php" class="btn btn-outline-success w-100 py-3">Cadastrar Fornecedores</a>
        </div>
        <div class="col-md-3">
            <a href="loja.php" class="btn btn-outline-secondary w-100 py-3">Ir à Loja</a>
        </div>
        <div class="col-md-3">
            <a href="cesta.php" class="btn btn-outline-warning w-100 py-3">Ver Cesta</a>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
