<?php
require_once __DIR__ . '/classes/Auth.php';
Auth::iniciarSessao();

if (Auth::usuarioLogado()) {
    header('Location: dashboard.php');
    exit;
}

$erro = null;
$sucesso = $_GET['sucesso'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    [$ok, $mensagem] = Auth::login($email, $senha);

    if ($ok) {
        header('Location: dashboard.php');
        exit;
    }

    $erro = $mensagem;
}

$paginaTitulo = 'Login - Gestão de Produtos';
require __DIR__ . '/includes/header.php';
?>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="card-title text-center mb-4">Entrar</h3>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>

                    <?php if ($sucesso): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label class="form-label" for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="senha">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Não tem conta? <a href="cadastro.php">Cadastre-se</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
