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
}