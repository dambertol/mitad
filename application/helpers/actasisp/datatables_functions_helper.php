<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 22/02/2021
 * Modificado: 22/02/2021 (Leandro)
 */
if (!function_exists('dt_column_inspectores_estado'))
{

    function dt_column_inspectores_estado($estado)
    {
        switch ($estado)
        {
            case 'Activo':
                return '<span class="label label-success">Activo</span>';
                break;
            case 'Inactivo':
                return '<span class="label label-danger">Inactivo</span>';
                break;
            default:
                return $estado;
                break;
        }
    }
}