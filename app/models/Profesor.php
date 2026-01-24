<?php

class Profesor {
    private $id;
    private $nombre;
    private $email;
    private $rol;

    public function __construct($id,$nombre,$email,$rol='usuario'){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->rol = $rol;
    }

    public function getId(){
        return $this->id;
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getRol(){
        return $this->rol;
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