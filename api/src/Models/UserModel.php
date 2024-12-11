<?php
namespace Models;

class UserModel {
    private $db;
    private $table = 'usuario';

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                 (username, email, password, created_at) 
                 VALUES (?, ?, ?, NOW())";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            $data['username'],
            $data['email'],
            $data['password']
        ]);
    }

    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        
        return $stmt->rowCount() > 0;
    }
}