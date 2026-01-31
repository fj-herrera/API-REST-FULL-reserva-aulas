<?php
include_once __DIR__ . '/BaseService.php';

class AulaService extends \App\Services\BaseService {
    protected $tabla = 'aulas';
    protected $campos = 'id, nombre, capacidad, descripcion';
    protected $campos_insert = "nombre, capacidad, descripcion";
    protected $fk = 'id_aula';

    protected function comprobarNombre($nombre){
        if ($nombre) {
            $nombres = $this->obtenerNombres();
            // Comprobar si $nombre está en el array de nombres
            return in_array($nombre, $nombres);
        }
        return false;
    }

    public function agregarAula($body){
        // Si el nombre ya existe
        if ($this->comprobarNombre($body['nombre'])) {
           return false;
        }
        $sql = "INSERT INTO {$this->tabla} ({$this->campos_insert}) VALUES (?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['nombre'],$body['capacidad'],$body['descripcion']]);
    }
}
?>