<?php

namespace App\Models;

use PDO;
use App\Databases\Mysql;

class FileModel
{
    private $mysql;
    private $connection;
    private $tableName = 'user_files';

    public function __construct()
    {
        $this->mysql = new Mysql();
        $this->connection = $this->mysql->connect();
    }

    public function create(array $data): bool
    {
        try {
            $columns = [];
            $placeholders = [];
            $params = [];

            foreach ($data as $key => $value) {
                $columns[] = $key;
                $placeholders[] = ":$key";
                $params[":$key"] = $value;
            }

            $query = "INSERT INTO {$this->tableName} (" . implode(", ", $columns) . ") 
                      VALUES (" . implode(", ", $placeholders) . ")";

            $stmt = $this->connection->prepare($query);

            foreach ($params as $placeholder => $value) {
                $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($placeholder, $value, $paramType);
            }

            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            error_log("Error: " . $e->getMessage());
            return false;
        }
    }

    public function select(): ?array
    {
        try {
            $query = "SELECT * FROM {$this->tableName} WHERE user_id = :id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":id", $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $query = "DELETE FROM {$this->tableName} WHERE id = :id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
}
