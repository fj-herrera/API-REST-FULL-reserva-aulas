<?php
namespace App\Core;

/**
 * Clase que encapsula y normaliza los datos de una petición HTTP recibida por la API.
 * Permite acceder de forma sencilla al método, recurso, id, subrecurso y cuerpo de la petición,
 * independientemente de cómo se haya recibido (endpoint o body).
 */
class Peticion {
    private $metodo;
    private $id;
    private $recurso;
    private $sub_recurso;
    private $body;

    public function __construct($args) {
        $this->metodo = $args['metodo'] ?? null;
        $this->id = $this->setId($args);
        $this->recurso = $this->setRecurso($args);
        $this->sub_recurso = $this->setSubRecurso($args);
        $this->body = json_decode($args['body'], true) ?? null;
    }

    public function getMetodo(){
        return $this->metodo;
    }

    private function setID($args){
        return (isset($args['id'])) ? $args['id'] : null;
    }

    public function getId(){
        return ($this->id) ? $this->id : null;
    }

    private function setRecurso($args) {
        return (isset($args['recurso'])) ? $args['recurso'] : null;
    }

    public function getRecurso(){
        return $this->recurso;
    }

    private function setSubRecurso($args){
        return (isset($args['sub_recurso'])) ? $args['sub_recurso'] : null;
    }

    public function getSubRecurso(){
        return $this->sub_recurso;
    }

    public function getIdBorrar(){
        return $this->id;
    }

    public function getRol(){
        return (isset($this->body['rol_user'])) ? $this->body['rol_user'] : null;
    }

    public function getIdUser(){
        return (isset($this->body['id_user'])) ? $this->body['id_user'] : null;
    }

    public function getBody(){
        return $this->body;
    }
}
