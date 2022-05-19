<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 09/01/2020
 * Modificado: 23/01/2020 (Leandro)
 */
if (!function_exists('dt_column_documentos_estado'))
{

	function dt_column_documentos_estado($estado, $id)
	{
		switch ($estado)
		{
			case 'Activo':
				return '<span class="label label-success" style="margin:0 auto;">Activo</span>';
				break;
			case 'Anulado':
				return '<span class="label label-danger" style="margin:0 auto;">Anulado</span>';
				break;
			default:
				return '';
				break;
		}
	}
}

if (!function_exists('dt_column_documentos_editar'))
{

	function dt_column_documentos_editar($estado, $id, $url)
	{
		switch ($estado)
		{
			case 'Activo':
				return '<a href="gobierno/documentos/' . $url . '/' . $id . '" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}

if (!function_exists('dt_column_documentos_eliminar'))
{

	function dt_column_documentos_eliminar($estado, $id, $url)
	{
		switch ($estado)
		{
			case 'Activo':
				return '<a href="gobierno/documentos/' . $url . '/' . $id . '" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>';
				break;
			default:
				return '';
				break;
		}
	}
}