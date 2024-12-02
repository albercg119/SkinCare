<?php
namespace Services;

use Core\Service;
use Models\ProductModel;

class ProductService extends Service {
    public function __construct($db) {
        parent::__construct(new ProductModel($db));
    }

    public function create($data) {
        $this->validateProductData($data);
        return $this->model->create($data);
    }

    public function update($id, $data) {
        $this->validateProductData($data);
        return $this->model->update($id, $data);
    }

    private function validateProductData($data) {
        if (empty($data['nombre'])) {
            throw new \Exception('El nombre del producto es requerido');
        }
        if (!isset($data['precio']) || $data['precio'] <= 0) {
            throw new \Exception('El precio debe ser mayor a 0');
        }
        if (isset($data['cantidad_stock']) && $data['cantidad_stock'] < 0) {
            throw new \Exception('La cantidad en stock no puede ser negativa');
        }
    }
}