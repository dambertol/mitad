<!--
	/*
	 * Vista listado de Hobbies.
	 * Autor: Leandro
	 * Creado: 12/08/2019
	 * Modificado: 12/08/2019 (Leandro)
	 */
-->
<script>
	var hobbies_table;
	function complete_hobbies_table() {
		agregar_filtros('hobbies_table', hobbies_table, 1);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Hobbies'; ?></h2>
				<?php echo anchor('recursos_humanos/hobbies/agregar', 'Crear Hobby', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>