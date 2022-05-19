<!--
	/*
	 * Vista listado de Escribanos.
	 * Autor: Leandro
	 * Creado: 04/06/2018
	 * Modificado: 09/10/2018 (Leandro)
	 */
-->
<script>
	var escribanos_table;
	function complete_escribanos_table() {
		agregar_filtros('escribanos_table', escribanos_table, 6);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Escribanos'; ?></h2>
				<?php if ($agregar) : ?>
					<?php echo anchor('transferencias/escribanos/agregar', 'Crear Escribano', 'class="btn btn-primary btn-sm"') ?>
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