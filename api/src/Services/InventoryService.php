<?php
namespace Services;

use Core\Service;
use Models\InventoryModel;

class InventoryService extends Service {
    public function __construct($db) {
        parent::__construct(new InventoryModel($db));
    }

    public function create($data) {
        $this->validateInventoryData($data);
        return $this->model->create($data);
    }

    public function update($id, $data) {
        $this->validateInventoryData($data);
        return $this->model->update($id, $data);
    }

    private function validateInventoryData($data) {
        if (empty($data['id_producto'])) {
            throw new \Exception('El ID del producto es requerido');
        }
        if (empty($data['ubicacion_tienda'])) {
            throw new \Exception('La ubicación es requerida');
        }
    }
}