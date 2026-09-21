<?php
require_once __DIR__ . '/classes/Auth.php';
Auth::iniciarSessao();

if (Auth::usuarioLogado()) {
    header('Location: dashboard.php');
    exit;
}

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($senha !== $confirmarSenha) {
        $erro = 'As senhas não coincidem.';
    } else {
        [$ok, $mensagem] = Auth::registrar($nome, $email, $senha);

        if ($ok) {
            header('Location: index.php?sucesso=' . urlencode($mensagem));
            exit;
        }

        $erro = $mensagem;
    }
}

$paginaTitulo = 'Cadastro - Gestão de Produtos';
require __DIR__ . '/includes/header.php';
?>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="card-title text-center mb-4">Criar Conta</h3>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label class="form-label" for="nome">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required
                                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="senha">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" minlength="6" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="confirmar_senha">Confirmar senha</label>
                            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" minlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Já tem conta? <a href="index.php">Entrar</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
