<?php

namespace App\Models;

use PDO;
use App\Databases\Mysql;

class UserModel
{
    private PDO $connection;
    private string $tableName = 'user_catin';

    public function __construct()
    {
        $this->connection = (new Mysql())->connect();
    }

    public function show(): ?array
    {
        try {
            $query = "SELECT * FROM {$this->tableName} ORDER BY id DESC";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result ?: null; // Kembalikan null jika tidak ada hasil
        } catch (\PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }

    public function select(string $nik): ?array
    {
        try {
            $query = "SELECT * FROM {$this->tableName} WHERE user_nik = :nik";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":nik", $nik, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result ?: null; // Kembalikan null jika tidak ada hasil
        } catch (\PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }

    public function selectByID(): ?array
    {
        try {
            $query = "SELECT * FROM {$this->tableName} WHERE id = :id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":id", $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result ?: null; // Kembalikan null jika tidak ada hasil
        } catch (\PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $params): bool
    {
        try {
            if (empty($params->phone) || empty($params->nik) || empty($params->password)) {
                throw new \Exception("Kolom wajib tidak boleh kosong.");
            }

            $query = "INSERT INTO {$this->tableName} (user_nik, user_password, user_phone) 
                      VALUES (:user_nik, :user_password, :user_phone)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(":user_nik", $params->nik, PDO::PARAM_STR);
            $stmt->bindParam(":user_password", $params->password, PDO::PARAM_STR); // Gunakan password yang sudah di-hash
            $stmt->bindParam(":user_phone", $params->phone, PDO::PARAM_STR);
            return $stmt->execute();
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
