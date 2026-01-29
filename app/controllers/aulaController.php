<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/aulaService.php';

class AulaController extends \App\Controllers\BaseController {
    public function manejarGet($peticion) {
        $servicio = new AulaService();
        return $this->Get($servicio, $peticion);
    }

    public function manejarPost($peticion) {
        $servicio = new AulaService();
        return $this->Post($servicio, $peticion);
    }
}

// Uso procedural (si no usas instancias en el router):
function instanciarAulaController($peticion) {
    $metodo = $peticion->getMetodo();  
    if ($metodo === 'GET') {
        $controller = new AulaController();
        $controller->manejarGet($peticion);
    }
    else if($metodo === 'POST') {
        $controller = new AulaController();
        $controller->manejarPost($peticion);
    }
   
}

?>