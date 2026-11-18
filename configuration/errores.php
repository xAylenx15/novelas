<?php
function mensajeBD(PDOException $e): string
{
    // P0001 es el código de PostgreSQL para un RAISE EXCEPTION
    if ($e->getCode() === 'P0001') {
        $msg = $e->errorInfo[2] ?? $e->getMessage();
        $msg = preg_replace('/^ERROR:\s*/', '', $msg);
        return trim(explode("\n", $msg)[0]);
    }

    // Error técnico: el detalle va al registro del servidor, no a la pantalla
    error_log($e->getMessage());
    return 'Ocurrió un error inesperado. Intentá de nuevo.';
}
