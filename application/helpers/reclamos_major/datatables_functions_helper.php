<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 17/17/2019
 * Modificado: 04/11/2020 (Leandro)
 */
if (!function_exists('dt_column_incidencias_numero'))
{

    function dt_column_incidencias_numero($numero, $cant_adjuntos)
    {
        if ($cant_adjuntos > 0)
        {
            return '<i class="fa fa-paperclip" style=" font-size: 14px;"></i> ' . $numero;
        }
        else
        {
            return $numero;
        }
    }
}

if (!function_exists('dt_column_incidencias_estado'))
{

    function dt_column_incidencias_estado($estado)
    {
        switch ($estado)
        {
            case 'Anulada':
                return '<span class="label label-danger" style="margin:0 auto;">Anulada</span>';
                break;
            case 'Cerrada':
                return '<span class="label label-info" style="margin:0 auto;">Cerrada</span>';
                break;
            case 'Solucionada':
                return '<span class="label label-success" style="margin:0 auto;">Solucionada</span>';
                break;
            case 'En Proceso':
                return '<span class="label label-default" style="margin:0 auto;">En Proceso</span>';
                break;
            case 'Pendiente':
                return '<span class="label label-warning" style="margin:0 auto;">Pendiente</span>';
                break;
        }
    }
}

if (!function_exists('dt_column_incidencias_editar'))
{

    function dt_column_incidencias_editar($estado, $id, $url = 'editar')
    {
        switch ($estado)
        {
            case 'Anulada':
            case 'Cerrada':
            case 'Solucionada':
                return '';
                break;
            default:
                return '<a href="reclamos_major/incidencias/' . $url . '/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
                break;
        }
    }
}

if (!function_exists('dt_column_incidencias_anular'))
{

    function dt_column_incidencias_anular($estado, $id)
    {
        switch ($estado)
        {
            case 'Anulada':
            case 'Cerrada':
            case 'Solucionada':
                return '';
                break;
            default:
                return '<a href="reclamos_major/incidencias/anular/' . $id . '" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>';
                break;
        }
    }
}

if (!function_exists('dt_column_incidencias_finalizar'))
{

    function dt_column_incidencias_finalizar($estado, $id)
    {
        switch ($estado)
        {
            case 'Pendiente':
            case 'En Proceso':
                return '<a href="#" onclick="finalizar_incidencia(' . $id . ');return false;" title="Finalizar" class="btn btn-success btn-xs"><i class="fa fa-check-circle"></i></a>';
                break;
            default:
                return '';
                break;
        }
    }
}