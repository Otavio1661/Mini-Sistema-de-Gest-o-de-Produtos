<?php

require_once __DIR__ . '/../config/Database.php';

class Fornecedor
{
    public ?int $id;
    public string $nome;
    public string $cnpj;
    public ?string $telefone;
    public ?string $email;
    public ?string $endereco;

    public function __construct(
        string $nome = '',
        string $cnpj = '',
        ?string $telefone = null,
        ?string $email = null,
        ?string $endereco = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->cnpj = $cnpj;
        $this->telefone = $telefone;
        $this->email = $email;
        $this->endereco = $endereco;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            $row['nome'],
            $row['cnpj'],
            $row['telefone'] ?? null,
            $row['email'] ?? null,
            $row['endereco'] ?? null,
            (int) $row['id']
        );
    }

    public function salvar(): int
    {
        $pdo = Database::getConnection();

        if ($this->id) {
            $stmt = $pdo->prepare(
                'UPDATE fornecedores SET nome = :nome, cnpj = :cnpj, telefone = :telefone, email = :email, endereco = :endereco WHERE id = :id'
            );
            $stmt->execute([
                ':nome' => $this->nome,
                ':cnpj' => $this->cnpj,
                ':telefone' => $this->telefone,
                ':email' => $this->email,
                ':endereco' => $this->endereco,
                ':id' => $this->id,
            ]);

            return $this->id;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO fornecedores (nome, cnpj, telefone, email, endereco) VALUES (:nome, :cnpj, :telefone, :email, :endereco)'
        );
        $stmt->execute([
            ':nome' => $this->nome,
            ':cnpj' => $this->cnpj,
            ':telefone' => $this->telefone,
            ':email' => $this->email,
            ':endereco' => $this->endereco,
        ]);

        $this->id = (int) $pdo->lastInsertId();

        return $this->id;
    }

    public static function excluir(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM fornecedores WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public static function buscarPorId(int $id): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM fornecedores WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? self::fromArray($row) : null;
    }

    public static function listarTodos(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT * FROM fornecedores ORDER BY nome ASC');

        return array_map(fn (array $row) => self::fromArray($row), $stmt->fetchAll());
    }

    /**
     * Valida o formato do CNPJ (14 dígitos, não permite sequências repetidas
     * como "00000000000000"). Não valida os dígitos verificadores para não
     * bloquear CNPJs de teste durante a demonstração/correção do trabalho.
     */
    public static function cnpjValido(string $cnpj): bool
    {
        $digitos = preg_replace('/\D/', '', $cnpj);

        return strlen($digitos) === 14 && !preg_match('/^(\d)\1{13}$/', $digitos);
    }

    /**
     * Impede excluir um fornecedor que ainda possui produtos vinculados
     * (fk_produto_fornecedor é ON DELETE RESTRICT).
     */
    public static function possuiProdutos(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT 1 FROM produtos WHERE fornecedor_id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        return (bool) $stmt->fetch();
    }
}
