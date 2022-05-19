<!--
	/*
	 * Vista listado de Reportes
	 * Autor: Leandro
	 * Creado: 04/09/2019
	 * Modificado: 05/09/2019 (Leandro)
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
				<h2 class="text-center">Informes Excel</h2>
				<ul class="to_do">
					<li><a href="telefonia/reportes/consumo_lineas_fijas" title="Informe de Consumo de Líneas Fijas" class="exportar_excel"><p>Informe de Consumo de Líneas Fijas</p></a></li>
					<li><a href="telefonia/reportes/equipos" title="Informe de Equipos" class="exportar_excel"><p>Informe de Equipos</p></a></li>
					<li><a href="telefonia/reportes/lineas" title="Informe de Líneas" class="exportar_excel"><p>Informe de Líneas</p></a></li>
					<li><a href="telefonia/reportes/lineas_listado" title="Listado de Líneas" class="exportar_excel"><p>Listado de Líneas</p></a></li>
				</ul>
			</div>
		</div>
	</div>
</div>