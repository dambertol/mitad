<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 12/12/2017
 * Modificado: 12/12/2017 (Leandro)
 */
if (!function_exists('dt_column_resoluciones_estado'))
{

	function dt_column_resoluciones_estado($estado, $id)
	{
		switch ($estado)
		{
			case 'Activa':
				return '<a href="resoluciones/resoluciones/anular/' . $id . '" title="Anular"><span class="label label-success">Activa</span></a>';
				break;
			case 'Anulada':
				return '<span class="label label-danger">Anulada</span>';
				break;
			default:
				return '';
				break;
		}
	}
}

if (!function_exists('dt_column_resoluciones_imprimir'))
{

	function dt_column_resoluciones_imprimir($estado, $id)
	{
		switch ($estado)
		{
			case 'Activa':
				return '<a href="resoluciones/resoluciones/imprimir/' . $id . '" title="Imprimir" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}

if (!function_exists('dt_column_resoluciones_editar'))
{

	function dt_column_resoluciones_editar($estado, $id)
	{
		switch ($estado)
		{
			case 'Activa':
				return '<a href="resoluciones/resoluciones/editar/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}