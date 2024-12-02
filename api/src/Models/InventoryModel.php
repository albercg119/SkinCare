<?php
class InventoryModel {
    private $conn;
    private $table = 'inventario';
    
    public $Id_Inventario;
    public $Id_Producto;
    public $Ubicacion_Tienda;
    public $Fecha_Ultima_Actualizacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        try {
            $query = "SELECT 
                        i.ID_Inventario, 
                        i.ID_Producto,
                        i.Ubicacion_Tienda,
                        i.Fecha_Ultima_Actualizacion,
                        p.Nombre as nombre_producto
                     FROM " . $this->table . " i
                     LEFT JOIN producto p ON i.ID_Producto = p.ID_Producto
                     ORDER BY i.ID_Inventario ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }

    public function create() {
        try {
            $query = "INSERT INTO " . $this->table . " 
                    (ID_Producto, Ubicacion_Tienda, Fecha_Ultima_Actualizacion) 
                    VALUES 
                    (:id_producto, :ubicacion_tienda, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            $this->ubicacion_tienda = htmlspecialchars(strip_tags($this->ubicacion_tienda));
            
            $stmt->bindParam(':id_producto', $this->id_producto);
            $stmt->bindParam(':ubicacion_tienda', $this->ubicacion_tienda);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al crear inventario: " . $e->getMessage());
        }
    }

    public function readOne() {
        try {
            $query = "SELECT 
                        i.ID_Inventario,
                        i.ID_Producto,
                        i.Ubicacion_Tienda,
                        i.Fecha_Ultima_Actualizacion,
                        p.Nombre as nombre_producto
                     FROM " . $this->table . " i
                     LEFT JOIN producto p ON i.ID_Producto = p.ID_Producto
                     WHERE i.ID_Inventario = :id
                     LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id_inventario);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row) {
                $this->id_inventario = $row['ID_Inventario'];
                $this->id_producto = $row['ID_Producto'];
                $this->ubicacion_tienda = $row['Ubicacion_Tienda'];
                $this->fecha_ultima_actualizacion = $row['Fecha_Ultima_Actualizacion'];
                return true;
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }

    public function update() {
        try {
            $query = "UPDATE " . $this->table . " 
                    SET 
                        ID_Producto = :id_producto,
                        Ubicacion_Tienda = :ubicacion_tienda,
                        Fecha_Ultima_Actualizacion = NOW()
                    WHERE ID_Inventario = :id_inventario";

            $stmt = $this->conn->prepare($query);

            $this->ubicacion_tienda = htmlspecialchars(strip_tags($this->ubicacion_tienda));
            
            $stmt->bindParam(':id_producto', $this->id_producto);
            $stmt->bindParam(':ubicacion_tienda', $this->ubicacion_tienda);
            $stmt->bindParam(':id_inventario', $this->id_inventario);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar inventario: " . $e->getMessage());
        }
    }

    public function delete() {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE ID_Inventario = :id_inventario";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_inventario', $this->id_inventario);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar inventario: " . $e->getMessage());
        }
    }
}