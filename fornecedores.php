<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Fornecedor.php';

Auth::exigirLogin();

$erro = null;
$sucesso = null;
$editando = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? 'salvar';

    if ($acao === 'excluir') {
        Fornecedor::excluir((int) $_POST['id']);
        header('Location: fornecedores.php?sucesso=' . urlencode('Fornecedor excluído com sucesso.'));
        exit;
    }

    $nome = trim($_POST['nome'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '') ?: null;
    $email = trim($_POST['email'] ?? '') ?: null;
    $endereco = trim($_POST['endereco'] ?? '') ?: null;
    $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;

    if ($nome === '' || $cnpj === '') {
        $erro = 'Nome e CNPJ são obrigatórios.';
    } else {
        $fornecedor = new Fornecedor($nome, $cnpj, $telefone, $email, $endereco, $id);
        $fornecedor->salvar();
        header('Location: fornecedores.php?sucesso=' . urlencode('Fornecedor salvo com sucesso.'));
        exit;
    }
}

if (isset($_GET['editar'])) {
    $editando = Fornecedor::buscarPorId((int) $_GET['editar']);
}

$sucesso = $_GET['sucesso'] ?? null;
$fornecedores = Fornecedor::listarTodos();

$paginaTitulo = 'Fornecedores - Gestão de Produtos';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<div class="container">
    <h2 class="mb-4">Fornecedores</h2>

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
                    <h5 class="card-title"><?= $editando ? 'Editar Fornecedor' : 'Novo Fornecedor' ?></h5>
                    <form method="post">
                        <input type="hidden" name="acao" value="salvar">
                        <input type="hidden" name="id" value="<?= $editando ? $editando->id : '' ?>">
                        <div class="mb-2">
                            <label class="form-label">Nome</label>
                            <input type="text" class="form-control" name="nome" required
                                   value="<?= htmlspecialchars($editando->nome ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">CNPJ</label>
                            <input type="text" class="form-control" name="cnpj" required
                                   value="<?= htmlspecialchars($editando->cnpj ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="telefone"
                                   value="<?= htmlspecialchars($editando->telefone ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control" name="email"
                                   value="<?= htmlspecialchars($editando->email ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Endereço</label>
                            <input type="text" class="form-control" name="endereco"
                                   value="<?= htmlspecialchars($editando->endereco ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Salvar</button>
                        <?php if ($editando): ?>
                            <a href="fornecedores.php" class="btn btn-outline-secondary w-100 mt-2">Cancelar edição</a>
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
                            <th>CNPJ</th>
                            <th>Telefone</th>
                            <th>E-mail</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fornecedores as $f): ?>
                            <tr>
                                <td><?= htmlspecialchars($f->nome) ?></td>
                                <td><?= htmlspecialchars($f->cnpj) ?></td>
                                <td><?= htmlspecialchars($f->telefone ?? '-') ?></td>
                                <td><?= htmlspecialchars($f->email ?? '-') ?></td>
                                <td class="text-end">
                                    <a href="fornecedores.php?editar=<?= $f->id ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Excluir este fornecedor?');">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id" value="<?= $f->id ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$fornecedores): ?>
                            <tr><td colspan="5" class="text-center text-muted">Nenhum fornecedor cadastrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
