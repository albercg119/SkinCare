<?php

class ProductModel {
    private $conn;
    private $table = 'producto';
    
    // Propiedades públicas
    public $id;
    public $nombre;
    public $marca;
    public $precio;
    public $cantidad_stock;
    public $ubicacion;  
    public $id_inventario;
    

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        try {
            $query = "SELECT 
                        p.ID_Producto as id, 
                        p.Nombre as nombre, 
                        p.Marca as marca, 
                        p.Precio as precio, 
                        p.Cantidad_En_Stock as cantidad_stock,
                        i.ID_Inventario as id_inventario 
                     FROM " . $this->table . " p
                     LEFT JOIN inventario i ON p.ID_Producto = i.ID_Producto";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }

    // Método create con los nombres de columnas correctos
    public function create() {
        try {
            $this->conn->beginTransaction();
    
            // 1. Insertar producto
            $query = "INSERT INTO " . $this->table . " 
                    (Nombre, Marca, Precio, Cantidad_En_Stock) 
                    VALUES 
                    (:nombre, :marca, :precio, :cantidad_stock)";
            
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $this->nombre = htmlspecialchars(strip_tags($this->nombre));
            $this->marca = htmlspecialchars(strip_tags($this->marca));
            $this->precio = floatval($this->precio);
            $this->cantidad_stock = intval($this->cantidad_stock);
            
            // Vincular valores
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':marca', $this->marca);
            $stmt->bindParam(':precio', $this->precio);
            $stmt->bindParam(':cantidad_stock', $this->cantidad_stock);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al crear el producto");
            }
            
            $producto_id = $this->conn->lastInsertId();
    
            // 2. Insertar en inventario
            $query_inv = "INSERT INTO inventario 
                         (ID_Producto, Ubicacion_Tienda, Fecha_Ultima_Actualizacion) 
                         VALUES 
                         (:id_producto, :ubicacion, NOW())";
            
            $stmt_inv = $this->conn->prepare($query_inv);
            $stmt_inv->bindParam(':id_producto', $producto_id);
            $stmt_inv->bindParam(':ubicacion', $this->ubicacion);
            
            if (!$stmt_inv->execute()) {
                throw new Exception("Error al crear el registro de inventario");
            }
    
            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new Exception("Error en la transacción: " . $e->getMessage());
        }
    }

    // Método update con los nombres de columnas correctos
    public function update($id) {
        try {
            $this->conn->beginTransaction();
            
            // Actualizar producto
            $query = "UPDATE " . $this->table . " 
                    SET Nombre = :nombre,
                        Marca = :marca,
                        Precio = :precio,
                        Cantidad_En_Stock = :cantidad_stock
                    WHERE ID_Producto = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $this->nombre = htmlspecialchars(strip_tags($this->nombre));
            $this->marca = htmlspecialchars(strip_tags($this->marca));
            $this->precio = floatval($this->precio);
            $this->cantidad_stock = intval($this->cantidad_stock);
            
            // Vincular valores
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':marca', $this->marca);
            $stmt->bindParam(':precio', $this->precio);
            $stmt->bindParam(':cantidad_stock', $this->cantidad_stock);
            $stmt->bindParam(':id', $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el producto");
            }
    
            // Si hay ubicación, actualizar también el inventario
            if ($this->ubicacion) {
                $query_inv = "UPDATE inventario 
                             SET Ubicacion_Tienda = :ubicacion,
                                 Fecha_Ultima_Actualizacion = NOW()
                             WHERE ID_Producto = :id";
                
                $stmt_inv = $this->conn->prepare($query_inv);
                $stmt_inv->bindParam(':ubicacion', $this->ubicacion);
                $stmt_inv->bindParam(':id', $id);
                
                if (!$stmt_inv->execute()) {
                    throw new Exception("Error al actualizar el inventario");
                }
            }
            
            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new Exception("Error al actualizar producto: " . $e->getMessage());
        }
    }

    // Método delete con el nombre correcto de la columna ID
    public function delete($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE ID_Producto = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar producto: " . $e->getMessage());
        }
    }

    public function readOne() {
        try {
            $query = "SELECT 
                        p.ID_Producto as id,
                        p.Nombre as nombre, 
                        p.Marca as marca, 
                        p.Precio as precio, 
                        p.Cantidad_En_Stock as cantidad_stock,
                        i.ID_Inventario as id_inventario,
                        i.Ubicacion_Tienda as ubicacion
                     FROM " . $this->table . " p
                     LEFT JOIN inventario i ON p.ID_Producto = i.ID_Producto
                     WHERE p.ID_Producto = :id 
                     LIMIT 1";
    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
    
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->id = $row['id'];
                $this->nombre = $row['nombre'];
                $this->marca = $row['marca'];
                $this->precio = $row['precio'];
                $this->cantidad_stock = $row['cantidad_stock'];
                $this->id_inventario = $row['id_inventario'];
                $this->ubicacion = $row['ubicacion'];
                return true;
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }

    public function updateStock($id, $cantidad) {
        try {
            $query = "UPDATE " . $this->table . " 
                    SET Cantidad_En_Stock = Cantidad_En_Stock - :cantidad 
                    WHERE ID_Producto = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':cantidad', $cantidad);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar stock: " . $e->getMessage());
        }
    }

    public function readWithInventory() {
        try {
            $query = "SELECT 
                        i.ID_Inventario as id_inventario,
                        p.ID_Producto as id_producto,
                        p.Nombre as nombre,
                        p.Marca as marca,
                        p.Precio as precio,
                        p.Cantidad_En_Stock as cantidad_stock,
                        i.Ubicacion_Tienda as ubicacion
                      FROM " . $this->table . " p
                      JOIN inventario i ON p.ID_Producto = i.ID_Producto
                      WHERE p.Cantidad_En_Stock > 0
                      ORDER BY p.Nombre ASC";
                      
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error al consultar productos: " . $e->getMessage());
        }
    }
}