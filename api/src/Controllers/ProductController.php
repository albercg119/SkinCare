<?php
namespace Controllers;

use Core\Controller;
use Services\ProductService;

class ProductController extends Controller {
    private $productService;

    public function __construct($db) {
        $this->productService = new ProductService($db);
    }

    public function getAll() {
        try {
            $products = $this->productService->getAll();
            return $this->response($products);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function create($data) {
        try {
            if (!isset($data['nombre']) || !isset($data['precio'])) {
                throw new \Exception('Faltan datos requeridos');
            }
            
            $product = $this->productService->create($data);
            return $this->response($product, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update($id, $data) {
        try {
            $product = $this->productService->update($id, $data);
            return $this->response($product);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $this->productService->delete($id);
            return $this->response(['message' => 'Producto eliminado exitosamente']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}