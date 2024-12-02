<?php
namespace Controllers;

use Core\Controller;
use Core\Service;

class InventoryController extends Controller {
    private $inventoryModel;
    private $service;

    public function __construct() {
        parent::__construct();
        $this->inventoryModel = new InventoryModel($this->db);
        $this->service = new Service();
    }

    public function index() {
        try {
            $results = $this->inventoryModel->read();
            $data = [
                'title' => 'Gestión de Inventario',
                'inventory' => $results
            ];
            $this->view('inventory/index', $data);
        } catch (Exception $e) {
            $this->service->respondWithError($e->getMessage());
        }
    }

    public function create() {
        try {
            $data = $this->service->getRequestData();
            
            if (!isset($data['id_producto']) || !isset($data['ubicacion_tienda'])) {
                throw new Exception('Datos incompletos');
            }

            $result = $this->inventoryModel->create($data);
            
            if ($result) {
                $this->service->respondWithSuccess('Registro de inventario creado exitosamente');
            } else {
                throw new Exception('Error al crear el registro de inventario');
            }
        } catch (Exception $e) {
            $this->service->respondWithError($e->getMessage());
        }
    }

    public function read($id = null) {
        try {
            if ($id === null) {
                $result = $this->inventoryModel->read();
            } else {
                $result = $this->inventoryModel->readSingle($id);
            }
            
            $this->service->respondWithData($result);
        } catch (Exception $e) {
            $this->service->respondWithError($e->getMessage());
        }
    }

    public function update($id) {
        try {
            $data = $this->service->getRequestData();
            
            if (!isset($data['id_producto']) || !isset($data['ubicacion_tienda'])) {
                throw new Exception('Datos incompletos');
            }

            $result = $this->inventoryModel->update($id, $data);
            
            if ($result) {
                $this->service->respondWithSuccess('Registro actualizado exitosamente');
            } else {
                throw new Exception('Error al actualizar el registro');
            }
        } catch (Exception $e) {
            $this->service->respondWithError($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $result = $this->inventoryModel->delete($id);
            
            if ($result) {
                $this->service->respondWithSuccess('Registro eliminado exitosamente');
            } else {
                throw new Exception('Error al eliminar el registro');
            }
        } catch (Exception $e) {
            $this->service->respondWithError($e->getMessage());
        }
    }
}