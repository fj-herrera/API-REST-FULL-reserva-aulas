<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/reservaService.php';

class ReservaController extends \App\Controllers\BaseController {
    public function manejarGet($peticion) {
        $servicio = new ReservaService();
        return $this->GET($servicio, $peticion);
    }

    public function manejarPost($peticion) {
        $servicio = new ReservaService();
        return $this->Post($servicio, $peticion);
    }

    public function manejarPut($peticion) {
        $servicio = new ReservaService();
        return $this->Put($servicio, $peticion);
    }
}

// Uso procedural (si no usas instancias en el router):
function instanciarReservaController($peticion) {
    $metodo = $peticion->getMetodo();  
    if ($metodo === 'GET') {
        $controller = new ReservaController();
        $controller->manejarGet($peticion);
    }
    else if($metodo === 'POST') {
        $controller = new ReservaController();
        $controller->manejarPost($peticion);
    }
    else if($metodo === 'PUT') {
        $controller = new ReservaController();
        $controller->manejarPut($peticion);
    }
}

?>