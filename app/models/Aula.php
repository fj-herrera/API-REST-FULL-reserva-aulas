<?php

class Aula {
    private $id;
    private $nombre;
    private $capacidad;
    private $descripcion;

    public function __construct($id,$nombre,$capacidad,$descripcion=null){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->capacidad = $capacidad;
        $this->descripcion = $descripcion;
    }

    public function getId(){
        return $this->id;
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getCapacidad(){
        return $this->nombre;
    }
    public function getDescripcion(){
        return $this->descripcion;
    }

    public function toArray(){
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'capacidad' => $this->capacidad,
            'descripcion' => $this->descripcion
        ];
    }
    
}