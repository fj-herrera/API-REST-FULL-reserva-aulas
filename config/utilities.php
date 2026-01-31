<?php
namespace Config\utilities;

class Peticion {
    private $metodo;
    private $endpoint = [];
    private $body;

    public function __construct($args) {
        $this->metodo = $args['metodo'] ?? null;
        $this->endpoint = is_array($args['endpoint']) ? $args['endpoint'] : [$args['endpoint']];
        $this->body = json_decode($args['body'], true);
    }
    public function getMetodo(){
        return $this->metodo;
    }
    public function getEndpoint(){
        return $this->endpoint;
    }
    public function getRecurso(){
        return $this->endpoint[1];
    }
    public function getID(){
        if (count($this->endpoint) >= 3) {
            return $this->endpoint[2];
        }
        return null;
    }
    public function getRecursoSec(){
        if (count($this->endpoint) === 4) {
            return $this->endpoint[3];
        }
        return null;
    }

    public function getBody(){
        return $this->body;
    }

    
    /*
    public function printPeticion(){
        print "metodo: {$this->metodo}\n
              endpoint: {$this->printArray($this->endpoint)}\n
              parametros:{$this->printArray($this->parametros)}\n
              body: {$this->body}";
    }

    private function printArray($array){
        $resultado ='';
        foreach ($array as $k => $v){
            $resultado .="'{$k}'=>'{$v} | '"; 
        }
        return $resultado;
    }
        */
}

function validarPeticion($endpoint){
    $isValid = false;
    foreach (validEndpoints::VALID as $clave => $valor){
        if( preg_match($valor, $endpoint, $matches)){
            $isValid = true;
        }
    }
    return $isValid;
}

function partirEndpoint($endpoint){
    $parte = strtok($endpoint,"/");
    $peticion =[];
    while ($parte !== false){
        array_push($peticion, $parte);
        $parte = strtok("/");
    }
    return $peticion;
}


class ValidEndpoints {
    public const VALID = [
        'aulas' =>          '#^/api/aulas$#',           // /api/aulas
        'aulas-id' =>       '#^/api/aulas/\\d+$#',      // /api/aulas/1
        'profesores' =>     '#^/api/profesores$#',      // /api/profesores
        'profesores-id' =>  '#^/api/profesores/\\d+$#', // /api/profesores/1
        'franjas' =>        '#^/api/franjas$#',         // /api/franjas
        'franjas-id' =>     '#^/api/franjas/\\d+$#',    // /api/franjas/1
        'reservas' =>       '#^/api/reservas$#',        // /api/reservas
        'reservasas-id' =>  '#^/api/reservas/\\d+$#',   // /api/reservas/1
        // Compuestas
        'profesores-id-reservas' =>  '#^/api/profesores/\d+/reservas$#', // /api/profesores/1/reservas, 
        'Aulas-disponibles'      =>  '#^/api/aulas/disponibles$#' // /api/auals/disponibles,
    
    ];
}

class ValidValues {
    public const NOMBRE =   '#^(?=.{1,100}$)[A-Za-zÁÉÍÓÚáéíóúÑñüÜ\s]+$#';
        // Debe contener al menos una letra o número
        public const NOMBRE_AULA = '#^(?=.*[A-Za-zÁÉÍÓÚáéíóúÑñüÜ0-9])[A-Za-zÁÉÍÓÚáéíóúÑñüÜ0-9\\-\\s]{1,100}$#';
        public const NOMBRE_FRANJA = '#^(?=.*[A-Za-zÁÉÍÓÚáéíóúÑñüÜ0-9])[A-Za-zÁÉÍÓÚáéíóúÑñüÜ0-9\\-\\s]{1,100}$#';
    public const EMAIL =    '#^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$#';
    public const HORA =     '#^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$#';
    public const FECHA =    '#^\d{4}-\d{2}-\d{2}$#';
}

class Codes {
    public const OK =           200;
    public const CREATED =      201;
    public const NO_CONTENT =   204;
    public const BAD_REQUEST =  400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN =    403;
    public const CONFLICT =     409;
    public const NOT_FOUND =    404;
    public const SERVER_ERROR = 500;
}

class ErrMsgs {
    public const NOT_FOUND = 'Recurso no encontrado';
    public const INVALID_ENDPOINT = 'URL no válida';
    public const SERVER_ERROR = 'Error interno del servidor';
    public const FECHA_PASADA = 'La fecha indicada ya ha pasado';
    // Aula 
    public const AULA_EXISTE = 'Ya existe un aula con ese nombre';
    public const NOMBRE_AULA = 'El nombre del aula no es válido';

    // Profesor 
    public const NOMBRE_PROFESOR = 'El nombre del profesor no es válido';
    public const NOMBRE_PROFESOR_EXISTE = 'Ya existe un profesor con este nombre';
    public const PROFESOR_EXISTE = 'Ya existe un profesor con este nombre y correo';
    public const EMAIL_PROFESOR = 'El email del profesor no es válido';
    public const EMAIL_PROFESOR_EXISTE = 'Ya existe un profesor con este email';
    // Franjas 
    public const NOMBRE_FRANJA = 'El nombre de la franja no es válido';
    public const HORA_I_FRANJA = 'El formato de la hora de inicio no es válido';
    public const HORA_F_FRANJA = 'El formato de la hora de fin no es válido';
    public const HORAS_FRANJA =  'La hora de fin no puede ser igual o inferior a la hora de inicio';
    public const NOMBRE_FRANJA_EXISTE = 'Ya existe una franja con este nombre';
    public const FRANJA_INVALIDA = 'La franja horaria no es válida: la hora de inicio y fin no pueden ser iguales ni la hora de fin anterior a la de inicio.';
    public const FRANJA_EXISTE = 'Ya existe una franja con la misma hora de inicio y fin';

    // Reservas
    public const PROFESOR_FRANJA = 'Ya tienes otro aula reservada el mismo dia en la misma franja';
    public const AULA_FRANJA = 'Este aula ya está reservada en esa franja y fecha';
    public const AULA_PROFESOR_FRANJA = 'El aula ya esta reservada y el profesor tiene esa franja reservada en otra aula';
    public const FECHA = 'No se puede reservar un aula con una fecha anterior a hoy';
}

class OkMsgs {
    public const AULA_OK = 'El aula ha sido creada correctamente';
    public const PROFESOR_OK = 'El profesor ha sido creado correctamente';
    public const FRANJA_OK = 'La franja ha sido creada correctamente';
    public const RESERVA_OK = 'La reserva ha sido creada correctamente';

    public const AULA_UPDATE = 'El aula ha sido actualizada correctamente';
    public const PROFESOR_UPDATE = 'El Profesor ha sido actualizado correctamente';
    public const FRANJA_UPDATE = 'La franja ha sido actualizado correctamente';
}
?>