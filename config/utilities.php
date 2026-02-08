<?php

namespace Config\utilities;

function validarPeticion($endpoint){
    $isValid = false;
    foreach (validEndpoints::VALID as $clave => $valor){
        if( preg_match($valor, $endpoint, $matches)){
            $isValid = true;
        }
    }
    return $isValid;
}

/**
 * Normaliza el body recibido: si es string JSON, lo decodifica a array.
 * Si ya es array, lo retorna tal cual.
 */
function normalizarBody($rawBody) {
    if (is_string($rawBody)) {
        $body = json_decode($rawBody, true);
        return is_array($body) ? $body : [];
    }
    return $rawBody;
}

/**
 * Extrae y valida parámetros del endpoint, como el recurso y el id numérico
*/
function extraerPartesEndpoint($endpoint) {
    $partes = array_values(array_filter(explode('/', $endpoint)));
    $parametros = [];
    // api/recurso
    if (isset($partes[1])) {
        $parametros['recurso'] = $partes[1];
    }
    // api/recurso/id
    if (isset($partes[2]) && is_numeric($partes[2])) {
        $parametros['id'] = (int)$partes[2];
    }
    // api/recurso/sub-recurso
    else if (isset($partes[2]) && is_string($partes[2])) {
        $parametros['sub_recurso'] = $partes[2];
    }
    // api/recurso/id/sub-recurso
    if (isset($partes[2]) && isset($partes[3]) && is_numeric($partes[2])) {
        $parametros['sub_recurso'] = $partes[3];
    }
    return $parametros;
}

/**
 * Regex para validar los endpoints perrmitidos
 */
class ValidEndpoints {
    public const VALID = [
        'aulas' =>          '#^/api/aulas$#',           // /api/aulas
        'aulas-id' =>       '#^/api/aulas/\\d+$#',      // /api/aulas/1
        'profesores' =>     '#^/api/profesores$#',      // /api/profesores
        'profesores-id' =>  '#^/api/profesores/\\d+$#', // /api/profesores/1
        'franjas' =>        '#^/api/franjas$#',         // /api/franjas
        'franjas-id' =>     '#^/api/franjas/\\d+$#',    // /api/franjas/1
        'reservas' =>       '#^/api/reservas$#',        // /api/reservas
        'reservas-id' =>    '#^/api/reservas/\\d+$#',   // /api/reservas/1
        // Compuestas
        'profesores-id-reservas' =>  '#^/api/profesores/\d+/reservas$#', // /api/profesores/1/reservas, 
        'Aulas-disponibles'      =>  '#^/api/aulas/disponibles$#' // /api/auals/disponibles,
    
    ];
}

/**
 * Endpoints exlusivos para admin
 */
class AdminEndpoints {
    public const ADMIN_ONLY = [
        '/api/aulas',
        '/api/profesores',
        '/api/franjas'
    ];
}

/**
 * Regex para validar las distintas entradas de datos
 */
class ValidValues {
    public const NOMBRE =   '#^(?=.*[A-Za-zÁÉÍÓÚáéíóúÑñüÜ])[A-Za-zÁÉÍÓÚáéíóúÑñüÜ\s]{1,100}$#';
    public const NOMBRE_FRANJA = '#^(?=.*[A-Za-zÁÉÍÓÚáéíóúÑñüÜ])[A-Za-zÁÉÍÓÚáéíóúÑñüÜ0-9\-\s]{1,100}$#u';
    public const NOMBRE_AULA = '#^(?=.*[A-Za-zÁÉÍÓÚáéíóúÑñüÜ0-9])[A-Za-zÁÉÍÓÚáéíóúÑñüÜ0-9\\-\\s]{1,100}$#';
    public const EMAIL =    '#^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$#';
    public const HORA =     '#^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$#';
    public const FECHA =    '#^\d{4}-\d{2}-\d{2}$#';
}

/**
 * Codigos para devolver en cada respuesta
 */
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

/**
 * Mensajes de error que maneja la API
 */
class ErrMsgs {
    public const NOT_FOUND = 'Recurso no encontrado';
    public const INVALID_ENDPOINT = 'URL no válida';
    public const SERVER_ERROR = 'Error interno del servidor';
    public const PERMISOS = 'No esta autorizado para realizar esta operación';
    public const FECHA_PASADA = 'La fecha indicada ya ha pasado';
    // Aula 
    public const AULA_EXISTE = 'Ya existe un aula con ese nombre';
    public const NOMBRE_AULA = 'El nombre del aula no es válido';
    public const AULA_RESERVAS = 'El aula no se puede borrar por que tiene reservas asociadas';
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
    public const FECHA_FORMATO = 'El formato de la fecha no es correcto';
    public const ID_PROFESOR = 'La referencia del profesor asociado a la reserva no existe';
    public const ID_AULA = 'La referencia de el aula asociada a la reserva no existe';
    public const ID_FRANJA = 'La referencia de la franja asociada a la reserva no existe';
    // Reservas (custom)
    public const CAMPOS_OBLIGATORIOS_RESERVA = 'Faltan campos obligatorios: fecha, id_aula o id_franja';
    public const RESERVA_AJENA = 'No está permitido modificar reservas de otro profesor.';
    public const TRASPASO_AJENO = 'No está permitido asignar la reserva a otro profesor.';
    public const FALTA_FECHA_RESERVA = 'Falta el campo obligatorio: fecha';
    public const FALTA_ID_AULA_RESERVA = 'Falta el campo obligatorio: id_aula';
    public const FALTA_ID_FRANJA_RESERVA = 'Falta el campo obligatorio: id_franja';
    public const FALTA_ID_PROFESOR_RESERVA = 'Falta el campo obligatorio: id_profesor';
}
/**
 * Mensajes de éxito que maneja la API
 */
class OkMsgs {
    public const AULA_OK = 'El aula ha sido creada correctamente';
    public const PROFESOR_OK = 'El profesor ha sido creado correctamente';
    public const FRANJA_OK = 'La franja ha sido creada correctamente';
    public const RESERVA_OK = 'La reserva ha sido creada correctamente';
    public const AULA_UPDATE = 'El aula ha sido actualizada correctamente';
    public const PROFESOR_UPDATE = 'El Profesor ha sido actualizado correctamente';
    public const FRANJA_UPDATE = 'La franja ha sido actualizado correctamente';
    public const RESERVA_UPDATE = 'La reserva ha sido actualizada correctamente';
    public const AULA_DELETE = 'El aula ha sido borrada correctamente';
}