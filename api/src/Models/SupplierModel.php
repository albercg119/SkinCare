<?php
class SupplierModel {
    private $conn;
    private $table = 'proveedor';

    // Propiedades del objeto
    public $id;
    public $nombre;
    public $telefono;
    public $correo_electronico;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        try {
            $query = "SELECT 
                        ID_Proveedor as id, 
                        Nombre as nombre, 
                        Telefono as telefono, 
                        Correo_Electronico as correo_electronico 
                     FROM " . $this->table;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }

     // Método para obtener un proveedor específico
     public function readOne() {
        try {
            $query = "SELECT 
                        ID_Proveedor as id, 
                        Nombre as nombre, 
                        Telefono as telefono, 
                        Correo_Electronico as correo_electronico
                     FROM " . $this->table . " 
                     WHERE ID_Proveedor = ?
                     LIMIT 0,1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row) {
                $this->nombre = $row['nombre'];
                $this->telefono = $row['telefono'];
                $this->correo_electronico = $row['correo_electronico'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }

    // Método para crear un nuevo proveedor
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table . "
                    (Nombre, Telefono, Correo_Electronico) 
                    VALUES (?, ?, ?)";

            $stmt = $this->conn->prepare($query);

            // Sanitización básica de datos
            $this->nombre = htmlspecialchars(strip_tags($this->nombre));
            $this->telefono = htmlspecialchars(strip_tags($this->telefono));
            $this->correo_electronico = htmlspecialchars(strip_tags($this->correo_electronico));

            $stmt->bindParam(1, $this->nombre);
            $stmt->bindParam(2, $this->telefono);
            $stmt->bindParam(3, $this->correo_electronico);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al crear: " . $e->getMessage());
        }
    }

    // Método para actualizar un proveedor existente
    public function update() {
        try {
            $query = "UPDATE " . $this->table . "
                    SET 
                        Nombre = ?,
                        Telefono = ?,
                        Correo_Electronico = ?
                    WHERE ID_Proveedor = ?";

            $stmt = $this->conn->prepare($query);

            // Sanitización básica de datos
            $this->nombre = htmlspecialchars(strip_tags($this->nombre));
            $this->telefono = htmlspecialchars(strip_tags($this->telefono));
            $this->correo_electronico = htmlspecialchars(strip_tags($this->correo_electronico));

            $stmt->bindParam(1, $this->nombre);
            $stmt->bindParam(2, $this->telefono);
            $stmt->bindParam(3, $this->correo_electronico);
            $stmt->bindParam(4, $this->id);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar: " . $e->getMessage());
        }
    }

    // Método para eliminar un proveedor
    public function delete() {
        try {
            $this->conn->beginTransaction();
            
            $query = "DELETE FROM " . $this->table . " WHERE ID_Proveedor = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);

            if($stmt->execute()) {
                $this->conn->commit();
                return true;
            }
            
            $this->conn->rollBack();
            return false;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error en delete(): " . $e->getMessage());
            throw new Exception("Error al eliminar el proveedor: " . $e->getMessage());
        }
    }
}