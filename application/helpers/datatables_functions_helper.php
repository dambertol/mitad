<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Datatables Functions Helper
 *
 * @package    CodeIgniter
 * @subpackage helpers
 * @category   helper
 * @version    0.0.1
 * @author     ZettaSys <info@zettasys.com.ar>
 *
 */
if (!function_exists('dt_column_usuarios_estado'))
{

	function dt_column_usuarios_estado($estado, $id, $modulo = '')
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
if (!function_exists('dt_column_grupos_usuarios'))
{

	function dt_column_grupos_usuarios($estado, $cantidad)
	{
		switch ($estado)
		{
			case 1:
				return '<span class="label label-success">' . $cantidad . '</span>';
				break;
			case 0:
				return '<span class="label label-danger">' . $cantidad . '</span>';
				break;
			default:
				return $cantidad;
				break;
		}
	}
}
if (!function_exists('dt_column_modulos_grupos'))
{

	function dt_column_modulos_grupos($cantidad)
	{
		if ($cantidad > 0)
		{
			return '<span class="label label-success">' . $cantidad . '</span>';
		}
		else
		{
			return '<span class="label label-danger">' . $cantidad . '</span>';
		}
	}
}