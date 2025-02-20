<?php

namespace Model;

use PDO;

class Model
{
    protected $pdo;
    protected $table;

    public function __construct(string $table)
    {
        $this->table = $table;
        $this->pdo = new PDO("mysql:host=localhost;dbname=lk_test_db;charset=utf8mb4", 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function tableExists(): bool
    {
        $stmt = $this->pdo->query("SHOW TABLES LIKE '{$this->table}'");
        return $stmt->rowCount() > 0;
    }

    protected function createTable()
    {

    }

    public function insert(array $data)
    {
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values});";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
    }

    public function read(array $options = [])
    {
        $query = "SELECT * FROM {$this->table}";

        if (array_key_exists("where", $options)) {
            foreach ($options['where'] as $field => $value) {
                if ($field === array_key_first($options['where'])) {
                    $query .= " WHERE {$field} {$value}";
                } else {
                    $query .= " AND {$field} {$value}";
                }
            }
        }

        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data)
    {
        // TODO: Update the existing data in the case we need to edit the data manually
    }

    public function delete(?int $id = null)
    {
        $query = "DELETE FROM {$this->table}";

        if ($id) {
            $query = $query ." WHERE id = $id";
        }

        $this->pdo->exec($query);
    }

    public function dropTable()
    {
        $this->pdo->exec("DROP TABLE IF EXISTS {$this->table}");
    }
}