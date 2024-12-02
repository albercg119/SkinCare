<?php
namespace Controllers;

use Core\Controller;
use Services\SupplierService;

class SupplierController extends Controller {
    private $supplierService;

    public function __construct($db) {
        $this->supplierService = new SupplierService($db);
    }

    public function getAll() {
        try {
            $suppliers = $this->supplierService->getAll();
            return $this->response($suppliers);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function create($data) {
        try {
            if (!isset($data['nombre']) || !isset($data['telefono'])) {
                throw new \Exception('Faltan datos requeridos');
            }
            
            $supplier = $this->supplierService->create($data);
            return $this->response($supplier, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update($id, $data) {
        try {
            $supplier = $this->supplierService->update($id, $data);
            return $this->response($supplier);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $this->supplierService->delete($id);
            return $this->response(['message' => 'Proveedor eliminado exitosamente']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}