<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 10/12/2019
 * Modificado: 27/02/2020 (Leandro)
 */
if (!function_exists('dt_column_usuarios_legajos_legajos'))
{

	function dt_column_usuarios_legajos_legajos($cantidad)
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

if (!function_exists('dt_column_bonos_envio'))
{

	function dt_column_bonos_envio($envio)
	{
		switch ($envio)
		{
			case 'Falló':
				return '<span class="label label-danger" style="margin:0 auto;">Falló</span>';
				break;
			case 'Enviado':
				return '<span class="label label-success" style="margin:0 auto;">Enviado</span>';
				break;
			case 'Enviando':
				return '<span class="label label-warning" style="margin:0 auto;">Enviando</span>';
				break;
			case 'Pendiente':
				return '<span class="label label-info" style="margin:0 auto;">Pendiente</span>';
				break;
			default:
				return '<span class="label label-default" style="margin:0 auto;">Sin Envío</span>';
				break;
		}
	}
}