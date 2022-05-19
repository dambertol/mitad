<?php setlocale(LC_TIME, "esp"); ?>
<div style="margin:0 10px 0 10px;">
	<div style="text-align:center;">
		<img src="img/generales/logo_lujan_001.png" alt="Luján de Cuyo" />
		<p style="font-size:16px; font-weight:bold;">
			MUNICIPALIDAD DE LUJÁN DE CUYO<br />
			DIRECCIÓN DE DEFUNCIONES
		</p>
	</div>
	<div class="fichada_data">
		<p style="text-align:right; font-weight:bold;">
			Luján de Cuyo, <?php echo strftime('%d de %B de %Y', date_timestamp_get(new DateTime())); ?>
		</p>
		<br />
		<p>
			<b>Referencia:</b><br />
			<?php echo '<b>Difunto:</b> ' . (!empty($concesion->difunto) ? $concesion->difunto : '-'); ?> <br />
			<?php echo '<b>Fecha defunción:</b> ' . (!empty($concesion->defuncion) ? $concesion->defuncion : '-'); ?> <br />
			<?php echo '<b>Ubicación:</b> ' . (!empty($concesion->ubicacion) ? $concesion->ubicacion : '-'); ?> <br />
			<?php echo '<b>Expediente:</b> ' . (!empty($concesion->expediente) ? $concesion->expediente : '-'); ?> <br />
		</p>
		<p>
			<b>Notificar a:</b><br />
			<?php echo '<b>Solicitante:</b> ' . (!empty($concesion->solicitante) ? $concesion->solicitante : '-'); ?> <br />
			<?php echo '<b>DNI:</b> ' . (!empty($concesion->solicitante_dni) ? $concesion->solicitante_dni : '-'); ?> <br />
			<?php echo '<b>Dirección:</b> ' . (!empty($concesion->solicitante_direccion) ? $concesion->solicitante_direccion : '-'); ?> <br />
			<?php echo '<b>Teléfono:</b> ' . (!empty($concesion->solicitante_telefono) ? $concesion->solicitante_telefono : '-'); ?> <br />
		</p>
		<p style="text-align:justify; text-indent:6em;">
			Me dirijo a UD. a efectos de informarle que en un plazo de 5 días hábiles, a partir de recibida la presente notificación, deberá presentarse en la Oficina de Defunciones de la Municipalidad de Luján de Cuyo, sita en calle XX de Septiembre al 83, a los que efectos que se le informarán oportunamente.
		</p>
		<p style="text-align:justify; text-indent:6em;">
			Es indispensable que se presente portando esta notificación.
		</p>
		<p style="text-align:justify; text-indent:6em;">
			Atentamente
		</p>
		<br />
		<br />
		<br />
		<p style="text-align:center;">
			Firma y sello<br />
			Encargado Defunciones
		</p>
		<br />
		<br />
		<br />
		<br />
		<br />
		<div style="text-align:center;width:50%;float:left;">
			Firma y aclaración<br />
			Notificado
		</div>
		<div style="text-align:center;width:50%;float:right;">
			Firma y sello<br />
			Notificador
		</div>
	</div>
</div>