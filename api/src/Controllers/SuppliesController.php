<?php
namespace Controllers;

use Exception;
use Models\SuppliesModel;
use Core\Controller;
use Core\Service;

class SuppliesController extends Controller {
    private $suppliesModel;
    private $service;

    public function __construct() {
        parent::__construct();
        $this->suppliesModel = new SuppliesModel($this->db);
        $this->service = new Service();
    }

    public function index() {
        try {
            $results = $this->suppliesModel->read();
            $data = [
                'title' => 'Gestión de Suministros',
                'supplies' => $results
            ];
            $this->view('supplies/index', $data);
        } catch (Exception $e) {
            $this->service->respondWithError($e->getMessage());
        }
    }

    public function create() {
        try {
            $data = $this->service->getRequestData();
            
            if (!isset($data['article_id']) || !isset($data['quantity'])) {
                throw new Exception('Datos incompletos');
            }

            $result = $this->suppliesModel->create($data);
            
            if ($result) {
                $this->service->respondWithSuccess('Suministro creado exitosamente');
            } else {
                throw new Exception('Error al crear el suministro');
            }
        } catch (Exception $e) {
            $this->service->respondWithError($e->getMessage());
        }
    }

    public function read($id = null) {
        try {
            if ($id === null) {
                $result = $this->suppliesModel->read();
            } else {
                $result = $this->suppliesModel->readSingle($id);
            }
            
            $this->service->respondWithData($result);
        } catch (Exception $e) {
            $this->service->respondWithError($e->getMessage());
        }
    }

    public function update($id) {
        try {
            $data = $this->service->getRequestData();
            
            if (!isset($data['article_id']) || !isset($data['quantity'])) {
                throw new Exception('Datos incompletos');
            }

            $result = $this->suppliesModel->update($id, $data);
            
            if ($result) {
                $this->service->respondWithSuccess('Suministro actualizado exitosamente');
            } else {
                throw new Exception('Error al actualizar el suministro');
            }
        } catch (Exception $e) {
            $this->service->respondWithError($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $result = $this->suppliesModel->delete($id);
            
            if ($result) {
                $this->service->respondWithSuccess('Suministro eliminado exitosamente');
            } else {
                throw new Exception('Error al eliminar el suministro');
            }
        } catch (Exception $e) {
            $this->service->respondWithError($e->getMessage());
        }
    }
}