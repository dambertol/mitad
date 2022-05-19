<!--
	/*
	 * Vista listado de Legajos.
	 * Autor: Leandro
	 * Creado: 02/02/2017
	 * Modificado: 09/10/2019 (Leandro)
	 */
-->
<script>
	var legajos_table;
	function complete_legajos_table() {
		agregar_filtros('legajos_table', legajos_table, 4);
	}
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Legajos'; ?></h2>
				<?php if ($edicion) : ?>
					<?php echo anchor('recursos_humanos/legajos/agregar', 'Crear Legajo', 'class="btn btn-primary btn-sm"') ?>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>