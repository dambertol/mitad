<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 17/03/2020
 * Modificado: 30/05/2021 (Leandro)
 */
if (!function_exists('dt_column_tramites_editar'))
{

    function dt_column_tramites_editar($estado_id, $id)
    {
        return '<a href="tramites_online/tramites/revisar/' . $id . '" title="Revisar" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i></a>';
    }
}

if (!function_exists('dt_column_tramites_imprimir'))
{

    function dt_column_tramites_imprimir($estado_imprimible, $id)
    {
        if ($estado_imprimible === 'SI') //Finalizado (HC)
        {
            return '<a href="tramites_online/tramites/imprimir_detalle_tramite/' . $id . '" title="Imprimir" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>';
        }
        else
        {
            return '';
        }
    }
}