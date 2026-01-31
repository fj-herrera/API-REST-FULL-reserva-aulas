<?php
include_once __DIR__ . '/BaseService.php';

class FranjaService extends \App\Services\BaseService {
    protected $tabla = 'franjas';
    protected $campos = 'id, nombre, hora_inicio, hora_fin';
    protected $campos_insert = "nombre, hora_inicio, hora_fin";
    protected $fk = 'id_franja';

    protected function comprobarNombre($nombre){
        if ($nombre) {
            $nombres = $this->obtenerNombres();
            // Comprobar si $nombre está en el array de nombres
            return in_array($nombre, $nombres);
        }
        return false;
    }

    protected function comprobarFranja($hora_i, $hora_f){
    // Comprobacion franjas
        $horas = $this->obtenerFranjas();
        // Si ya esxiste una franja con el mismo inicio y final
        $inicio = (in_array($hora_i, $horas['horas_i'])) ? true : false;
        $fin = (in_array($hora_f, $horas['horas_f'])) ? true : false;
        // Si ya existe una franja con identicos fi y inicio
        if ($inicio && $fin){
            return true;
        }
        return false; 
    }

    public function agregarFranja($body){
        $existe_nombre = $this->comprobarNombre($body['nombre']);
        $existe_franja = $this->comprobarFranja($body['hora_inicio'], $body['hora_fin']);

        if ($existe_nombre && $existe_franja) {
            return 'ambos';
        } elseif ($existe_nombre) {
            return 'nombre';
        } elseif ($existe_franja) {
            return 'franja';
        }

        // Usar $this->camposInsert para evitar incluir 'id' en el insert
        $sql = "INSERT INTO {$this->tabla} ({$this->campos_insert}) VALUES (?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['nombre'],$body['hora_inicio'],$body['hora_fin']]);
    }
}
?>