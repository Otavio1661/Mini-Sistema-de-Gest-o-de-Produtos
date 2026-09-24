<?php

require_once __DIR__ . '/Usuario.php';

class Auth
{
    public static function iniciarSessao(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function registrar(string $nome, string $email, string $senha): array
    {
        self::iniciarSessao();

        if (trim($nome) === '' || trim($email) === '' || trim($senha) === '') {
            return [false, 'Preencha todos os campos.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [false, 'E-mail inválido.'];
        }

        if (strlen($senha) < 6) {
            return [false, 'A senha deve ter ao menos 6 caracteres.'];
        }

        if (Usuario::emailExiste($email)) {
            return [false, 'Já existe um usuário cadastrado com este e-mail.'];
        }

        $usuario = new Usuario($nome, $email, Usuario::hashSenha($senha));
        $usuario->salvar();

        return [true, 'Cadastro realizado com sucesso. Faça login para continuar.'];
    }

    public static function login(string $email, string $senha): array
    {
        self::iniciarSessao();

        $usuario = Usuario::buscarPorEmail($email);

        if (!$usuario || !$usuario->verificarSenha($senha)) {
            return [false, 'E-mail ou senha inválidos.'];
        }

        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nome'] = $usuario->nome;

        return [true, 'Login realizado com sucesso.'];
    }

    public static function logout(): void
    {
        self::iniciarSessao();
        $_SESSION = [];
        session_destroy();
    }

    public static function usuarioLogado(): bool
    {
        self::iniciarSessao();

        return isset($_SESSION['usuario_id']);
    }

    public static function usuarioId(): ?int
    {
        self::iniciarSessao();

        return $_SESSION['usuario_id'] ?? null;
    }

    public static function usuarioNome(): ?string
    {
        self::iniciarSessao();

        return $_SESSION['usuario_nome'] ?? null;
    }

    public static function exigirLogin(): void
    {
        self::iniciarSessao();

        if (!self::usuarioLogado()) {
            header('Location: index.php');
            exit;
        }
    }
}
