<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/profesorService.php';

class ProfesorController extends \App\Controllers\BaseController {
    public function manejarGetProfesores($peticion,$parametros) {
        $servicio = new ProfesorService();
        return $this->GET($servicio, $peticion, $parametros);
    }
}

// Uso procedural (si no usas instancias en el router):
function manejarGetProfesores($peticion, $parametros) {
    $controller = new ProfesorController();
    $controller->manejarGetProfesores($peticion, $parametros);
}

?>
