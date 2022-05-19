<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 23/04/2020
 * Modificado: 30/12/2020 (Leandro)
 */
if (!function_exists('dt_column_comercios_estado'))
{

    function dt_column_comercios_estado($estado)
    {
        switch ($estado)
        {
            case 'Pendiente':
                return '<span class="label label-warning" style="margin:0 auto;">Pendiente</span>';
                break;
            case 'Aprobado':
                return '<span class="label label-success" style="margin:0 auto;">Aprobado</span>';
                break;
            case 'Anulado':
                return '<span class="label label-danger" style="margin:0 auto;">Anulado</span>';
                break;
        }
    }
}

if (!function_exists('dt_column_comercios_aprobar'))
{

    function dt_column_comercios_aprobar($estado, $id)
    {
        switch ($estado)
        {
            case 'Pendiente':
                return '<a href="lujan_pass/comercios/aprobar/' . $id . '" title="Aprobar" class="btn btn-success btn-xs"><i class="fa fa-check"></i></a>';
                break;
            case 'Aprobado':
                return '';
                break;
            case 'Anulado':
                return '<a href="lujan_pass/comercios/aprobar/' . $id . '" title="Aprobar" class="btn btn-success btn-xs"><i class="fa fa-check"></i></a>';
                break;
        }
    }
}

if (!function_exists('dt_column_promociones_estado'))
{

    function dt_column_promociones_estado($estado)
    {
        switch ($estado)
        {
            case 'Pendiente':
                return '<span class="label label-warning" style="margin:0 auto;">Pendiente</span>';
                break;
            case 'Aprobado':
                return '<span class="label label-success" style="margin:0 auto;">Aprobado</span>';
                break;
            case 'Anulado':
                return '<span class="label label-danger" style="margin:0 auto;">Anulado</span>';
                break;
        }
    }
}

if (!function_exists('dt_column_promociones_aprobar'))
{

    function dt_column_promociones_aprobar($estado, $id)
    {
        switch ($estado)
        {
            case 'Pendiente':
                return '<a href="lujan_pass/promociones/aprobar/' . $id . '" title="Aprobar" class="btn btn-success btn-xs"><i class="fa fa-check"></i></a>';
                break;
            case 'Aprobado':
                return '';
                break;
            case 'Anulado':
                return '<a href="lujan_pass/promociones/aprobar/' . $id . '" title="Aprobar" class="btn btn-success btn-xs"><i class="fa fa-check"></i></a>';
                break;
        }
    }
}