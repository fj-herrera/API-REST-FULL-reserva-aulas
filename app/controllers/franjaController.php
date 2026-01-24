<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/franjaService.php';

class FranjaController extends \App\Controllers\BaseController {
    public function manejarGetFranjas($peticion) {
        $servicio = new FranjaService();
        return $this->manejarPeticionGET($servicio, $peticion);
    }
}

// Uso procedural (si no usas instancias en el router):
function manejarGetFranjas($peticion) {
    $controller = new FranjaController();
    $controller->manejarGetFranjas($peticion);
}

?>