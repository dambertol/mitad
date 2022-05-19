<!--
	/*
	 * Vista imprimir Compra.
	 * Autor: Leandro
	 * Creado: 22/10/2019
	 * Modificado: 22/10/2019 (Leandro)
	 */
-->
<script>
	$(document).ready(function() {
		$(".cabecera_logo_avisos").clone().appendTo("#obrador");
		$("#obrador_imprimir").clone().appendTo("#obrador");
		window.print();
	});
</script>
<?php if (!empty($error)) : ?>
	<div class="alert alert-danger alert-dismissible fade in alert-fixed" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong>ERROR!</strong><?php echo $error; ?>
	</div>
<?php endif; ?>
<?php if (!empty($message)) : ?>
	<div class="alert alert-success alert-dismissible fade in alert-fixed" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong>OK!</strong><?php echo $message; ?>
	</div>
<?php endif; ?>
<div class="row">
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Compras'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div id="obrador">
					<div id="obrador_imprimir">
						<div class="obrador_imprimir_titulo">
							<img src="img/generales/reportes/logo_lujan.png" width="60" alt="Luján de Cuyo">
							<br>COMPRA
						</div>
						<div class="obrador_imprimir_datos">
							<div class="obrador_imprimir_label">Número: </div>
							<div class="obrador_imprimir_value"><?php echo $compra->id; ?></div>
						</div>
						<div class="obrador_imprimir_datos">
							<div class="obrador_imprimir_label">Fecha: </div>
							<div class="obrador_imprimir_value"><?php echo date_format(new DateTime($compra->fecha_recepcion), 'd-m-Y'); ?></div>
						</div>
						<table class="obrador_imprimir_tabla">
							<tbody>
								<tr>
									<td class="obrador_imprimir_tabla_titulos">Recepcionista:</td>
									<td><?php echo $compra->recepcionista; ?></td>
									<td colspan="4"></td>
								</tr>
								<tr>
									<td class="obrador_imprimir_tabla_titulos">Destino:</td>
									<td><?php echo $compra->destino; ?></td>
									<td colspan="4"></td>
								</tr>
								<tr>
									<td class="obrador_imprimir_tabla_titulos">Expediente:</td>
									<td><?php echo $compra->expediente; ?></td>
									<td colspan="4"></td>
								</tr>
								<tr>
									<td class="obrador_imprimir_tabla_titulos">Nro Orden:</td>
									<td><?php echo $compra->nro_orden; ?></td>
									<td colspan="4"></td>
								</tr>
								<tr>
									<td class="obrador_imprimir_tabla_titulos">Lugar Físico:</td>
									<td><?php echo $compra->lugar_fisico; ?></td>
									<td colspan="4"></td>
								</tr>
								<tr>
									<td class="obrador_imprimir_tabla_titulos">Proveedor:</td>
									<td><?php echo $compra->proveedor; ?></td>
									<td colspan="4"></td>
								</tr>
								<tr>
									<td style="width:15%;"></td>
									<td style="width:40%;" class="obrador_imprimir_tabla_titulos">Artículo</td>
									<td style="width:10%;" class="obrador_imprimir_tabla_titulos obrador_imprimir_tabla_derecha">Cantidad</td>
									<td style="width:10%;" class="obrador_imprimir_tabla_titulos obrador_imprimir_tabla_derecha">Valor</td>
									<td style="width:10%;" class="obrador_imprimir_tabla_titulos obrador_imprimir_tabla_derecha">Total</td>
								</tr>
								<?php
								if (!empty($detalles))
									foreach ($detalles as $detalle)
									{
										?>
										<tr>
											<td></td>
											<td><?php echo "$detalle->articulo" . (empty($detalle->caracteristica_articulo) ? "" : " | $detalle->caracteristica_articulo"); ?></td>
											<td class="obrador_imprimir_tabla_derecha"><?php echo number_format($detalle->cantidad, 2, ',', '.'); ?></td>
											<td class="obrador_imprimir_tabla_derecha">$ <?php echo number_format($detalle->valor, 2, ',', '.'); ?></td>
											<td class="obrador_imprimir_tabla_derecha">$ <?php echo number_format($detalle->cantidad * $detalle->valor, 2, ',', '.'); ?></td>
										</tr>
										<?php
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center hidden-print obrador_imprimir_botones">
					<a href="obrador/compras/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
			</div>
		</div>
	</div>
</div>