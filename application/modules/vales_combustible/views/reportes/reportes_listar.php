<!--
	/*
	 * Vista listado de Reportes
	 * Autor: Leandro
	 * Creado: 15/11/2017
	 * Modificado: 28/03/2020 (Leandro)
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Reportes'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php if (!empty($contaduria)) : ?>
					<h2 class="text-center">Informes Excel</h2>
					<ul class="to_do">
						<li><a href="vales_combustible/reportes/facturas" title="Informe de Facturas" class="exportar_excel"><p>Informe de Facturas</p></a></li>
						<li><a href="vales_combustible/reportes/areas" title="Informe de Consumo por Área" class="exportar_excel"><p>Informe de Consumo por Área</p></a></li>
						<li><a href="vales_combustible/reportes/ordenes_compra" title="Informe de Órdenes de Compra" class="exportar_excel"><p>Informe de Órdenes de Compra</p></a></li>
						<li><a href="vales_combustible/reportes/ordenes_compra_detalle" title="Informe de Órdenes de Compra Detallado" class="exportar_excel"><p>Informe de Órdenes de Compra Detallado</p></a></li>
						<li><a href="vales_combustible/reportes/tipos_combustible" title="Informe de Consumo por Tipo de Combustible" class="exportar_excel"><p>Informe de Consumo por Tipo de Combustible</p></a></li>
						<li><a href="vales_combustible/reportes/emitidos" title="Informe de Vales emitidos" class="exportar_excel"><p>Informe de Vales emitidos</p></a></li>
						<li><a href="vales_combustible/reportes/fuera_termino" title="Informe de Vales cargados fuera de término" class="exportar_excel"><p>Informe de Vales cargados fuera de término</p></a></li>
						<li><a href="vales_combustible/reportes/sin_uso" title="Informe de Vales sin uso" class="exportar_excel"><p>Informe de Vales sin uso</p></a></li>
						<li><a href="vales_combustible/reportes/vencidos" title="Informe de Vales vencidos" class="exportar_excel"><p>Informe de Vales vencidos</p></a></li>
						<li><a href="vales_combustible/reportes/vehiculos" title="Informe de Vehículos" class="exportar_excel"><p>Informe de Vehículos</p></a></li>
					</ul>
				<?php endif; ?>
				<h2 class="text-center">Informes Sistema</h2>
				<ul class="to_do">
					<li><a href="vales_combustible/reportes/resumen_vales" title="Resumen de Vales" class="exportar_excel"><p>Resumen de Vales</p></a></li>
				</ul>
			</div>
		</div>
	</div>
</div>