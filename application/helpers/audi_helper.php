<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Auditoría
 * Autor: Leandro
 * Creado: 04/12/2017
 * Modificado: 16/01/2018 (Leandro)
 */
if (!function_exists('audi_modal'))
{

	function audi_modal($registro, $id = 'audi-modal')
	{
		$modal = '<div class="modal fade" id="' . $id . '" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Última acción</h4>
			</div>
			<div class="modal-body">';
		if (!empty($registro->audi_accion))
		{
			$modal .= '<p><b>Usuario:</b> ' . $registro->audi_usuario . '</p>
				<p><b>Fecha:</b> ' . date_format(new DateTime($registro->audi_fecha), "d/m/Y H:i:s") . '</p>
				<p><b>Acción:</b> ' . ($registro->audi_accion === "I" ? "Creación" : "Modificación" ) . '</p>';
		}
		else
		{
			$modal .= '<p><b>SIN DATOS</b></p>';
		}
		$modal .= '</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>';

		return $modal;
	}
}