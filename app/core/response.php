<?php

function printJSON($datos){
    header('Content-Type: application/json');
    echo json_encode($datos);
    exit;
}
?>