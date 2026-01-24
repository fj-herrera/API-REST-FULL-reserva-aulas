<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/profesorService.php';

class ProfesorController extends \App\Controllers\BaseController {
    public function manejarGetProfesores($peticion) {
        $servicio = new ProfesorService();
        return $this->manejarPeticionGET($servicio, $peticion);
    }
}

// Uso procedural (si no usas instancias en el router):
function manejarGetProfesores($peticion) {
    $controller = new ProfesorController();
    $controller->manejarGetProfesores($peticion);
}

?>
