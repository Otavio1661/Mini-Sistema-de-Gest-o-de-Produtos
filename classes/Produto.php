<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Fornecedor.php';

class Produto
{
    public ?int $id;
    public string $nome;
    public ?string $descricao;
    public float $preco;
    public int $quantidadeEstoque;
    public int $fornecedorId;
    private ?Fornecedor $fornecedor = null;

    public function __construct(
        string $nome = '',
        ?string $descricao = null,
        float $preco = 0,
        int $quantidadeEstoque = 0,
        int $fornecedorId = 0,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->preco = $preco;
        $this->quantidadeEstoque = $quantidadeEstoque;
        $this->fornecedorId = $fornecedorId;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            $row['nome'],
            $row['descricao'] ?? null,
            (float) $row['preco'],
            (int) $row['quantidade_estoque'],
            (int) $row['fornecedor_id'],
            (int) $row['id']
        );
    }

    /**
     * Relacionamento Produto -> Fornecedor (N:1), carregado sob demanda.
     */
    public function getFornecedor(): ?Fornecedor
    {
        if ($this->fornecedor === null && $this->fornecedorId) {
            $this->fornecedor = Fornecedor::buscarPorId($this->fornecedorId);
        }

        return $this->fornecedor;
    }

    public function salvar(): int
    {
        $pdo = Database::getConnection();

        if ($this->id) {
            $stmt = $pdo->prepare(
                'UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, quantidade_estoque = :qtd, fornecedor_id = :fornecedor_id WHERE id = :id'
            );
            $stmt->execute([
                ':nome' => $this->nome,
                ':descricao' => $this->descricao,
                ':preco' => $this->preco,
                ':qtd' => $this->quantidadeEstoque,
                ':fornecedor_id' => $this->fornecedorId,
                ':id' => $this->id,
            ]);

            return $this->id;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO produtos (nome, descricao, preco, quantidade_estoque, fornecedor_id) VALUES (:nome, :descricao, :preco, :qtd, :fornecedor_id)'
        );
        $stmt->execute([
            ':nome' => $this->nome,
            ':descricao' => $this->descricao,
            ':preco' => $this->preco,
            ':qtd' => $this->quantidadeEstoque,
            ':fornecedor_id' => $this->fornecedorId,
        ]);

        $this->id = (int) $pdo->lastInsertId();

        return $this->id;
    }

    public static function excluir(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM produtos WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public static function buscarPorId(int $id): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? self::fromArray($row) : null;
    }

    /**
     * @return self[]
     */
    public static function listarTodos(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT * FROM produtos ORDER BY nome ASC');

        return array_map(fn (array $row) => self::fromArray($row), $stmt->fetchAll());
    }

    /**
     * Traz os produtos já com o nome do fornecedor (JOIN), útil para listagens.
     */
    public static function listarComFornecedor(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            'SELECT p.*, f.nome AS fornecedor_nome FROM produtos p
             INNER JOIN fornecedores f ON f.id = p.fornecedor_id
             ORDER BY p.nome ASC'
        );

        return $stmt->fetchAll();
    }
}
