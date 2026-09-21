<?php

require_once __DIR__ . '/../config/Database.php';

class Usuario
{
    public ?int $id;
    public string $nome;
    public string $email;
    private ?string $senhaHash;

    public function __construct(string $nome = '', string $email = '', ?string $senhaHash = null, ?int $id = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senhaHash = $senhaHash;
    }

    public static function fromArray(array $row): self
    {
        return new self($row['nome'], $row['email'], $row['senha_hash'], (int) $row['id']);
    }

    /**
     * Requisito do trabalho: senha armazenada com hash SHA-256.
     */
    public static function hashSenha(string $senha): string
    {
        return hash('sha256', $senha);
    }

    public function verificarSenha(string $senha): bool
    {
        return hash_equals($this->senhaHash ?? '', self::hashSenha($senha));
    }

    public static function emailExiste(string $email): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
        $stmt->execute([':email' => $email]);

        return (bool) $stmt->fetch();
    }

    public static function buscarPorEmail(string $email): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();

        return $row ? self::fromArray($row) : null;
    }

    public static function buscarPorId(int $id): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? self::fromArray($row) : null;
    }

    public function salvar(): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO usuarios (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)'
        );
        $stmt->execute([
            ':nome' => $this->nome,
            ':email' => $this->email,
            ':senha_hash' => $this->senhaHash,
        ]);

        $this->id = (int) $pdo->lastInsertId();

        return $this->id;
    }
}
