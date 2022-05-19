<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 22/10/2019
 * Modificado: 22/10/2019 (Leandro)
 */
if (!function_exists('dt_column_compras_anular'))
{

	function dt_column_compras_anular($estado, $id)
	{
		switch ($estado)
		{
			case 'Anulada':
				return '';
				break;
			default:
				return '<a href="obrador/compras/anular/' . $id . '" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>';
				break;
		}
	}
}

if (!function_exists('dt_column_compras_editar'))
{

	function dt_column_compras_editar($estado, $id)
	{
		switch ($estado)
		{
			case 'Anulada':
				return '';
				break;
			default:
				return '<a href="obrador/compras/editar/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
		}
	}
}

if (!function_exists('dt_column_compras_estado'))
{

	function dt_column_compras_estado($estado)
	{
		switch ($estado)
		{
			case 'Anulada':
				return '<span class="label label-danger" style="margin:0 auto;">Anulada</span>';
				break;
			case 'Activa':
				return '<span class="label label-success" style="margin:0 auto;">Activa</span>';
				break;
		}
	}
}

if (!function_exists('dt_column_entregas_anular'))
{

	function dt_column_entregas_anular($estado, $id)
	{
		switch ($estado)
		{
			case 'Anulada':
				return '';
				break;
			default:
				return '<a href="obrador/entregas/anular/' . $id . '" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>';
				break;
		}
	}
}

if (!function_exists('dt_column_entregas_editar'))
{

	function dt_column_entregas_editar($estado, $id)
	{
		switch ($estado)
		{
			case 'Anulada':
				return '';
				break;
			default:
				return '<a href="obrador/entregas/editar/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
		}
	}
}

if (!function_exists('dt_column_entregas_estado'))
{

	function dt_column_entregas_estado($estado)
	{
		switch ($estado)
		{
			case 'Anulada':
				return '<span class="label label-danger" style="margin:0 auto;">Anulada</span>';
				break;
			case 'Activa':
				return '<span class="label label-success" style="margin:0 auto;">Activa</span>';
				break;
		}
	}
}