<?php
namespace Core;

class Database {
    private $connection;
    
    public function __construct() {
        $db = new \Database();  // Usa la clase Database que ya creamos
        $this->connection = $db->getConnection();
    }
    
    public function getConnection() {
        return $this->connection;
    }
}