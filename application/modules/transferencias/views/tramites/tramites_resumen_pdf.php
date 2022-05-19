<table style="width:100%; font-family:serif; margin:2px; font-size:11px;">
	<tr>
		<td style="width:25%; text-align:left;">
			<img src="img/generales/reportes/logo_lujan.png" alt="Luján de Cuyo" style="width:10%;"/>
		</td>
		<td style="width:50%; font-size:22px; font-weight:bold; text-align:center; vertical-align:middle;">
			DIRECCIÓN DE CATASTRO
		</td>
		<td style="width:25%; text-align:right;">
			<img src="img/generales/reportes/logo_escudo.png" alt="Luján de Cuyo" style="width:10%;"/>
		</td>
	<tr>
		<td colspan="3" style="font-size:16px; text-align:center;">
			BOLETA DE TRANSFERENCIA N°:<?php echo "$tramite->transferencia_nro/$tramite->transferencia_eje"; ?>
		</td>
	</tr>
	<tr>
		<td colspan="3" style="font-size:16px; text-align:center;">
			TRAMITE ON-LINE N°:<?php echo $tramite->id; ?>
		</td>
	</tr>

</tr>
</table>
<table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:2px; font-size:11px;">
	<thead>
		<tr>
			<th style="border:1px solid; background-color:#CCC;" colspan="2">TRANSFERENCIA</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="border:1px solid; padding:2px; width:15%; font-weight:bold;">Tipo</td>
			<td style="border:1px solid; padding:2px;"><?php echo $tramite->tipo; ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Inicio</td>
			<td style="border:1px solid; padding:2px;"><?php echo date_format(new DateTime($tramite->fecha_inicio), 'd/m/Y'); ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Fin</td>
			<td style="border:1px solid; padding:2px;"><?php echo empty($tramite->fecha_fin) ? '' : date_format(new DateTime($tramite->fecha_fin), 'd/m/Y'); ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Escritura</td>
			<td style="border:1px solid; padding:2px;"><?php echo "N°: $tramite->escritura_nro Foja: $tramite->escritura_foja Fecha: " . (empty($tramite->escritura_fecha) ? '' : date_format(new DateTime($tramite->escritura_fecha), 'd/m/Y')); ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Observaciones</td>
			<td style="border:1px solid; padding:2px;"><?php echo $tramite->observaciones; ?></td>
		</tr>
	</tbody>
</table>
<table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:2px; font-size:11px;">
	<thead>
		<tr>
			<th style="border:1px solid; background-color:#CCC;" colspan="2">ESCRIBANO</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="border:1px solid; padding:2px; width:15%; font-weight:bold;">Matrícula</td>
			<td style="border:1px solid; padding:2px;"><?php echo $tramite->matricula_nro; ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Registro</td>
			<td style="border:1px solid; padding:2px;"><?php echo "$tramite->registro_nro ($tramite->registro_tipo)"; ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">CUIL</td>
			<td style="border:1px solid; padding:2px;"><?php echo substr($tramite->cuil, 0, 2) . "-" . substr($tramite->cuil, 2, 8) . "-" . substr($tramite->cuil, 10); ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Apellido y Nombre</td>
			<td style="border:1px solid; padding:2px;"><?php echo "$tramite->apellido, $tramite->nombre"; ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Teléfono</td>
			<td style="border:1px solid; padding:2px;"><?php echo $tramite->telefono; ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Celular</td>
			<td style="border:1px solid; padding:2px;"><?php echo $tramite->celular; ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Email</td>
			<td style="border:1px solid; padding:2px;"><?php echo $tramite->email; ?></td>
		</tr>
	</tbody>
</table>
<?php $vend = 1; ?>
<?php foreach ($vendedores as $Vendedor): ?>
	<table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:2px; font-size:11px;">
		<thead>
			<tr>
				<th style="border:1px solid; background-color:#CCC;" colspan="2">TRANSMITENTE <?php echo $vend; ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="border:1px solid; padding:2px; width:15%; font-weight:bold;">Porcentaje</td>
				<td style="border:1px solid; padding:2px;"><?php echo number_format($Vendedor->porcentaje, 2, ',', '.') . "%"; ?></td>
			</tr>
			<tr>
				<td style="border:1px solid; padding:2px; font-weight:bold;">CUIL</td>
				<td style="border:1px solid; padding:2px;"><?php echo substr($Vendedor->cuil, 0, 2) . "-" . substr($Vendedor->cuil, 2, 8) . "-" . substr($Vendedor->cuil, 10); ?></td>
			</tr>
			<tr>
				<td style="border:1px solid; padding:2px; font-weight:bold;">Apellido y Nombre</td>
				<td style="border:1px solid; padding:2px;"><?php echo "$Vendedor->apellido, $Vendedor->nombre"; ?></td>
			</tr>
			<tr>
				<td style="border:1px solid; padding:2px; font-weight:bold;">Email</td>
				<td style="border:1px solid; padding:2px;"><?php echo $Vendedor->email; ?></td>
			</tr>
		</tbody>
	</table>
	<?php $vend++; ?>
