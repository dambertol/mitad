<!--
	/*
	 * Vista ABM de Solicitudes.
	 * Autor: Leandro
	 * Creado: 14/08/2019
	 * Modificado: 14/08/2019 (Leandro)
	 */
-->
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Hobbies'; ?></h2>
				<?php if (!empty($audi_modal)): ?>
					<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
						<i class="fa fa-info-circle"></i>
					</button>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php if (!empty($comprobante)): ?>
					<?php if (!empty($comprobante->items)): ?>
						<table class="table table-striped table-hover table-condensed">
							<thead>
								<tr>
									<th style="text-align:right;">Nro</th>
									<th style="text-align:right;">Cantidad</th>
									<th style="text-align:right;">Artículo</th>
									<th>Descripción Pedido</th>
									<th>Muestra</th>
									<th style="text-align:right;">Precio Unitario</th>
									<th style="text-align:right;">Importe Total</th>
								</tr>
							</thead>
							<tbody>
								<?php $monto_total = 0; ?>
								<?php foreach ($comprobante->items as $item): ?>
									<?php if (empty($item->IPed_Cantidad) || $item->IPed_Cantidad <= 0): ?>
										<tr>
											<td style="text-align:right;"><?php echo $item->IPed_Renglon; ?> </td>
											<td style="text-align:right;"></td>
											<td style="text-align:right;"></td>
											<td style="font-size:80%;"><?php echo $item->IPed_Descripcion; ?> </td>
											<td></td>
											<td style="text-align:right;"></td>
											<td style="text-align:right;"></td>
										</tr>
									<?php else: ?>
										<tr>
											<td style="text-align:right;"><?php echo $item->IPed_Renglon; ?> </td>
											<td style="text-align:right;"><?php echo $item->IPed_Cantidad; ?> </td>
											<td style="text-align:right;"><?php echo $item->Obj_Objeto; ?> </td>
											<td><?php echo $item->IPed_Descripcion; ?> </td>
											<td><?php echo $item->IPed_Muestra; ?> </td>
											<td style="text-align:right;"><?php echo '$ ' . number_format($item->IPed_Precio, 2, ',', '.'); ?> </td>
											<td style="text-align:right; font-weight: bold;"><?php echo '$ ' . number_format($item->IPed_Importe, 2, ',', '.'); ?> </td>
										</tr>
									<?php endif; ?>
									<?php $monto_total += $item->IPed_Importe; ?>
								<?php endforeach; ?>
								<tr>
									<td style="text-align:center; font-weight: bold;" colspan="5">Total</td>
									<td style="text-align:right; font-weight: bold;" colspan="2"><?php echo '$ ' . number_format($monto_total, 2, ',', '.'); ?> </td>
								</tr>
							</tbody>
						</table>
					<?php endif; ?>
					<table class="table table-striped table-hover table-condensed">
						<thead>
							<tr>
								<th>Fecha</th>
								<th>Detalle Pase</th>
								<th>Remito</th>
								<th>Oficina Origen / Comprobante</th>
								<th>Oficina Destino / Expediente</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1; ?>
							<?php if (!empty($comprobante->pases)): ?>
								<?php foreach ($comprobante->pases as $pase): ?>
									<?php $comprobante->anexos[] = (object) array('pase' => $pase, 'fecha' => date_format(new DateTime($pase->FechaRemito), 'Ymd') . $i++); ?>
								<?php endforeach; ?>
							<?php endif; ?>
							<?php if (!empty($comprobante->avances)): ?>
								<?php foreach ($comprobante->avances as $avance): ?>
									<?php $comprobante->anexos[] = (object) array('avance' => $avance, 'fecha' => date_format(new DateTime($avance->FechaAvance), 'Ymd') . $i++); ?>
								<?php endforeach; ?>
							<?php endif; ?>
							<?php if (!empty($comprobante->anexos)): ?>
								<?php

								function sort_anexos_comprobantes($a, $b)
								{
									return strcmp($a->fecha, $b->fecha);
								}
								usort($comprobante->anexos, "sort_anexos_comprobantes");
								?>
								<?php foreach ($comprobante->anexos as $anexo): ?>
									<?php if (!empty($anexo->pase)): ?>
										<?php $pase = $anexo->pase; ?>
										<tr style="background-color:#82C0D5;">
											<td><?php echo date_format(new DateTime($pase->FechaRemito), 'd-m-Y H:i'); ?> </td>
											<td style="font-size:80%;"><?php echo $pase->DetallePase; ?> </td>
											<td><?php echo $pase->Remito; ?> </td>
											<td style="font-size:80%;"><?php echo "$pase->OfiEjerO - $pase->OficinaO | $pase->NomOficinaO"; ?> </td>
											<td style="font-size:80%;"><?php echo "$pase->OfiEjerD - $pase->OficinaD | $pase->NomOficinaD"; ?> </td>
										</tr>
									<?php endif; ?>
									<?php if (!empty($anexo->avance)): ?>
										<?php $avance = $anexo->avance; ?>
										<tr style="background-color:#A6C37B;">
											<td><?php echo date_format(new DateTime($avance->FechaAvance), 'd-m-Y'); ?> </td>
											<td colspan="2" style="font-size:80%;">
												<?php echo ($avance->TipoComprobante === 'ORDENES DE COMPRA') ? $avance->TipoComprobante . ' <a style="color:#FF0000;" target="_blank" href="major/solicitudes/orden/' . $avance->EjercicioAvance . '/' . $avance->OficinaAvance . '/' . $avance->TipoAvance . '/' . $avance->NumeroAvance . '">VER</a>' : $avance->TipoComprobante; ?>
											</td>
											<td><?php echo "$avance->TipoAvance - $avance->EjercicioAvance - $avance->OficinaAvance - $avance->NumeroAvance"; ?> </td>
											<td><?php echo empty($avance->EjercicioExp) ? "" : "$avance->EjercicioExp / $avance->NumeroExp"; ?> </td>
										</tr>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
					<?php if (!empty($comprobante->expediente)): ?>
						<h3>Información Expediente N° <?php echo $comprobante->expediente[0]->NumeroExp . '/' . $comprobante->expediente[0]->EjercicioExp; ?></h3>
						<h4>Solicitud <?php echo $comprobante_desc; ?></h4>
						<b>Oficina: </b>
						<?php echo $comprobante->expediente[0]->OficinaEjercicioExp . ' - ' . $comprobante->expediente[0]->OficinaExp . ' | ' . $comprobante->expediente[0]->NomOficinaExp; ?>
						<br>
						<b>Fecha Creación: </b>
						<?php echo date_format(new DateTime($comprobante->expediente[0]->FechaIngresoExp), 'd-m-Y'); ?>
						<br>
						<b>Tema: </b>
						<?php echo $comprobante->expediente[0]->TemaExp; ?>
						<br>
						<b>Descripción: </b>
						<?php echo $comprobante->expediente[0]->DescripcionExp; ?>
						<table class="table table-striped table-hover table-condensed">
							<thead>
								<tr>
									<th>Fecha</th>
									<th>Detalle Remito</th>
									<th>Remito</th>
									<th>Oficina Origen</th>
									<th>Oficina Destino</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($comprobante->expediente as $pase): ?>
									<tr class="fila_pase_comprobante">
										<td><?php echo date_format(new DateTime($pase->FechaRemito), 'd-m-Y H:i'); ?> </td>
										<td style="font-size:80%;"><?php echo $pase->DetalleRemito; ?> </td>
										<td><?php echo $pase->Remito; ?> </td>
										<td style="font-size:80%;"><?php echo "$pase->OfiEjerO - $pase->OficinaO | $pase->NomOficinaO"; ?> </td>
										<td style="font-size:80%;"><?php echo "$pase->OfiEjerD - $pase->OficinaD | $pase->NomOficinaD"; ?> </td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
					<table class="table table-condensed">
						<thead>
							<tr>
								<th>Referencias</th>
							</tr>
						</thead>
						<tbody>
							<tr style="background-color:#82C0D5;">
								<td>Pases</td>
							</tr>
							<tr style="background-color:#A6C37B;">
								<td>Avances</td>
							</tr>
						</tbody>
					</table>
				<?php else: ?>
					<h3 class="text-center">No se encontró la solicitud</h3>
				<?php endif; ?>
				<div class="ln_solid"></div>
				<div class="text-center">
					<a href="major/solicitudes/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>