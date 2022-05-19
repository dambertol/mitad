<?php setlocale(LC_TIME, "esp"); ?>
<div style="margin: 0 10px 0 10px;">
	<div style="text-align: center;">
		<img src="img/generales/logo_lujan_001.png" alt="Luján de Cuyo" />
		<p style="font-size: 16px; font-weight: bold;">
			MUNICIPALIDAD DE LUJÁN DE CUYO<br />
			DIRECCIÓN DE DEFUNCIONES
		</p>
	</div>
	<div class="fichada_data">
		<p style="text-align: right; font-weight:bold;">
			Luján de Cuyo, Mendoza, <?php echo strftime('%d de %B de %Y', date_timestamp_get(new DateTime($operacion->fecha_tramite))); ?>
		</p>
		<br />
		<p style="font-weight: bold;">
			Sr Intendente Municipal<br />
			Luján de Cuyo<br />
			S________//________D<br />
		</p>
		<p style="text-align:justify; text-indent:6em;">
			Quien suscribe <?php echo $operacion->s_nombre; ?>, con D.N.I. N° <?php echo $operacion->s_dni; ?>,
			domiciliado en calle <?php echo $operacion->s_domicilio; ?><?php if (!empty($operacion->s_domicilio_alt)) echo ' (Domicilio alternativo: ' . $operacion->s_domicilio_alt . ')'; ?><?php if (!empty($operacion->s_telefono)) echo ', teléfono ' . $operacion->s_telefono; ?><?php if (!empty($operacion->s_telefono_alt)) echo ' (Teléfono alternativo: ' . $operacion->s_telefono_alt . ')'; ?>, se dirige a UD. a efectos de solicitar la concesión de un terreno ubicado en el Cuadro N° <?php echo $detalle_operacion->u_cuadro; ?> del
			Cementerio Municipal, por un período de <?php echo $detalle_operacion->tiempo; ?>,
			para la construcción de <?php echo $detalle_operacion->u_tipo === 'Pileta' ? 'una ' . $detalle_operacion->u_tipo : 'un ' . $detalle_operacion->u_tipo; ?> Familiar,
			denominado "<?php echo $detalle_operacion->u_denominacion; ?>".
		</p>
		<p style="text-align:justify; text-indent:6em;">
			La construcción de <?php echo $detalle_operacion->u_tipo === 'Pileta' ? 'dicha ' . $detalle_operacion->u_tipo : 'dicho ' . $detalle_operacion->u_tipo; ?> estará a cargo de <?php echo $detalle_operacion->c_nombre; ?>,
			con D.N.I. N° <?php echo $detalle_operacion->c_dni; ?>.
		</p>
		<p>
			<span style="font-weight: bold; text-decoration: underline;">
				FORMA DE PAGO
			</span>
			<br />
			a) Al contado la suma de pesos ................................................................................. ($ ........................................ ) <br />
			b) En el acto la suma de pesos ................................................................................... ($ ........................................ ) <br />
			y el saldo en (..........) cuotas iguales de pesos ........................................................... ($ ........................................ ) <br />
			cada una con mas el 1,6% de interés mensual sobre el saldo.
		</p>
		<br />
		<br />
		<br />
		<p style="text-align: right;">
			Firma y Aclaración
		</p>
	</div>
	<pagebreak />
	<p style="text-align: justify;">
		El aforo que antecede concuerda con lo determinado en la ORDENANZA GENERAL TRIBUTARIA VIGENTE.
	</p>
	<br />
	<br />
	<br />
	<p style="text-align: right;">
		<?php echo!empty($operacion->agente) ? $operacion->agente : "Firma y Sello"; ?>
	</p>
	<br />
	<br />
	<br />
	<table style="width:100%;">
		<tr>
			<td>PAGO</td>
			<td>.........................</td>
			<td>CUOTA N°</td>
			<td>.........................</td>
			<td>CUOTA N°</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>BOLETA N°</td>
			<td>.........................</td>
			<td>BOLETA N°</td>
			<td>.........................</td>
			<td>BOLETA N°</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>IMPORTE $</td>
			<td>.........................</td>
			<td>IMPORTE $</td>
			<td>.........................</td>
			<td>IMPORTE $</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>SEPELIO $</td>
			<td>.........................</td>
			<td>INTERESES $</td>
			<td>.........................</td>
			<td>INTERESES $</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>INTERESES $</td>
			<td>.........................</td>
			<td>FECHA</td>
			<td>.........................</td>
			<td>FECHA</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>SELLADO $</td>
			<td>.........................</td>
			<td>FIRMA</td>
			<td>.........................</td>
			<td>FIRMA</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>FECHA</td>
			<td>.........................</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>FIRMA</td>
			<td>.........................</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	<br />
	<br />
	<br />
	<table style="width:100%;">
		<tr>
			<td>CUOTA N°</td>
			<td>.........................</td>
			<td>CUOTA N°</td>
			<td>.........................</td>
			<td>CUOTA N°</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>BOLETA N°</td>
			<td>.........................</td>
			<td>BOLETA N°</td>
			<td>.........................</td>
			<td>BOLETA N°</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>IMPORTE $</td>
			<td>.........................</td>
			<td>IMPORTE $</td>
			<td>.........................</td>
			<td>IMPORTE $</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>INTERESES $</td>
			<td>.........................</td>
			<td>INTERESES $</td>
			<td>.........................</td>
			<td>INTERESES $</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>FECHA</td>
			<td>.........................</td>
			<td>FECHA</td>
			<td>.........................</td>
			<td>FECHA</td>
			<td>.........................</td>
		</tr>
		<tr>
			<td>FIRMA</td>
			<td>.........................</td>
			<td>FIRMA</td>
			<td>.........................</td>
			<td>FIRMA</td>
			<td>.........................</td>
		</tr>
	</table>
	<br />
	<br />
	<br />
	<p style="text-align:right; font-weight:bold;">
		Luján de Cuyo, <?php echo strftime('%d de %B de %Y', date_timestamp_get(new DateTime($operacion->fecha))); ?>
	</p>
	<br />
	<p style="text-align: justify;">
		El recurrente hizo efectiva la suma de pesos .......................................................................... ($ .............................)
		según boleta de Ingresos N° ......................................................... de fecha ..................................................................
	</p>
	<br />
	<br />
	<br />
	<p style="text-align: right;">
		<?php echo!empty($operacion->agente) ? $operacion->agente : "Firma y Sello"; ?>
	</p>
</div>