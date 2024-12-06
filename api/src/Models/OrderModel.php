<?php
namespace Models;

use PDO;
use PDOException;
use Exception;

class OrderModel {
    private $conn;
    private $table = 'pedido';    

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($filters = []) {
        try {
            $query = "SELECT 
                        p.ID_Pedido as id,
                        p.Fecha_Pedido as fecha_pedido,
                        p.Estado_Pedido as estado_pedido,
                        p.ID_Proveedor as id_proveedor,
                        ROUND(COALESCE((
                            SELECT SUM(Precio_Total)
                            FROM detalle_pedido
                            WHERE ID_Pedido = p.ID_Pedido
                        ), 0), 2) as total
                    FROM {$this->table} p";
    
            $whereConditions = [];
            $params = [];
    
            if (!empty($filters['estado'])) {
                $whereConditions[] = "p.Estado_Pedido = :estado";
                $params[':estado'] = $filters['estado'];
            }
    
            if (!empty($filters['fecha'])) {
                $whereConditions[] = "DATE(p.Fecha_Pedido) = :fecha";
                $params[':fecha'] = $filters['fecha'];
            }
    
            if (!empty($whereConditions)) {
                $query .= " WHERE " . implode(' AND ', $whereConditions);
            }
    
            $query .= " ORDER BY p.ID_Pedido DESC";
    
            error_log("Query a ejecutar: " . $query);
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }
    
    public function update($id, $data) {
        try {
            $this->conn->beginTransaction();
    
            // Actualizar pedido principal
            $query = "UPDATE " . $this->table . "
                    SET Estado_Pedido = :estado_pedido,
                        ID_Proveedor = :id_proveedor
                    WHERE ID_Pedido = :id";
    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':estado_pedido', $data['estado_pedido']);
            $stmt->bindParam(':id_proveedor', $data['id_proveedor']);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
    
            if (isset($data['productos']) && is_array($data['productos'])) {
                // Primero eliminamos los detalles existentes
                $deleteDetalles = "DELETE FROM detalle_pedido WHERE ID_Pedido = :id_pedido";
                $stmtDelete = $this->conn->prepare($deleteDetalles);
                $stmtDelete->bindParam(':id_pedido', $id);
                $stmtDelete->execute();
    
                // Preparar la consulta para obtener el precio del producto
                $queryPrecio = "SELECT p.Precio 
                               FROM producto p 
                               JOIN inventario i ON p.ID_Producto = i.ID_Producto 
                               WHERE i.ID_Inventario = :id_inventario";
                $stmtPrecio = $this->conn->prepare($queryPrecio);
    
                // Insertar los nuevos detalles
                $queryDetalle = "INSERT INTO detalle_pedido 
                               (ID_Pedido, ID_Inventario, Cantidad, Precio_Total) 
                               VALUES 
                               (:ID_Pedido, :ID_Inventario, :Cantidad, :Precio_Total)";
                
                $stmtDetalle = $this->conn->prepare($queryDetalle);
                
                foreach ($data['productos'] as $producto) {
                    // Obtener el precio del producto
                    $stmtPrecio->bindValue(':id_inventario', $producto['id_inventario']);
                    $stmtPrecio->execute();
                    $precioData = $stmtPrecio->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$precioData) {
                        throw new Exception("No se encontró el precio para el producto");
                    }
    
                    $precio_total = $precioData['Precio'] * $producto['cantidad'];
    
                    // Insertar el detalle
                    $stmtDetalle->bindValue(':ID_Pedido', $id);
                    $stmtDetalle->bindValue(':ID_Inventario', $producto['id_inventario']);
                    $stmtDetalle->bindValue(':Cantidad', $producto['cantidad']);
                    $stmtDetalle->bindValue(':Precio_Total', $precio_total);
                    
                    if (!$stmtDetalle->execute()) {
                        throw new Exception("Error al insertar detalle del pedido");
                    }
                }
            }
    
            $this->conn->commit();
            return true;
    
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw new Exception("Error al actualizar el pedido: " . $e->getMessage());
        }
    }

    public function getDetails($orderId) {
        try {
            $query = "SELECT 
                        pr.Nombre as nombre,
                        dp.Cantidad as cantidad,
                        pr.Precio as precio,
                        (dp.Cantidad * pr.Precio) as subtotal
                    FROM detalle_pedido dp
                    JOIN inventario i ON dp.ID_Inventario = i.ID_Inventario
                    JOIN producto pr ON i.ID_Producto = pr.ID_Producto
                    WHERE dp.ID_Pedido = :orderId";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':orderId', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => [
                    'detalles' => array_map(function($row) {
                        return [
                            'nombre' => $row['nombre'],
                            'cantidad' => (int)$row['cantidad'],
                            'precio' => (float)$row['precio'],
                            'subtotal' => (float)$row['subtotal']
                        ];
                    }, $result),
                    'total' => number_format(array_reduce($result, function($carry, $item) {
                        return $carry + $item['subtotal'];
                    }, 0), 2, '.', '')
                ]
            ];
            
        } catch (PDOException $e) {
            throw new Exception("Error consultando detalles: " . $e->getMessage());
        }
    }

    public function readSingle($id)
{
    try {
        $query = "SELECT
                    p.ID_Pedido as id,
                    p.Fecha_Pedido as fecha_pedido,
                    p.Estado_Pedido as estado_pedido,
                    p.ID_Proveedor as id_proveedor,
                    COALESCE((
                        SELECT SUM(dp.Cantidad * pr.Precio)
                        FROM detalle_pedido dp
                        JOIN producto pr ON dp.ID_Inventario = pr.ID_Producto
                        WHERE dp.ID_Pedido = p.ID_Pedido
                    ), 0) as total,
                    prov.Nombre as proveedor_nombre
                FROM pedido p
                LEFT JOIN proveedor prov ON p.ID_Proveedor = prov.ID_Proveedor
                WHERE p.ID_Pedido = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt;
    } catch (PDOException $e) {
        throw new Exception("Error in the query: " . $e->getMessage());
    }
}

    public function create($data) {
        try {
            // Validaciones iniciales
            if (!isset($data['id_proveedor']) || !isset($data['estado_pedido']) || !isset($data['productos'])) {
                throw new Exception("Faltan datos requeridos");
            }

            // Verificar que productos sea un array y no esté vacío
            if (!is_array($data['productos']) || empty($data['productos'])) {
                throw new Exception("No hay productos especificados");
            }

            // Iniciar transacción
            $this->conn->beginTransaction();
    
            // 1. Insertar pedido principal
            $query = "INSERT INTO " . $this->table . " 
                    (Fecha_Pedido, ID_Proveedor, Estado_Pedido) 
                    VALUES 
                    (NOW(), :ID_Proveedor, :Estado_Pedido)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ID_Proveedor', $data['id_proveedor'], PDO::PARAM_INT);
            $stmt->bindValue(':Estado_Pedido', $data['estado_pedido'], PDO::PARAM_STR);
            $stmt->execute();
            
            $pedido_id = $this->conn->lastInsertId();

            // 2. Insertar detalles del pedido
            $queryDetalle = "INSERT INTO detalle_pedido 
                           (ID_Pedido, ID_Inventario, Cantidad, Precio_Total) 
                           VALUES 
                           (:ID_Pedido, :ID_Inventario, :Cantidad, :Precio_Total)";
            
            $stmtDetalle = $this->conn->prepare($queryDetalle);
            
            foreach ($data['productos'] as $producto) {
                // Verificar que el producto tenga los datos necesarios
                if (!isset($producto['id_inventario']) || !isset($producto['cantidad'])) {
                    throw new Exception("Datos de producto incompletos");
                }

                // Verificar que el inventario existe y obtener precio
                $queryVerificar = "SELECT i.ID_Inventario, p.Precio 
                                 FROM inventario i
                                 JOIN producto p ON i.ID_Producto = p.ID_Producto
                                 WHERE i.ID_Inventario = :id";
                
                $stmtVerificar = $this->conn->prepare($queryVerificar);
                $stmtVerificar->bindValue(':id', $producto['id_inventario'], PDO::PARAM_INT);
                $stmtVerificar->execute();
                
                $inventarioData = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
                
                if (!$inventarioData) {
                    throw new Exception("El inventario ID " . $producto['id_inventario'] . " no existe");
                }

                // Calcular precio total
                $precio_total = $inventarioData['Precio'] * $producto['cantidad'];
                
                // Insertar detalle
                $stmtDetalle->bindValue(':ID_Pedido', $pedido_id, PDO::PARAM_INT);
                $stmtDetalle->bindValue(':ID_Inventario', $producto['id_inventario'], PDO::PARAM_INT);
                $stmtDetalle->bindValue(':Cantidad', $producto['cantidad'], PDO::PARAM_INT);
                $stmtDetalle->bindValue(':Precio_Total', $precio_total, PDO::PARAM_STR);
                
                if (!$stmtDetalle->execute()) {
                    throw new Exception("Error al insertar detalle del pedido");
                }
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw new Exception($e->getMessage());
        }
    }    

    public function delete($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE ID_Pedido = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar pedido: " . $e->getMessage());
        }
    }  
}