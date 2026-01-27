<?php 
namespace Config\utilities;

class Peticion {
    private $metodo;
    private $endpoint = [];
    private $parametros = [];
    private $body;

    public function __construct($args) {
        $this->metodo = $args['metodo'] ?? null;
        $this->endpoint = is_array($args['endpoint']) ? $args['endpoint'] : [$args['endpoint']];
        $this->parametros = $this->set_parametros($args['parametros']);
        $this->body =$args['body'] ?? null;
    }
    
    public function get_metodo(){
        return $this->peticion; 
    }
    public function get_endpoint(){
        return $this->endpoint; 
    }
    public function get_recurso(){
        return $this->endpoint[1]; 
    }
    public function get_id(){
        if (count($this->endpoint) >= 3) {
            return $this->endpoint[2];
        }
        return null;
    }
    public function get_recurso_sec(){
        if (count($this->endpoint) === 4) {
            return $this->endpoint[3];
        }
        return null;
    }

    public function get_parametros(){
        return $this->parametros; 
    }
    public function get_body(){
        return $this->body; 
    }

    private function set_parametros($parametros) {
        if (is_string($parametros)) {
            parse_str($parametros, $result);
            return $result;
        } else {
            return null;
        }
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

class ResponseCodes {
    public const OK =           200;
    public const CREATED =      201;
    public const NO_CONTENT =   204;
    public const BAD_REQUEST =  400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN =    403;
    public const NOT_FOUND =    404;
    public const SERVER_ERROR = 500;
}

class ErrMsgs {
    public const NOT_FOUND = 'Recurso no encontrado';
    public const INVALID_ENDPOINT = 'URL no válida';
    public const SERVER_ERROR = 'Error interno del servidor';
}

class OkMsgs {
    public const NOT_FOUND = 'Recurso no encontrado';
    public const INVALID_ENDPOINT = 'URL no válida';
    public const SERVER_ERROR = 'Error interno del servidor';
}
?>