<!--
	/*
	 * Vista ABM de Ordenes.
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
				<?php if (!empty($orden_compra)): ?>
					<b>Proveedor: </b>
					<?php echo '(' . $orden_compra->Tprov_ProveedorTipo . '/' . $orden_compra->Prov_Proveedor . ') ' . $orden_compra->Prov_NombreFantasia; ?>
					<br />
					<b>Días mantenimiento oferta: </b>
					<?php echo $orden_compra->Ocom_DiasMantOferta . ' ' . $orden_compra->Ocom_DescMantOferta; ?>
					<br />
					<b>Días plazo entrega: </b>
					<?php echo $orden_compra->Ocom_DiasPlazoEntrega . ' ' . $orden_compra->Ocom_DescPlazoEntrega; ?>
					<br />
					<b>Lugar Entrega: </b>
					<?php echo $orden_compra->Ocom_LugarEntrega; ?>
					<br />
					<b>Días condición pago: </b>
					<?php echo $orden_compra->Ocom_DiasCondicionPago . ' ' . $orden_compra->Ocom_DescCondicionPago; ?>
					<br />
					<b>Fecha Afectación: </b>
					<?php echo date_format(new DateTime($orden_compra->OCom_Afectacion), 'd-m-Y'); ?>
					<br />
					<br />
					<?php if (!empty($orden_compra->items)): ?>
						<table class="table table-striped table-hover table-condensed">
							<thead>
								<tr>
									<th>Nro</th>
									<th>Cantidad</th>
									<th>Artículo</th>
									<th>Descripción</th>
									<th>Precio Unitario</th>
									<th>Importe Total</th>
								</tr>
							</thead>
							<tbody>
								<?php $monto_total = 0; ?>
								<?php foreach ($orden_compra->items as $item): ?>
									<?php if (empty($item->Ocom_Cantidad) || $item->Ocom_Cantidad <= 0): ?>
										<tr>
											<td style="text-align:right;"><?php echo $item->Ioco_Renglon; ?> </td>
											<td style="text-align:right;"></td>
											<td style="text-align:right;"></td>
											<td style="font-size:80%;"><?php echo $item->Ocom_Descripcion; ?> </td>
											<td style="text-align:right;"></td>
											<td style="text-align:right;"></td>
										</tr>
									<?php else: ?>
										<tr>
											<td style="text-align: right;"><?php echo $item->Ioco_Renglon; ?> </td>
											<td style="text-align: right;"><?php echo $item->Ocom_Cantidad; ?> </td>
											<td style="text-align: right;"><?php echo $item->Obj_Objeto; ?> </td>
											<td><?php echo $item->Ocom_Descripcion; ?> </td>
											<td style="text-align: right;"><?php echo '$ ' . number_format($item->Ocom_Precio, 2, ',', '.'); ?> </td>
											<td style="text-align: right; font-weight: bold;"><?php echo '$ ' . number_format($item->Ocom_Importe, 2, ',', '.'); ?> </td>
										</tr>
									<?php endif; ?>
									<?php $monto_total += $item->Ocom_Importe; ?>
								<?php endforeach; ?>
								<tr>
									<td style="text-align:center; font-weight: bold;" colspan="4">Total</td>
									<td style="text-align:right; font-weight: bold;" colspan="2"><?php echo '$ ' . number_format($monto_total, 2, ',', '.'); ?> </td>
								</tr>
							</tbody>
						</table>
					<?php endif; ?>
				<?php else: ?>
					<h3 class="text-center">No se encontró la orden de compra</h3>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>