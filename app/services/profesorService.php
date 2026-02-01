<?php
include_once __DIR__ . '/BaseService.php';

class ProfesorService extends \App\Services\BaseService {
    protected $tabla = 'profesores';
    protected $campos = 'id, nombre, email, rol';
    protected $campos_insert = "nombre, email, rol";
    protected $fk = 'id_profesor';

    protected function comprobarNombre($nombre){
        if ($nombre) {
            $nombres = $this->obtenerNombres();
            // Comprobar si $nombre está en el array de nombres
            return in_array($nombre, $nombres);
        }
        return false;
    }

    protected function comprobarEmail($email){
    // Comprobacion emails
        if ($email) {
            $emails = $this->obtenerEmails();
            return in_array($email, $emails);
        }
        return false;
    }

    public function agregarProfesor($body){
        $existe_nombre = $this->comprobarNombre($body['nombre']);
        $existe_email = $this->comprobarEmail($body['email']);
        if ($existe_nombre && $existe_email) {
            return 'ambos';
        } elseif ($existe_nombre) {
            return 'nombre';
        } elseif ($existe_email) {
            return 'email';
        }
        // Usar $this->camposInsert para evitar incluir 'id' en el insert
        $sql = "INSERT INTO {$this->tabla} ({$this->campos_insert}) VALUES (?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['nombre'],$body['email'],$body['rol']]);
    }

    public function actualizarProfesor($body) {
        $profesores = $this->obtenerIds($body['id']);
        $existe = in_array($body['id'], $profesores);

        // Comprobar si el nombre ya existe en otro profesor
        $nombres = $this->obtenerNombres();
        $profesor_actual = $this->obtenerPorID($body['id']);
        $nombre_actual = $profesor_actual[0]['nombre'] ?? null;
        if ($body['nombre'] !== $nombre_actual && in_array($body['nombre'], $nombres)) {
            return 'nombre'; // Nombre duplicado
        }

        // Comprobar si el email ya existe en otro profesor
        $emails = $this->obtenerEmails();
        $email_actual = $profesor_actual[0]['email'] ?? null;
        if ($body['email'] !== $email_actual && in_array($body['email'], $emails)) {
            return 'email'; // Email duplicado
        }

        if ($existe === true){
            $sql = "UPDATE {$this->tabla} SET nombre = ?, email = ?, rol = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$body['nombre'],$body['email'],$body['rol'],$body['id']]);
        } else {
            return 'no_existe';
        }
    }

    public function borrarProfesor($id){
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