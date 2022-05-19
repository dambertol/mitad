<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 07/05/2019
 * Modificado: 27/12/2019 (Leandro)
 */
if (!function_exists('dt_column_consumibles_estado'))
{

	function dt_column_consumibles_estado($estado)
	{
		switch ($estado)
		{
			case 'Baja':
				return '<span class="label label-danger" style="margin:0 auto;">Baja</span>';
				break;
			case 'Activo':
				return '<span class="label label-success" style="margin:0 auto;">Activo</span>';
				break;
		}
	}
}
if (!function_exists('dt_column_movimientos_estado'))
{

	function dt_column_movimientos_estado($estado)
	{
		switch ($estado)
		{
			case 'Anulado':
				return '<span class="label label-danger" style="margin:0 auto;">Anulado</span>';
				break;
			case 'Activo':
				return '<span class="label label-success" style="margin:0 auto;">Activo</span>';
				break;
		}
	}
}
if (!function_exists('dt_column_movimientos_anular'))
{

	function dt_column_movimientos_anular($estado, $id)
	{
		switch ($estado)
		{
			case 'Anulado':
				return '';
				break;
			default:
				return '<a href="toner/movimientos/anular/' . $id . '" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>';
				break;
		}
	}
}
if (!function_exists('dt_column_pedidos_consumibles_estado'))
{

	function dt_column_pedidos_consumibles_estado($estado)
	{
		switch ($estado)
		{
			case 'Pendiente':
				return '<span class="label label-warning" style="margin:0 auto;">Pendiente</span>';
				break;
			case 'Finalizado':
				return '<span class="label label-success" style="margin:0 auto;">Finalizado</span>';
				break;
			case 'Anulado':
				return '<span class="label label-danger" style="margin:0 auto;">Anulado</span>';
				break;
		}
	}
}
if (!function_exists('dt_column_pedidos_consumibles_anular'))
{

	function dt_column_pedidos_consumibles_anular($estado, $id)
	{
		switch ($estado)
		{
			case 'Anulado':
				return '';
				break;
			default:
				return '<a href="toner/pedidos_consumibles/anular/' . $id . '" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>';
				break;
		}
	}
}
if (!function_exists('dt_column_pedidos_consumibles_editar'))
{

	function dt_column_pedidos_consumibles_editar($estado, $id)
	{
		switch ($estado)
		{
			case 'Anulado':
				return '';
				break;
			default:
				return '<a href="toner/pedidos_consumibles/editar/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
		}
	}
}