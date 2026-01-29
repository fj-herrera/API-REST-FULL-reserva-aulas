<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/profesorService.php';

class ProfesorController extends \App\Controllers\BaseController {
    public function manejarGet($peticion) {
        $servicio = new ProfesorService();
        return $this->GET($servicio, $peticion);
    }

    public function manejarPost($peticion) {
        $servicio = new ProfesorService();
        return $this->Post($servicio, $peticion);
    }
}

// Uso procedural (si no usas instancias en el router):
function instanciarProfesorController($peticion) {
    $metodo = $peticion->getMetodo();  
    if ($metodo === 'GET') {
        $controller = new ProfesorController();
        $controller->manejarGet($peticion);
    }
    else if($metodo === 'POST') {
        $controller = new ProfesorController();
        $controller->manejarPost($peticion);
    }
}

?>
