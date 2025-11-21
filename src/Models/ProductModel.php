<?php

namespace App\Models;

use PDO;
use App\Databases\Mysql;

class ProductModel
{
    private $connection;
    private string $tableName = 'kua';

    public function __construct()
    {
        $mysql = new Mysql();
        $this->connection = $mysql->connect();
    }

    public function store(): ?array
    {
        try {
            $query = "SELECT * FROM {$this->tableName} ORDER BY id DESC";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            error_log("ProductModel.store Error: " . $e->getMessage());
            return null;
        }
    }

    public function select(string $nik): ?array
    {
        try {
            $query = "SELECT * FROM {$this->tableName} WHERE lk_nik = :nik1 OR pr_nik = :nik2";

            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":nik1", $nik, PDO::PARAM_STR);
            $stmt->bindParam(":nik2", $nik, PDO::PARAM_STR);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("ProductModel.select Error: " . $e->getMessage());
            return null;
        }
    }

    public function selectByID(?int $pid): ?array
    {
        // Tangani NULL lebih awal biar tidak fatal error
        if ($pid === null || $pid <= 0) {
            return null;
        }

        try {
            $query = "SELECT * FROM {$this->tableName} WHERE id = :id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":id", $pid, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("ProductModel.selectByID Error: " . $e->getMessage());
            return null;
        }
    }

    public function getLastID(): int
    {
        try {
            $query = "SELECT MAX(id) as max_id FROM {$this->tableName}";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return isset($result['max_id']) ? (int) $result['max_id'] : 0;
        } catch (\Exception $e) {
            error_log("ProductModel.getLastID Error: " . $e->getMessage());
            return 0;
        }
    }

    public function create(array $data): bool
    {
        try {
            $columns = array_keys($data);
            $placeholders = array_map(fn($key) => ":$key", $columns);

            $query = "INSERT INTO {$this->tableName} (" . implode(", ", $columns) . ") 
                      VALUES (" . implode(", ", $placeholders) . ")";

            $stmt = $this->connection->prepare($query);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("ProductModel.create Error: " . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, array $params): bool
    {
        try {
            $setClause = [];
            foreach ($params as $key => $value) {
                $setClause[] = "$key = :$key";
            }

            $query = "UPDATE {$this->tableName} SET " . implode(", ", $setClause) . " WHERE id = :id";
            $stmt = $this->connection->prepare($query);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("ProductModel.update Error: " . $e->getMessage());
            return false;
        }
    }
}
