<?php
include_once __DIR__ . '/BaseService.php';

class ReservaService extends \App\Services\BaseService {
    protected $tabla = 'reservas';
    protected $campos = 'id, fecha, id_profesor, id_aula, id_franja';
    protected $campos_insert = 'fecha, id_profesor, id_aula, id_franja';
    protected $fk = 'id_profesor';
}
?>