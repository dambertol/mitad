<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 14/11/2017
 * Modificado: 06/01/2020 (Leandro)
 */
if (!function_exists('dt_column_vales_anular'))
{

	function dt_column_vales_anular($estado, $id)
	{
		switch ($estado)
		{
			case 'Asignado':
				return '';
				break;
			case 'Anulado':
				return '<a href="vales_combustible/vales/desanular/' . $id . '" title="Desanular" class="btn btn-primary btn-xs"><i class="fa fa-check"></i></a>';
				break;
			default:
				return '<a href="vales_combustible/vales/anular/' . $id . '" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>';
				break;
		}
	}
}

if (!function_exists('dt_column_vales_pendientes_anular'))
{

	function dt_column_vales_pendientes_anular($estado, $id)
	{
		switch ($estado)
		{
			case 'Asignado':
				return '';
				break;
			case 'Anulado':
				return '<a href="vales_combustible/vales/desanular/' . $id . '/listar_pendientes" title="Desanular" class="btn btn-primary btn-xs"><i class="fa fa-check"></i></a>';
				break;
			default:
				return '<a href="vales_combustible/vales/anular/' . $id . '/listar_pendientes" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>';
				break;
		}
	}
}

if (!function_exists('dt_column_vales_editar'))
{

	function dt_column_vales_editar($estado, $id, $grupo)
	{
		switch ($estado)
		{
			case 'Anulado':
				return '';
				break;
			case 'Creado':
				if ($grupo === 'CON')
				{
					return '';
				}
				return '<a href="vales_combustible/vales/editar/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
			case 'Impreso':
			case 'Asignado':
				if ($grupo === 'HAC')
				{
					return '';
				}
				return '<a href="vales_combustible/vales/editar/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
			default:
				return '<a href="vales_combustible/vales/editar/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
		}
	}
}

if (!function_exists('dt_column_vales_pendientes_editar'))
{

	function dt_column_vales_pendientes_editar($estado, $id, $grupo)
	{
		switch ($estado)
		{
			case 'Anulado':
				return '';
				break;
			case 'Creado':
				if ($grupo === 'CON')
				{
					return '';
				}
				return '<a href="vales_combustible/vales/editar/' . $id . '/listar_pendientes" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
			case 'Impreso':
			case 'Asignado':
				if ($grupo === 'HAC')
				{
					return '';
				}
				return '<a href="vales_combustible/vales/editar/' . $id . '/listar_pendientes" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
			default:
				return '<a href="vales_combustible/vales/editar/' . $id . '/listar_pendientes" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
		}
	}
}

if (!function_exists('dt_column_vales_areas_editar'))
{

	function dt_column_vales_areas_editar($estado, $id)
	{
		switch ($estado)
		{
			case 'Pendiente':
				return '<a href="vales_combustible/vales/editar_area/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}
if (!function_exists('dt_column_vales_areas_anular'))
{

	function dt_column_vales_areas_anular($estado, $id)
	{
		switch ($estado)
		{
			case 'Pendiente':
				return '<a href="vales_combustible/vales/anular/' . $id . '/listar_areas" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}

if (!function_exists('dt_column_vales_estado'))
{

	function dt_column_vales_estado($estado)
	{
		switch ($estado)
		{
			case 'Anulado':
				return '<span class="label label-danger" style="margin:0 auto;">Anulado</span>';
				break;
			case 'Asignado':
				return '<span class="label label-success" style="margin:0 auto;">Asignado</span>';
				break;
			case 'Creado':
				return '<span class="label label-default" style="margin:0 auto;">Creado</span>';
				break;
			case 'Impreso':
				return '<span class="label label-info" style="margin:0 auto;">Impreso</span>';
				break;
			case 'Pendiente':
				return '<span class="label label-warning" style="margin:0 auto;">Pendiente</span>';
				break;
		}
	}
}

if (!function_exists('dt_column_autorizaciones_litros'))
{

	function dt_column_autorizaciones_litros($lleno, $litros)
	{
		switch ($lleno)
		{
			case 'SI':
				return 'LLENO';
				break;
			case 'NO':
				return $litros;
				break;
		}
	}
}

if (!function_exists('dt_column_autorizaciones_anular'))
{

	function dt_column_autorizaciones_anular($estado, $id)
	{
		switch ($estado)
		{
			case 'Autorizada':
				return '<a href="vales_combustible/autorizaciones/anular/' . $id . '" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}

if (!function_exists('dt_column_autorizaciones_editar'))
{

	function dt_column_autorizaciones_editar($estado, $id)
	{
		switch ($estado)
		{
			case 'Autorizada':
				return '<a href="vales_combustible/autorizaciones/editar/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}

if (!function_exists('dt_column_autorizaciones_estado'))
{

	function dt_column_autorizaciones_estado($estado)
	{
		switch ($estado)
		{
			case 'Anulada':
				return '<span class="label label-danger" style="margin:0 auto;">Anulada</span>';
				break;
			case 'Autorizada':
				return '<span class="label label-success" style="margin:0 auto;">Autorizada</span>';
				break;
			case 'Cargada':
				return '<span class="label label-info" style="margin:0 auto;">Cargada</span>';
				break;
		}
	}
}

if (!function_exists('dt_column_vehiculos_aprobar'))
{

	function dt_column_vehiculos_aprobar($estado, $id)
	{
		switch ($estado)
		{
			case 'Anulado':
			case 'Pendiente':
				return '<a href="#" onclick="aprobar_vehiculo(' . $id . ');return false;" title="Aprobar" class="btn btn-success btn-xs"><i class="fa fa-check-circle"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}

if (!function_exists('dt_column_vehiculos_estado'))
{

	function dt_column_vehiculos_estado($estado)
	{
		switch ($estado)
		{
			case 'Anulado':
				return '<span class="label label-danger" style="margin:0 auto;">Anulado</span>';
				break;
			case 'Pendiente':
				return '<span class="label label-warning" style="margin:0 auto;">Pendiente</span>';
				break;
			case 'Aprobado':
				return '<span class="label label-success" style="margin:0 auto;">Aprobado</span>';
				break;
		}
	}
}

if (!function_exists('dt_column_vehiculos_editar'))
{

	function dt_column_vehiculos_editar($propiedad, $id)
	{
		switch ($propiedad)
		{
			case 'Oficial':
				return '';
				break;
			case 'Alquilado':
			case 'Particular':
				return '<a href="vales_combustible/vehiculos/editar_area/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
		}
	}
}

if (!function_exists('dt_column_cupos_combustible_cupos_consumo'))
{

	function dt_column_cupos_combustible_cupos_consumo($consumo, $cupo)
	{
		if ($cupo <= 0)
		{
			return '';
		}
		$porcentaje = ($consumo * 100) / $cupo;
		if ($porcentaje > 100)
		{
			$porcentaje = 100;
		}

		if ($porcentaje > 70)
		{
			$bg = 'bg-red';
		}
		elseif ($porcentaje > 50)
		{
			$bg = 'bg-orange';
		}
		else
		{
			$bg = 'bg-green';
		}

		return '
			<div class="text-center">
				' . intval($consumo) . ' de ' . $cupo . '
				<div class="progress progress_sm" style="width: 100%;">
					<div class="progress-bar ' . $bg . '" role="progressbar" data-transitiongoal="50" style="width: ' . $porcentaje . '%;" aria-valuenow="49"></div>
				</div>
			</div>
		';
	}
}