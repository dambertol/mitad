<?php
//editado por yoel grosso
if (!function_exists('dt_column_oficina_de_empleo_estado')) {

    function dt_column_oficina_de_empleo_estado($estado) {
        switch ($estado) {
            case 'FINALIZADO':
                return '<span class="label label-danger" style="margin:0 auto; width: auto;">FINALIZADO</span>';
                break;
            case 'FINALIZADO C/SUP GIS':
                return '<span class="label label-success" style="margin:0 auto; width: auto;">FINALIZADO C/SUP GIS</span>';
                break;
            case 'FINALIZADO POR DECLARACION JURADA':
                return '<span class="label label-success" style="margin:0 auto; width: auto;">FINALIZADO POR DECLARACION JURADA</span>';
                break;
            case 'FINALIZADO S/SUP GIS':
                return '<span class="label label-default" style="margin:0 auto; width: auto;">FINALIZADO S/SUP GIS</span>';
                break;
            case 'RECALCULO':
                return '<span class="label label-info" style="margin:0 auto; width: auto;">RECALCULO</span>';
                break;
            case 'PENDIENTE':
                return '<span class="label label-warning" style="margin:0 auto; width: auto;">PENDIENTE</span>';
                break;
            case 'PENDIENTE P/ INSPECCION':
                return '<span class="label label-warning" style="margin:0 auto; width: auto;">PENDIENTE P/ INSPECCION</span>';
                break;
        }
    }

}