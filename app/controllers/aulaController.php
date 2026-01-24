<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/aulaService.php';

class AulaController extends \App\Controllers\BaseController {
    public function manejarGetAulas($peticion) {
        $servicio = new AulaService();
        return $this->manejarPeticionGET($servicio, $peticion);
    }
}

// Uso procedural (si no usas instancias en el router):
function manejarGetAulas($peticion) {
    $controller = new AulaController();
    $controller->manejarGetAulas($peticion);
}

?>