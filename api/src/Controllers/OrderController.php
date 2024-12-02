<?php
namespace Controllers;

use Core\Controller;
use Services\OrderService;

class OrderController extends Controller {
    private $orderService;

    public function __construct($db) {
        $this->orderService = new OrderService($db);
    }

    public function getAll() {
        try {
            $orders = $this->orderService->getAll();
            return $this->response($orders);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getById($id) {
        try {
            $order = $this->orderService->getWithDetails($id);
            return $this->response($order);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function create($data) {
        try {
            if (!isset($data['id_proveedor']) || !isset($data['productos'])) {
                throw new \Exception('Faltan datos requeridos');
            }
            
            $order = $this->orderService->create($data);
            return $this->response($order, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update($id, $data) {
        try {
            $order = $this->orderService->update($id, $data);
            return $this->response($order);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $this->orderService->delete($id);
            return $this->response(['message' => 'Pedido eliminado exitosamente']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}