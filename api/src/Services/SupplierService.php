<?php
namespace Services;

use Core\Service;
use Models\SupplierModel;

class SupplierService extends Service {
    public function __construct($db) {
        parent::__construct(new SupplierModel($db));
    }

    public function create($data) {
        $this->validateSupplierData($data);
        return $this->model->create($data);
    }

    public function update($id, $data) {
        $this->validateSupplierData($data);
        return $this->model->update($id, $data);
    }

    private function validateSupplierData($data) {
        if (empty($data['nombre'])) {
            throw new \Exception('El nombre del proveedor es requerido');
        }
        if (empty($data['telefono'])) {
            throw new \Exception('El teléfono es requerido');
        }
        if (!empty($data['correo_electronico']) && !filter_var($data['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('El correo electrónico no es válido');
        }
    }
}