<?php

class Company {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll() {
        try {
            $sql = "SELECT * FROM companies ORDER BY name ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Company::getAll Error: " . $e->getMessage());
            return [];
        }
    }
    
    public function search($search) {
        try {
            $sql = "SELECT * FROM companies WHERE name LIKE ? ORDER BY name ASC LIMIT 20";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['%' . $search . '%']);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Company::search Error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getById($id) {
        try {
            $sql = "SELECT * FROM companies WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Company::getById Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getByName($name) {
        try {
            $sql = "SELECT * FROM companies WHERE name = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Company::getByName Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function create($name, $website = null) {
        try {
            // Check if company already exists
            $existing = $this->getByName($name);
            if ($existing) {
                return $existing['id'];
            }
            
            $sql = "INSERT INTO companies (name, website) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $website]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Company::create Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getOrCreate($name, $website = null) {
        $existing = $this->getByName($name);
        if ($existing) {
            // Update website if company exists but doesn't have one, and a website is provided
            if (empty($existing['website']) && !empty($website)) {
                $this->updateWebsite($existing['id'], $website);
            }
            return $existing['id'];
        }
        return $this->create($name, $website);
    }
    
    public function updateWebsite($id, $website) {
        try {
            $sql = "UPDATE companies SET website = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$website, $id]);
        } catch (PDOException $e) {
            error_log("Company::updateWebsite Error: " . $e->getMessage());
            return false;
        }
    }
}