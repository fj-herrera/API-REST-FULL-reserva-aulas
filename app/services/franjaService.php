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

    public function actualizarFranja($body){
        // Comprobar si existe la franja actual
        $franjas = $this->obtenerPorId($body['id']);
        $ids_franjas = array_column($franjas, 'id');
        $existe = in_array($body['id'], $ids_franjas);

        // Comprobar si el nombre ya existe en otra franja
        $nombres = $this->obtenerNombres();
        $franja_actual = $this->obtenerPorId($body['id']);
        $nombre_actual = $franja_actual[0]['nombre'] ?? null;
        if ($body['nombre'] !== $nombre_actual && in_array($body['nombre'], $nombres)) {
            return 'nombre'; // Nombre duplicado
        }

        // Comprobar si ya existe una franja con el mismo inicio y fin (en otra franja)
        $horas = $this->obtenerFranjas();
        $existe_franja = false;
        foreach ($horas as $franja) {
            if (
                isset($franja['hora_inicio'], $franja['hora_fin'], $franja['id']) &&
                $franja['hora_inicio'] === $body['hora_inicio'] &&
                $franja['hora_fin'] === $body['hora_fin'] &&
                $franja['id'] != $body['id']
            ) {
                $existe_franja = true;
                break;
            }
        }
        if ($existe_franja) {
            return 'franja'; // Franja duplicada
        }

        if ($existe === true){
            $sql = "UPDATE {$this->tabla} SET nombre = ?, hora_inicio = ?, hora_fin = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$body['nombre'],$body['hora_inicio'],$body['hora_fin'],$body['id']]);
        } else {
            return 'no_existe';
        }
    }

    public function borrarFranja($id){
        $reservas = $this->obtenerPorID_Reservas($id);
        if (empty($reservas)){
            $sql = "DELETE FROM {$this->tabla} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        }
        else {
            $borrado_reservas = $this-> borrarReservasPoId($id);
            if ($borrado_reservas === true){
                $sql = "DELETE FROM {$this->tabla} WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$id]);
            }
        }
    }
}
