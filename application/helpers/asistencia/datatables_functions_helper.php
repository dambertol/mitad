<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 28/11/2017
 * Modificado: 16/10/2018 (Leandro)
 */
if (!function_exists('dt_column_asistencia_usuarios_estado'))
{

	function dt_column_asistencia_usuarios_estado($estado, $id, $modulo = '')
	{
		switch ($estado)
		{
			case 1:
				return '<a href="#" onclick="desactivar_usuario(' . $id . ');return false;" title="Desactivar"><span class="label label-success">Activo</span></a>';
				break;
			case 0:
				return '<a href="#" onclick="activar_usuario(' . $id . ');return false;" title="Activar"><span class="label label-danger">Inactivo</span></a>';
				break;
			default:
				return $estado;
				break;
		}
	}
}
if (!function_exists('dt_column_asistencia_usuarios_asignar'))
{

	function dt_column_asistencia_usuarios_asignar($group_name = '', $id = '')
	{
		if ($group_name === 'asistencia_director' || $group_name === 'asistencia_contralor')
		{
			return '<a href="asistencia/usuarios/asignar/' . $id . '" title="Asignar oficinas" class="btn btn-primary btn-xs"><i class="fa fa-sitemap"></i></a>';
		}
		else
		{
			return '';
		}
	}
}