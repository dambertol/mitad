<!--
	/*
	 * Contenido general imprimir planilla de vales PDF.
	 * Autor: Leandro
	 * Creado: 29/03/2017
	 * Modificado: 22/01/2019 (Leandro)
	 */
-->
<?php if (!empty($vales)): ?>
	<h2 style="text-align:center;">Planilla de Retiro de Vales</h2>
	<h4 style="text-align:center;">Desde: <?php echo 'VC' . str_pad($desde, 6, '0', STR_PAD_LEFT); ?> - Hasta: <?php echo 'VC' . str_pad($hasta, 6, '0', STR_PAD_LEFT); ?></h4>
	<?php $area_actual = 0; ?>
	<?php foreach ($vales as $vale): ?>
		<?php if ($area_actual !== $vale->area_id): ?>
			<?php if ($area_actual !== 0): ?>
				</tbody>
				</table>
			<?php endif; ?>
			<h4>
				<?php echo "$vale->area"; ?>
			</h4>
			<table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:2px; font-size:11px;">
				<thead>
					<tr>
						<th style="border:1px solid; width:10%;">Fecha</th>
						<th style="border:1px solid; width:10%;">Número</th>
						<th style="border:1px solid; width:40%;">Área</th>
						<th style="border:1px solid; width:10%;">Tipo</th>
						<th style="border:1px solid; width:10%;">Cantidad</th>
						<th style="border:1px solid; width:20%;">Retira</th>
					</tr>
				</thead>
				<tbody>
					<?php $area_actual = $vale->area_id; ?>
				<?php endif; ?>
				<tr>
					<td style="border:1px solid; text-align:right; padding:4px;"><?php echo date_format(new DateTime(), 'd-m-Y'); ?></td>
					<td style="border:1px solid; padding:4px;"><?php echo 'VC' . str_pad($vale->id, 6, '0', STR_PAD_LEFT); ?></td>
					<td style="border:1px solid; padding:4px;"><?php echo $vale->area; ?></td>
					<td style="border:1px solid; padding:4px;"><?php echo $vale->tipo_combustible; ?></td>
					<td style="border:1px solid; text-align:right; padding:4px;"><?php echo number_format($vale->metros_cubicos, 2, ',', '.'); ?> <?php echo ($vale->tipo_combustible === 'GNC' ? 'M³' : 'L'); ?></td>
					<td style="border:1px solid; padding:4px;">&nbsp;<br />&nbsp;<br /></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>