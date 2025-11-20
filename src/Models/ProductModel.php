<?php

namespace App\Models;

use PDO;
use App\Databases\Mysql;

class ProductModel
{
    private $mysql;
    private $connection;
    private $tableName = 'kua';

    public function __construct()
    {
        $this->mysql = new Mysql();
        $this->connection = $this->mysql->connect();
    }

    public function store()
    {
        try {
            $query = "SELECT * FROM {$this->tableName} ORDER BY id DESC";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error: " . $e->getMessage());
            return false;
        }
    }

    public function select(string $nik)
    {
        try {
            $query = "SELECT * FROM {$this->tableName} WHERE lk_nik = :nik1 OR pr_nik = :nik2";

            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":nik1", $nik, PDO::PARAM_STR);
            $stmt->bindParam(":nik2", $nik, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result ?: null;
        } catch (\PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }


    public function create(array $data): bool
    {
        try {
            // Menyiapkan kolom dan nilai untuk query insert
            $columns = [];
            $placeholders = [];
            $params = [];

            // Menyusun kolom dan placeholder, serta menyiapkan data untuk bind
            foreach ($data as $key => $value) {
                $columns[] = $key;
                $placeholders[] = ":$key";
                $params[":$key"] = $value;
            }

            // Membuat query insert dinamis
            $query = "INSERT INTO {$this->tableName} (" . implode(", ", $columns) . ") 
                      VALUES (" . implode(", ", $placeholders) . ")";

            // Menyiapkan statement
            $stmt = $this->connection->prepare($query);

            // Bind parameter secara dinamis
            foreach ($params as $placeholder => $value) {
                // Menentukan tipe data secara otomatis berdasarkan jenis nilai
                $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($placeholder, $value, $paramType);
            }

            // Menjalankan query
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            error_log("Error: " . $e->getMessage());
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
                $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
            }
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Error Database: " . $e->getMessage());
            return false;
        }
    }
}
