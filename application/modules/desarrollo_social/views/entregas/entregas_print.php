<!--
	/*
	 * Vista imprimir Entrega.
	 * Autor: Leandro
	 * Creado: 07/10/2019
	 * Modificado: 08/10/2019 (Leandro)
	 */
-->
<script>
	$(document).ready(function() {
		$(".cabecera_logo_avisos").clone().appendTo("#desarrollo_social");
		$("#desarrollo_social_imprimir").clone().appendTo("#desarrollo_social");
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Entregas'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div id="desarrollo_social">
					<div id="desarrollo_social_imprimir" style="height:auto;">
						<div class="desarrollo_social_imprimir_titulo">
							<img src="img/generales/reportes/logo_lujan.png" width="60" alt="Luján de Cuyo">
							<br>Entrega
						</div>
						<div class="desarrollo_social_imprimir_datos">
							<div class="desarrollo_social_imprimir_label">Número: </div>
							<div class="desarrollo_social_imprimir_value"><?php echo $entrega->id; ?></div>
						</div>
						<div class="desarrollo_social_imprimir_datos">
							<div class="desarrollo_social_imprimir_label">Fecha: </div>
							<div class="desarrollo_social_imprimir_value"><?php echo date_format(new DateTime($entrega->fecha), 'd-m-Y'); ?></div>
						</div>
						<div style="min-height: 300px;"> 
							<table class="desarrollo_social_imprimir_tabla">
								<tbody>
									<tr>
										<td class="desarrollo_social_imprimir_tabla_titulos">Destino:</td>
										<td><?php echo $entrega->destino; ?></td>
										<td colspan="4"></td>
									</tr>
									<tr>
										<td class="desarrollo_social_imprimir_tabla_titulos">Estado:</td>
										<td><?php echo strtoupper($entrega->estado); ?></td>
										<td colspan="4"></td>
									</tr>
									<tr>
										<td class="desarrollo_social_imprimir_tabla_titulos">Responsable:</td>
										<td><?php echo $entrega->responsable; ?></td>
										<td colspan="4"></td>
									</tr>
									<tr>
										<td style="width: 10%;" class="desarrollo_social_imprimir_tabla_titulos">Beneficiario:</td>
										<td><?php echo $entrega->beneficiario; ?></td>
										<td style="width: 6%;" class="desarrollo_social_imprimir_tabla_titulos">Apros:</td>
										<td><?php echo $entrega->nro_apros; ?></td>
										<td style="width:5%;" class="desarrollo_social_imprimir_tabla_titulos">Domicilio:</td>
										<td><?php echo $entrega->domicilio; ?></td>
									</tr>
									<tr>
										<td style="width:15%;"></td>
										<td style="width:40%;" class="desarrollo_social_imprimir_tabla_titulos">Artículo</td>
										<td style="width:20%;" class="desarrollo_social_imprimir_tabla_titulos desarrollo_social_imprimir_tabla_derecha">Cantidad</td>
										<td style="width:25%;" class="desarrollo_social_imprimir_tabla_titulos desarrollo_social_imprimir_tabla_derecha">Expediente</td>
									</tr>
									<?php
									if (!empty($detalles))
										foreach ($detalles as $detalle)
										{
											?>
											<tr>
												<td></td>
												<td><?php echo "$detalle->articulo" . (empty($detalle->caracteristica_articulo) ? "" : " | $detalle->caracteristica_articulo"); ?></td>
												<td class="desarrollo_social_imprimir_tabla_derecha"><?php echo number_format($detalle->cantidad, 2, ',', '.'); ?></td>
												<td class="desarrollo_social_imprimir_tabla_derecha"> <?php echo $detalle->expediente; ?></td>
											</tr>
											<?php
										}
									?>
								</tbody>
							</table>
						</div>
						<table cellspacing="20" >
							<td style="width: 6%;" class="desarrollo_social_imprimir_tabla_titulos">Firma Beneficiario  ................</td>                  
							<td style="width: 6%;" class="desarrollo_social_imprimir_tabla_titulos">Firma Responsable   .................</td>
						</table>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center hidden-print desarrollo_social_imprimir_botones">
					<a href="desarrollo_social/compras/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
			</div>
		</div>
	</div>
</div>