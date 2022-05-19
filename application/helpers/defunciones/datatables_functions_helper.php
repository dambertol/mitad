<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Funciones para DataTables
 * Autor: Leandro
 * Creado: 26/11/2019
 * Modificado: 29/11/2019 (Leandro)
 */
if (!function_exists('dt_column_difuntos_ubicacion'))
{

	function dt_column_difuntos_ubicacion($tipo, $sector, $fila, $nicho, $cuadro, $denominacion)
	{
		switch ($tipo)
		{
			case 'Nicho':
				$ubicacion = "S: $sector - F: $fila - N: $nicho";
				break;
			case 'Tierra':
				$ubicacion = "S: $sector - C: $cuadro - F: $fila - P: $nicho";
				break;
			case 'Mausoleo':
				$ubicacion = "C: $cuadro - D: $denominacion";
				break;
			case 'Pileta':
				$ubicacion = "C: $cuadro - D: $denominacion";
				break;
			case 'Nicho Urna':
				$ubicacion = "S: $sector - F: $fila - N: $nicho";
				break;
			default:
				$ubicacion = '';
		}

		return $ubicacion;
	}
}

if (!function_exists('dt_column_operaciones_tipo'))
{

	function dt_column_operaciones_tipo($tipo)
	{
		switch ($tipo)
		{
			case '1':
				$tipo_nombre = "Concesión";
				break;
			case '2':
				$tipo_nombre = "Ornato";
				break;
			case '3':
				$tipo_nombre = "Reducción";
				break;
			case '4':
				$tipo_nombre = "Traslado";
				break;
			case '5':
				$tipo_nombre = "Compra Terreno";
				break;
			default:
				$tipo_nombre = '';
		}

		return $tipo_nombre;
	}
}