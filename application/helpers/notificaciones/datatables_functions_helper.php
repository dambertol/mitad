<?php

if (!function_exists('dt_column_notificaciones_cedulas_estado')) {

    function dt_column_notificaciones_cedulas_estado($estado_id, $desc)
    {
        switch ($estado_id) {
            case Cedulas_estados_model::SOLICITUD_REALIZADA:
                return '<span class="label label-info" style="margin:0 auto; width: auto;">' . $desc . '</span>';
                break;
            case 2:
                return '<span class="label label-primary" style="margin:0 auto; width: auto;">' . $desc . '</span>';
                break;
            case 3:
                return '<span class="label label-default" style="margin:0 auto; width: auto;">' . $desc . '</span>';
                break;
            case 4:
            case 5:
                return '<span class="label label-success" style="margin:0 auto; width: auto;">' . $desc . '</span>';
                break;
            case 6:
                return '<span class="label label-danger" style="margin:0 auto; width: auto;">' . $desc . '</span>';
                break;
            case 7:
            case 8:
                return '<span class="label label-warning" style="margin:0 auto; width: auto;">' . $desc . '</span>';
                break;
            case 9:
                return '<span class="label label-info" style="margin:0 auto; width: auto;">' . $desc . '</span>';
                break;
        }
    }
}

if (!function_exists('dt_column_notificaciones_cedulas_oficina')) {

    function dt_column_notificaciones_cedulas_oficina($oficina_id)
    {
        $oficina = $this->Areas_model->get(['id' => oficina_id]);
        return $oficina->nombre;
    }
}

if (!function_exists('dt_column_notificaciones_cedulas_prioridad')) {

    function dt_column_notificaciones_cedulas_prioridad($id, $prioridad)
    {
        switch ($prioridad) {
            case 1:
                return '<i class="fa fa-info-circle red"></i> ' . $id ;
                break;
//            case 7:
//                return '<span class="label label-primary" style="margin:0 auto; width: auto;">' . $id . '</span>';
//                break;
//            case 14:
//                return '<span class="label label-default" style="margin:0 auto; width: auto;">' . $id . '</span>';
//                break;
            default:
                return $id;
        }
    }
}