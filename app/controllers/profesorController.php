<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/profesorService.php';

class ProfesorController extends \App\Controllers\BaseController {
    public function manejarGetProfesores($peticion) {
        $servicio = new ProfesorService();
        return $this->GET($servicio, $peticion);
    }
}

// Uso procedural (si no usas instancias en el router):
function instanciarProfesorController($peticion) {
    $controller = new ProfesorController();
    $controller->manejarGetProfesores($peticion);
}

?>
