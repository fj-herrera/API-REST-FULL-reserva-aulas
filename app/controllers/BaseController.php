<?php
namespace App\Controllers;
use Config\utilities\ValidEndpoints;
use Config\utilities\ResponseCodes;


class BaseController {

    protected function manejarPeticionGET($servicio, $peticion) {
        // -> /api/servicio = 2 elementos
        if (count($peticion) === 2) {
            $data = $servicio->obtenerTodos();
            $respuesta = $this->responder(null, $data, ResponseCodes::OK);
            if (empty($data)) {
                $respuesta = $this->responder(
                    ErrMsgs::NOT_FOUND, 
                    null, 
                    ResponseCodes::NO_CONTENT);
            } 
            return $respuesta;
        // -> /api/servicios/1 = 3 elementos    
        } elseif (count($peticion) === 3) {
            $id = $peticion[2];
            $data = $servicio->obtenerPorId($id);
            $respuesta = $this->responder(null, $data, ResponseCodes::OK);
            if (empty($data)) {
                $respuesta = $this->responder(
                    ErrMsgs::NOT_FOUND, 
                    null, 
                    ResponseCodes::NOT_FOUND);
            }
            return $respuesta;
        }
    }

    protected function responder($message, $data, $code)
    {
      ($message)? printJSON($message): null;
      ($data) ? printJSON($data) : null;
      ($code)? http_response_code($code) : null ;
      exit;
    }
}
