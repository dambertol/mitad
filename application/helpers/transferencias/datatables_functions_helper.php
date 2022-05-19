<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 02/07/2018
 * Modificado: 07/01/2020 (Leandro)
 */
if (!function_exists('dt_column_tramites_listar_editar'))
{

	function dt_column_tramites_listar_editar($estado_id, $id)
	{

		if ($estado_id === '12') //Finalizado (HC)
		{
			return '<a href="transferencias/tramites/revisar/' . $id . '" title="Revisar" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i></a>';
		}
		else
		{
			return '';
		}
	}
}

if (!function_exists('dt_column_tramites_editar'))
{

	function dt_column_tramites_editar($estado_id, $id)
	{

		if ($estado_id === '3' || $estado_id === '6') //Corrección de Datos o Corrección de acuerdo a Observaciones (HC)
		{
			return '<a href="transferencias/tramites/editar/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
		}
		if ($estado_id === '12' || $estado_id === '15') //Finalizado o Cancelado (HC)
		{
			return '';
		}
		else
		{
			return '<a href="transferencias/tramites/revisar/' . $id . '" title="Revisar" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i></a>';
		}
	}
}

if (!function_exists('dt_column_tramites_imprimir'))
{

	function dt_column_tramites_imprimir($estado_id, $id)
	{
		if ($estado_id === '12') //Finalizado (HC)
		{
			return '<a href="transferencias/tramites/imprimir_detalle_tramite/' . $id . '" title="Imprimir" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>';
		}
		else
		{
			return '';
		}
	}
}