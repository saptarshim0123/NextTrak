<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function emailExists($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT id from users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("User::emailExists Error: " . $e->getMessage());
            return false;
        }
    }

    public function create($first_name, $last_name, $email, $password_hash) {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$first_name, $last_name, $email, $password_hash]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("User::create Error: " . $e->getMessage());
        }
        return false;
    }

    public function getUserByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e){
            error_log("User::getUserByEmail Error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, first_name, last_name, email, created_at FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User::getUserById Error: " . $e->getMessage());
            return false;
        }
    }
}