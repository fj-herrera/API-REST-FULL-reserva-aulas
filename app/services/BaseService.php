<?php
namespace App\Services;
include_once __DIR__ . ('/../models/Profesor.php');

class BaseService {
    /*
    protected function instanciarProfesores($respuesta){
        $grupoProfesores = [];
        foreach ($respuesta as $objeto){
            $profesor = new Profesor(
                $objeto['id'],
                $objeto['nombre'],
                $objeto['email'],
                $objeto['rol']
            );
            $grupoProfesores[] = $profesor;
        }
        return $grupoProfesores;
    }
    */
    protected $db;
    protected $tabla;
    protected $campos;
    protected $fk;

    public function __construct() {
        $this->db = getDbConnection();
    }    

    public function obtenerTodos(){
        $sql = "SELECT {$this->campos} FROM {$this->tabla}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
        // Devolver objetos Profesores
        //return instanciarrProfesores($respuesta);
    }

    public function obtenerPorID($id){
        $sql = "SELECT {$this->campos} FROM {$this->tabla} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
    }

    public function obtenerPorID_Reservas($id,$recurso_sec){

        // Selecciona las reservas hechas por un profesor
        $campos_reserva = 'id, fecha, id_aula, id_profesor, id_franja';
        $sql = "SELECT {$campos_reserva} FROM {$recurso_sec} WHERE {$this->fk} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
    }

     public function obtenerAulasDisponibles($fecha,$franja){
        // Selecciona las aulas disponibles para una fecha y franja 
        $campos_reserva = 'id, fecha, id_aula, id_profesor, id_franja';
        // la consulta tiene que mostrar los nombres de las aulas disponibles 
        $sql = "SELECT a.id, a.nombre
                FROM aulas a
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM reservas r
                    WHERE r.id_aula = a.id
                    AND r.id_franja = ?
                    AND r.fecha = ?
                );";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([ $franja, $fecha]);
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
    }

}