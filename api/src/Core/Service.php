<?php
namespace Core;

abstract class Service {
    protected $model;
    
    public function __construct($model) {
        $this->model = $model;
    }
    
    public function getAll() {
        return $this->model->findAll();
    }
    
    public function getById($id) {
        return $this->model->findById($id);
    }
    
    public function delete($id) {
        return $this->model->delete($id);
    }
}