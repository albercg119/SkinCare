<?php
namespace Core;

abstract class Controller {
    protected $service;
    
    public function response($data, $status = 200) {
        http_response_code($status);
        return json_encode([
            'status' => $status < 300 ? 'success' : 'error',
            'data' => $data
        ]);
    }
    
    public function error($message, $status = 500) {
        http_response_code($status);
        return json_encode([
            'status' => 'error',
            'message' => $message
        ]);
    }
}