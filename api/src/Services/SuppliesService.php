<?php
namespace Services;

use Core\Service;
use Models\SuppliesModel;

class SuppliesService extends Service {
    public function __construct($db) {
        parent::__construct(new SuppliesModel($db));
    }

    public function create($data) {
        $this->validateSupplyData($data);
        return $this->model->create($data);
    }

    public function update($id, $data) {
        $this->validateSupplyData($data);
        return $this->model->update($id, $data);
    }

    private function validateSupplyData($data) {
        if (empty($data['article_id'])) {
            throw new \Exception('El ID del artículo es requerido');
        }
        if (!isset($data['quantity']) || $data['quantity'] <= 0) {
            throw new \Exception('La cantidad debe ser mayor a 0');
        }
    }
}