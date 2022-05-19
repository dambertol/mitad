<?php

if (!function_exists('dd')) {

    function dd($var = NULL)
    {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
        die;
    }
}

if (!function_exists('notificaciones_cedulas_movimientos_tipo_desc')) {

    function notificaciones_cedulas_movimientos_tipo_desc($id)
    {
        switch ($id) {
            case Cedulas_estados_model::SOLICITUD_REALIZADA:
                return '<span class="label label-info" style="margin:0 auto; width: auto;">SOLICITUD REALIZADA</span>';
                break;
            case Cedulas_estados_model::SOLICITUD_ACEPTADA:
                return '<span class="label label-primary" style="margin:0 auto; width: auto;">SOLICITUD_ACEPTADA</span>';
                break;
            case Cedulas_estados_model::NOTIFICADOR_ASIGNADO:
                return '<span class="label label-default" style="margin:0 auto; width: auto;">NOTIFICADOR_ASIGNADO</span>';
                break;
            case Cedulas_estados_model::ENTREGA_POSITIVA_MANO:
                return '<span class="label label-success" style="margin:0 auto; width: auto;">ENTREGA_POSITIVA_MANO</span>';
                break;
            case Cedulas_estados_model::ENTREGA_POSITIVA_BAJO_PUERTA:
                return '<span class="label label-success" style="margin:0 auto; width: auto;">ENTREGA_POSITIVA_BAJO_PUERTA</span>';
                break;
            case Cedulas_estados_model::ENTREGA_NEGATIVA:
                return '<span class="label label-danger" style="margin:0 auto; width: auto;">ENTREGA_NEGATIVA</span>';
                break;
            case Cedulas_estados_model::DATOS_INCORRECTOS:
                return '<span class="label label-warning" style="margin:0 auto; width: auto;">DATOS_INCORRECTOS</span>';
                break;
            case Cedulas_estados_model::SOLICITUD_ANULADA:
                return '<span class="label label-warning" style="margin:0 auto; width: auto;">SOLICITUD_ANULADA</span>';
                break;
            case Cedulas_estados_model::CEDULA_IMPRESA:
                return '<span class="label label-info" style="margin:0 auto; width: auto;">CEDULA_IMPRESA</span>';
                break;
        }
    }

}