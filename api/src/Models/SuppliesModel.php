<?php
class SuppliesModel {
    private $conn;
    private $table = 'supplies';
    
    // Propiedades que coinciden con la base de datos
    public $id;               // bigint AI PK
    public $article_id;       // bigint
    public $quantity;         // int
    public $supply_date;      // datetime

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        try {
            $query = "SELECT 
                        id,
                        article_id,
                        quantity,
                        supply_date
                     FROM " . $this->table . "
                     ORDER BY supply_date DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }

    public function create() {
        try {
            // Iniciar transacción
            $this->conn->beginTransaction();
    
            // Insertar el suministro
            $query = "INSERT INTO " . $this->table . " 
                    (article_id, quantity, supply_date) 
                    VALUES 
                    (:article_id, :quantity, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':article_id', $this->article_id, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
            
            if(!$stmt->execute()) {
                throw new Exception("Error al crear el suministro");
            }
    
            // Actualizar el inventario
            $query = "UPDATE producto 
                     SET Cantidad_En_Stock = Cantidad_En_Stock + :quantity 
                     WHERE ID_Producto = :article_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
            $stmt->bindParam(':article_id', $this->article_id, PDO::PARAM_INT);
            
            if(!$stmt->execute()) {
                throw new Exception("Error al actualizar el inventario");
            }
    
            // Confirmar transacción
            $this->conn->commit();
            return true;
    
        } catch (Exception $e) {
            // Revertir cambios si hay error
            $this->conn->rollBack();
            throw new Exception("Error en la transacción: " . $e->getMessage());
        }
    }
    
    private function validateData() {
        // Validar article_id
        if (!isset($this->article_id) || !is_numeric($this->article_id) || $this->article_id <= 0) {
            return false;
        }
        
        // Validar quantity
        if (!isset($this->quantity) || !is_numeric($this->quantity) || $this->quantity <= 0) {
            return false;
        }
        
        return true;
    }

    public function readOne() {
        try {
            $query = "SELECT id, article_id, quantity, supply_date 
                      FROM " . $this->table . "
                      WHERE id = ?";
    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            
            if($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($row) {
                    $this->id = $row['id'];
                    $this->article_id = $row['article_id'];
                    $this->quantity = $row['quantity'];
                    $this->supply_date = $row['supply_date'];
                    return true;
                }
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception("Error al leer el suministro: " . $e->getMessage());
        }
    }

    public function update() {
        try {
            // Obtener la cantidad anterior
            $query = "SELECT quantity, article_id FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al obtener el suministro original");
            }
            
            $old_supply = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$old_supply) {
                throw new Exception("No se encontró el suministro original");
            }
            
            // Calcular la diferencia
            $quantity_difference = $this->quantity - $old_supply['quantity'];
            
            // Iniciar transacción
            $this->conn->beginTransaction();
            
            // Actualizar suministro
            $query = "UPDATE " . $this->table . " SET article_id = :article_id, quantity = :quantity WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':article_id', $this->article_id, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el suministro");
            }
            
            // Actualizar stock del producto
            $query = "UPDATE producto SET cantidad_En_Stock = cantidad_En_Stock + :quantity_diff WHERE ID_Producto = :article_id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':quantity_diff', $quantity_difference, PDO::PARAM_INT);
            $stmt->bindParam(':article_id', $this->article_id, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el stock del producto");
            }
            
            // Confirmar transacción
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete() {
        try {
            $this->conn->beginTransaction();
    
            // Obtener la cantidad del suministro antes de eliminarlo
            $query = "SELECT quantity, article_id FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            $supply = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$supply) {
                throw new Exception("Suministro no encontrado");
            }
    
            // Eliminar el suministro
            $deleteQuery = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($deleteQuery);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            if(!$stmt->execute()) {
                throw new Exception("Error al eliminar el suministro");
            }
    
            // Actualizar el stock del producto
            $updateQuery = "UPDATE producto 
                           SET cantidad_En_Stock = cantidad_En_Stock - :quantity 
                           WHERE ID_Producto = :article_id";
            
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bindParam(':quantity', $supply['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':article_id', $supply['article_id'], PDO::PARAM_INT);
            
            if(!$stmt->execute()) {
                throw new Exception("Error al actualizar el stock");
            }
    
            $this->conn->commit();
            return true;
    
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log($e->getMessage());
            return false;
        }
    }

    // Método adicional para obtener suministros con información del artículo
    public function readWithArticleInfo() {
        try {
            $query = "SELECT 
                        s.id,
                        s.article_id,
                        s.quantity,
                        s.supply_date,
                        p.Nombre as article_name
                     FROM " . $this->table . " s
                     LEFT JOIN producto p ON s.article_id = p.ID_Producto
                     ORDER BY s.supply_date DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }
}