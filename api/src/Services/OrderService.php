<?php
namespace Services;

use Core\Service;
use Models\OrderModel;

class OrderService extends Service {
    public function __construct($db) {
        parent::__construct(new OrderModel($db));
    }

    public function create($data) {
        $this->validateOrderData($data);
        return $this->model->create($data);
    }

    public function update($id, $data) {
        $this->validateOrderUpdateData($data);
        return $this->model->update($id, $data);
    }

    public function getWithDetails($id) {
        return $this->model->getWithDetails($id);
    }

    private function validateOrderData($data) {
        if (empty($data['id_proveedor'])) {
            throw new \Exception('El ID del proveedor es requerido');
        }
        if (empty($data['productos']) || !is_array($data['productos'])) {
            throw new \Exception('Se requiere al menos un producto');
        }
        foreach ($data['productos'] as $producto) {
            if (empty($producto['id_producto']) || empty($producto['cantidad']) || $producto['cantidad'] <= 0) {
                throw new \Exception('Datos de producto inválidos');
            }
        }
    }

    private function validateOrderUpdateData($data) {
        if (empty($data['estado_pedido'])) {
            throw new \Exception('El estado del pedido es requerido');
        }
        if (!in_array($data['estado_pedido'], ['pendiente', 'enviado', 'entregado', 'cancelado'])) {
            throw new \Exception('Estado de pedido inválido');
        }
    }
}