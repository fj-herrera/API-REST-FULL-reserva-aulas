<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/franjaService.php';

class FranjaController extends \App\Controllers\BaseController {
    public function manejarGet($peticion) {
        $servicio = new FranjaService();
        return $this->GET($servicio, $peticion);
    }
    
    public function manejarPost($peticion) {
        $servicio = new FranjaService();
        return $this->Post($servicio, $peticion);
    }
}

// Uso procedural (si no usas instancias en el router):
function instanciarFranjaController($peticion) {
    $metodo = $peticion->getMetodo();  
    if ($metodo === 'GET') {
        $controller = new FranjaController();
        $controller->manejarGet($peticion);
    }
    else if($metodo === 'POST') {
        $controller = new FranjaController();
        $controller->manejarPost($peticion);
    }
}

?>