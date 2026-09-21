<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Cesta.php';

Auth::exigirLogin();

$cesta = Cesta::obterOuCriarAberta(Auth::usuarioId());

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'remover') {
    $cesta->removerProduto((int) $_POST['produto_id']);
    header('Location: cesta.php');
    exit;
}

$itens = $cesta->getItens();
$total = $cesta->getTotal();

$paginaTitulo = 'Cesta - Gestão de Produtos';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<div class="container">
    <h2 class="mb-4">Sua Cesta</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-bg-secondary">
                <div class="card-body">
                    <h6 class="card-title">Produtos selecionados</h6>
                    <p class="display-6 mb-0"><?= count($itens) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h6 class="card-title">Valor total</h6>
                    <p class="display-6 mb-0">R$ <?= number_format($total, 2, ',', '.') ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr><th>Produto</th><th>Preço</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $produto): ?>
                    <tr>
                        <td><?= htmlspecialchars($produto->nome) ?></td>
                        <td>R$ <?= number_format($produto->preco, 2, ',', '.') ?></td>
                        <td class="text-end">
                            <form method="post" onsubmit="return confirm('Remover este item da cesta?');">
                                <input type="hidden" name="acao" value="remover">
                                <input type="hidden" name="produto_id" value="<?= $produto->id ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$itens): ?>
                    <tr><td colspan="3" class="text-center text-muted">Sua cesta está vazia. <a href="loja.php">Ir à loja</a>.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
