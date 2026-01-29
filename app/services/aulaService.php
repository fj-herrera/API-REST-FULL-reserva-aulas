<?php
include_once __DIR__ . '/BaseService.php';

class AulaService extends \App\Services\BaseService {
    protected $tabla = 'aulas';
    protected $campos = 'id, nombre, capacidad, descripcion';
    protected $campos_insert = "nombre, capacidad, descripcion";
    protected $fk = 'id_aula';

}
?>