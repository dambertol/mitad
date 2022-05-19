<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 03/09/2019
 * Modificado: 03/09/2019 (Leandro)
 */
if (!function_exists('dt_column_equipos_estado'))
{

	function dt_column_equipos_estado($estado)
	{
		switch ($estado)
		{
			case 'Robado':
				return '<span class="label label-danger" style="margin:0 auto;">Robado</span>';
				break;
			case 'Disponible':
				return '<span class="label label-success" style="margin:0 auto;">Disponible</span>';
				break;
			case 'En Uso':
				return '<span class="label label-info" style="margin:0 auto;">En Uso</span>';
				break;
			case 'No Disponible':
				return '<span class="label label-warning" style="margin:0 auto;">No Dispon.</span>';
				break;
		}
	}
}

if (!function_exists('dt_column_lineas_estado'))
{

	function dt_column_lineas_estado($estado)
	{
		switch ($estado)
		{
			case 'Baja':
				return '<span class="label label-danger" style="margin:0 auto;">Baja</span>';
				break;
			case 'Denunciada':
				return '<span class="label label-default" style="margin:0 auto;">Denunciada</span>';
				break;
			case 'Disponible':
				return '<span class="label label-success" style="margin:0 auto;">Disponible</span>';
				break;
			case 'En Uso':
				return '<span class="label label-info" style="margin:0 auto;">En Uso</span>';
				break;
			case 'No Disponible':
				return '<span class="label label-warning" style="margin:0 auto;">No Dispon.</span>';
				break;
		}
	}
}

if (!function_exists('dt_column_movimientos_editar'))
{

	function dt_column_movimientos_editar($tipo, $id)
	{
		switch ($tipo)
		{
			case 'Alta de Equipo':
			case 'Alta de Línea':
				return '<a href="telefonia/movimientos/editar/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}

if (!function_exists('dt_column_movimientos_comodato'))
{

	function dt_column_movimientos_comodato($tipo, $nro_comodato, $id)
	{
		if (empty($nro_comodato))
		{
			return '';
		}
		switch ($tipo)
		{
			case 'Entrega':
			case 'Recepción':
				return '<a href="telefonia/movimientos/imprimir_comodato/' . $id . '" target="_blank" title="Comodato" class="btn btn-primary btn-xs"><i class="fa fa-file"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}