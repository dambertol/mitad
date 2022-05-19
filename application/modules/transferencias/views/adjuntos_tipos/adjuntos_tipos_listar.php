<!--
	/*
	 * Vista listado de Tipos de Adjuntos.
	 * Autor: Leandro
	 * Creado: 19/06/2018
	 * Modificado: 19/06/2018 (Leandro)
	 */
-->
<script>
	var adjuntos_tipos_table;
	function complete_adjuntos_tipos_table() {
		agregar_filtros('adjuntos_tipos_table', adjuntos_tipos_table, 2);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Tipos de Adjuntos'; ?></h2>
				<?php echo anchor('transferencias/adjuntos_tipos/agregar', 'Crear Tipo de Adjunto', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>