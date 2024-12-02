<?php
class Database {
    private $host = "localhost";
    private $database_name = "skincare";  // Verifica que este sea el nombre exacto
    private $username = "root";
    private $password = "Root1234";  // Si tienes contraseña, agrégala aquí
    public $conn;

    public function getConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database_name,
                $this->username,
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            return $this->conn;
        } catch(PDOException $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos");
        }
    }
}