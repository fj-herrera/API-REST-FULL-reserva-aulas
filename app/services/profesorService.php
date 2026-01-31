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



}
?>