<?php endforeach; ?>
<?php $comp = 1; ?>
<?php foreach ($compradores as $Comprador): ?>
	<table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:2px; font-size:11px;">
		<thead>
			<tr>
				<th style="border:1px solid; background-color:#CCC;" colspan="2">ADQUIRENTE <?php echo $comp; ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="border:1px solid; padding:2px; width:15%; font-weight:bold;">Porcentaje</td>
				<td style="border:1px solid; padding:2px;"><?php echo number_format($Comprador->porcentaje, 2, ',', '.') . "%"; ?></td>
			</tr>
			<tr>
				<td style="border:1px solid; padding:2px; font-weight:bold;">CUIL</td>
				<td style="border:1px solid; padding:2px;"><?php echo substr($Comprador->cuil, 0, 2) . "-" . substr($Comprador->cuil, 2, 8) . "-" . substr($Comprador->cuil, 10); ?></td>
			</tr>
			<tr>
				<td style="border:1px solid; padding:2px; font-weight:bold;">Apellido y Nombre</td>
				<td style="border:1px solid; padding:2px;"><?php echo "$Comprador->apellido, $Comprador->nombre"; ?></td>
			</tr>
			<tr>
				<td style="border:1px solid; padding:2px; font-weight:bold;">Email</td>
				<td style="border:1px solid; padding:2px;"><?php echo $Comprador->email; ?></td>
			</tr>
			<tr>
				<td style="border:1px solid; padding:2px; font-weight:bold;">Domicilio</td>
				<td style="border:1px solid; padding:2px;"><?php echo "Calle: $Comprador->calle Altura: $Comprador->altura Piso: $Comprador->piso Dpto: $Comprador->dpto Barrio: $Comprador->barrio Manzana: $Comprador->manzana Casa: $Comprador->casa Localidad: $Comprador->localidad"; ?></td>
			</tr>
		</tbody>
	</table>
	<?php $comp++; ?>
<?php endforeach; ?>
<table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:2px; font-size:11px;">
	<thead>
		<tr>
			<th style="border:1px solid; background-color:#CCC;" colspan="2">INMUEBLE</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="border:1px solid; padding:2px; width:15%; font-weight:bold;">Padrón Municipal</td>
			<td style="border:1px solid; padding:2px;"><?php echo $tramite->padron; ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Nomenclatura</td>
			<td style="border:1px solid; padding:2px;"><?php echo $tramite->nomenclatura; ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Superficie Título</td>
			<td style="border:1px solid; padding:2px;"><?php echo number_format($tramite->sup_titulo, 2, ',', '.'); ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Superficie Mensura</td>
			<td style="border:1px solid; padding:2px;"><?php echo number_format($tramite->sup_mensura, 2, ',', '.'); ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Superficie Afectada</td>
			<td style="border:1px solid; padding:2px;"><?php echo number_format($tramite->sup_afectada, 2, ',', '.'); ?></td>
		</tr>
		<tr>
			<td style="border:1px solid; padding:2px; font-weight:bold;">Superficie Cubierta</td>
			<td style="border:1px solid; padding:2px;"><?php echo number_format($tramite->sup_cubierta, 2, ',', '.'); ?></td>
		</tr>
	</tbody>
</table>
<table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:2px; font-size:11px;">
	<thead>
		<tr>
			<th style="border:1px solid; background-color:#CCC;" colspan="2">OBSERVACIONES FINALIZACIÓN</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($pases as $Pase): ?>
			<tr>
				<td style="border:1px solid; padding:2px; width:15%; font-weight:bold;"><?php echo (new DateTime($Pase->fecha))->format('d/m/Y H:i'); ?></td>
				<td style="border:1px solid; padding:2px;"><?php echo $Pase->observaciones; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<br>
<div style="padding:0;">
	<div style="text-align:right; vertical-align:bottom;">
		<barcode code="https://transferencias.lujandecuyo.gob.ar/transferencias/tramites/ver/10/" type="QR" class="barcode" size="1.3" error="M" disableborder="1" />
		<br>
		<a href="https://transferencias.lujandecuyo.gob.ar/transferencias/tramites/ver/10/" target="_blank">Ver Datos ON-LINE</a>
	</div>
</div>
