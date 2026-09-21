<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Produto.php';
require_once __DIR__ . '/classes/Fornecedor.php';

Auth::exigirLogin();

$erro = null;
$sucesso = null;
$editando = null;
$fornecedores = Fornecedor::listarTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? 'salvar';

    if ($acao === 'excluir') {
        Produto::excluir((int) $_POST['id']);
        header('Location: produtos.php?sucesso=' . urlencode('Produto excluído com sucesso.'));
        exit;
    }

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '') ?: null;
    $preco = (float) str_replace(',', '.', $_POST['preco'] ?? '0');
    $quantidade = (int) ($_POST['quantidade_estoque'] ?? 0);
    $fornecedorId = (int) ($_POST['fornecedor_id'] ?? 0);
    $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;

    if ($nome === '' || $fornecedorId <= 0) {
        $erro = 'Nome e fornecedor são obrigatórios.';
    } elseif (!$fornecedores) {
        $erro = 'Cadastre um fornecedor antes de cadastrar produtos.';
    } else {
        $produto = new Produto($nome, $descricao, $preco, $quantidade, $fornecedorId, $id);
        $produto->salvar();
        header('Location: produtos.php?sucesso=' . urlencode('Produto salvo com sucesso.'));
        exit;
    }
}

if (isset($_GET['editar'])) {
    $editando = Produto::buscarPorId((int) $_GET['editar']);
}

$sucesso = $_GET['sucesso'] ?? null;
$produtos = Produto::listarComFornecedor();

$paginaTitulo = 'Produtos - Gestão de Produtos';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<div class="container">
    <h2 class="mb-4">Produtos</h2>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?= $editando ? 'Editar Produto' : 'Novo Produto' ?></h5>
                    <form method="post">
                        <input type="hidden" name="acao" value="salvar">
                        <input type="hidden" name="id" value="<?= $editando->id ?? '' ?>">
                        <div class="mb-2">
                            <label class="form-label">Nome</label>
                            <input type="text" class="form-control" name="nome" required
                                   value="<?= htmlspecialchars($editando->nome ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control" name="descricao"
                                   value="<?= htmlspecialchars($editando->descricao ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="preco" required
                                   value="<?= htmlspecialchars($editando->preco ?? '0') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Quantidade em estoque</label>
                            <input type="number" min="0" class="form-control" name="quantidade_estoque" required
                                   value="<?= htmlspecialchars($editando->quantidadeEstoque ?? '0') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fornecedor</label>
                            <select class="form-select" name="fornecedor_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($fornecedores as $f): ?>
                                    <option value="<?= $f->id ?>"
                                        <?= (isset($editando) && $editando->fornecedorId === $f->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($f->nome) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" <?= $fornecedores ? '' : 'disabled' ?>>Salvar</button>
                        <?php if (!$fornecedores): ?>
                            <div class="form-text text-danger">Cadastre um fornecedor primeiro.</div>
                        <?php endif; ?>
                        <?php if ($editando): ?>
                            <a href="produtos.php" class="btn btn-outline-secondary w-100 mt-2">Cancelar edição</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Fornecedor</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nome']) ?></td>
                                <td><?= htmlspecialchars($p['fornecedor_nome']) ?></td>
                                <td>R$ <?= number_format((float) $p['preco'], 2, ',', '.') ?></td>
                                <td><?= (int) $p['quantidade_estoque'] ?></td>
                                <td class="text-end">
                                    <a href="produtos.php?editar=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Excluir este produto?');">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$produtos): ?>
                            <tr><td colspan="5" class="text-center text-muted">Nenhum produto cadastrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
