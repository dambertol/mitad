<div style="font-family: Arial; font-size: 14px;">
	<div style="text-align: center;"><img style="width:100px;" src="img/telefonia/logo_lujan_comodato.png" alt="Luján de Cuyo"/></div>
	<p style="text-align: center; font-weight: bold; font-size: 12px;">DIRECCIÓN DE INFORMÁTICA Y COMUNICACIONES</p>
	<p style="text-align: center; font-weight: bold; text-decoration: underline; font-size: 18px;">Devolución de Equipo</p>
	<div style="float: left; width: 50%; font-weight: bold; font-size: 15px;">Comodato N°: <?php echo $comodato->id; ?></div>
	<div style="float: right; text-align: right; width: 50%; font-weight: bold; font-size: 15px;">Fecha: <?php echo date_format(new Datetime($comodato->fecha_generacion), 'd/m/Y'); ?></div>
	<p style="text-align: justify;">
		Por medio de la presente hago constar que:<br />
		RECIBÍ DE: <?php echo (!empty($comodato->persona_equipo) ? $comodato->persona_equipo : (!empty($comodato->persona_linea) ? $comodato->persona_linea : '...........................................................................')); ?>, DNI: <?php echo (!empty($comodato->dni_persona_equipo) ? $comodato->dni_persona_equipo : (!empty($comodato->dni_persona_linea) ? $comodato->dni_persona_linea : '..................................................')); ?>
		el siguiente equipo el cual fue entregado en Comodato para uso exclusivo del desempeño de sus actividades laborales asignadas, y consta de las siguientes características:
	</p>
	<p style="text-align: left; font-weight: bold;">1) TELÉFONO CELULAR</p>
	<table style="border:1px solid; border-collapse:collapse; width:100%;">
		<tbody>
			<tr style="height: 50px;">
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">Datos</td>
				<td style="border:1px solid; width:37%; font-weight: bold;">Descripción</td>
				<td style="border:1px solid; width:37%; font-weight: bold;">Observación</td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">MARCA</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->marca) ? ' --- ' : $comodato->marca; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">MODELO</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->modelo) ? ' --- ' : $comodato->modelo; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">IMEI</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->imei) ? ' --- ' : $comodato->imei; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">ACCESORIOS</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->accesorios) ? ' --- ' : $comodato->accesorios; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">PERSONA</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->persona_equipo) ? ' --- ' : $comodato->persona_equipo; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">ÁREA</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->area_equipo) ? ' --- ' : $comodato->area_equipo; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">ESTADO</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->estado_equipo) ? ' --- ' : $comodato->estado_equipo; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
		</tbody>
	</table>		
	<p style="text-align: left; font-weight: bold;">2) LÍNEA DE CELULAR</p>
	<table style="border:1px solid; border-collapse:collapse; width:100%;">
		<tbody>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">Datos</td>
				<td style="border:1px solid; width:37%; font-weight: bold;">Descripción</td>
				<td style="border:1px solid; width:37%; font-weight: bold;">Observación</td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">PRESTADOR</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->prestador) ? ' --- ' : $comodato->prestador; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">NÚMERO DE LÍNEA</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->numero) ? ' --- ' : $comodato->numero; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">NÚMERO SIM</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->sim) ? ' --- ' : $comodato->sim; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">MINUTOS INTERNAC.</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->min_internacional) ? ' --- ' : $comodato->min_internacional; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">MINUTOS NACIONALES</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->min_nacional) ? ' --- ' : $comodato->min_nacional; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">MINUTOS INTERNOS</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->min_interno) ? ' --- ' : $comodato->min_interno; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">PLAN DE DATOS</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->datos) ? ' --- ' : $comodato->datos; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">PERSONA</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->persona_linea) ? ' --- ' : $comodato->persona_linea; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
			<tr>
				<td style="border:1px solid; width:26%; font-weight: bold; height: 30px;">ÁREA</td>
				<td style="border:1px solid; width:37%;"><?php echo empty($comodato->area_linea) ? ' --- ' : $comodato->area_linea; ?></td>
				<td style="border:1px solid; width:37%;"></td>
			</tr>
		</tbody>
	</table>
	<?php echo (!empty($comodato->observaciones) ? '<b>Observaciones: </b>' . $comodato->observaciones : ''); ?>
	<br />
	<table style="border-collapse:collapse; width:100%;">
		<tbody>
			<tr>
				<td style="width:33%; font-weight: bold; text-align: center;">Entregó</td>
				<td style="width:33%; font-weight: bold; text-align: center;">Recibió</td>
				<td style="width:34%; font-weight: bold; text-align: center;">Autorizó</td>
			</tr>
			<tr>
				<td style="width:33%;  height: 60px;"></td>
				<td style="width:33%;  height: 60px;"></td>
				<td style="width:34%;  height: 60px;"></td>
			</tr>
			<tr>
				<td style="width:33%; font-size: 10px; text-align: center;">Firma/Aclaración/DNI</td>
				<td style="width:33%; font-size: 10px; text-align: center;">Firma/Aclaración/DNI</td>
				<td style="width:34%; font-size: 10px; text-align: center;">Firma/Aclaración/DNI</td>
			</tr>
		</tbody>
	</table>
</div>