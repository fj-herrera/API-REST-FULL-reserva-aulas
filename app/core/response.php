<?php

/**
 * Imprime un array o dato como respuesta JSON y termina la ejecución del script.
 * Establece la cabecera Content-Type a application/json y codifica los datos en UTF-8.
*/
function printJSON($datos){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}