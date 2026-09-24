<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Produto.php';

Auth::exigirLogin();

$produtos = Produto::listarComFornecedor();

$paginaTitulo = 'Loja - Gestão de Produtos';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<div class="container">
    <h2 class="mb-1">Loja</h2>
    <p class="text-muted">Selecione um ou mais produtos e adicione à sua cesta.</p>

    <div id="alertaLoja"></div>

    <form id="formLoja">
        <div class="row g-3">
            <?php foreach ($produtos as $p): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input produto-checkbox" type="checkbox"
                                       value="<?= $p['id'] ?>" id="produto<?= $p['id'] ?>" name="produtos[]">
                                <label class="form-check-label fw-bold" for="produto<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nome']) ?>
                                </label>
                            </div>
                            <p class="card-text text-muted small mb-1"><?= htmlspecialchars($p['descricao'] ?? '') ?></p>
                            <p class="card-text small mb-1">Fornecedor: <?= htmlspecialchars($p['fornecedor_nome']) ?></p>
                            <p class="card-text small mb-1">Estoque: <?= (int) $p['quantidade_estoque'] ?></p>
                            <p class="card-text fs-5 fw-bold">R$ <?= number_format((float) $p['preco'], 2, ',', '.') ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (!$produtos): ?>
                <p class="text-muted">Nenhum produto disponível no momento.</p>
            <?php endif; ?>
        </div>

        <?php if ($produtos): ?>
            <button type="submit" id="btnAdicionarCesta" class="btn btn-primary mt-4" disabled>Adicionar selecionados à Cesta</button>
        <?php endif; ?>
    </form>
</div>
<script src="assets/js/loja.js"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
