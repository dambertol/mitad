<!--
	/*
	 * Vista listado de Tipos de Documentos.
	 * Autor: Leandro
	 * Creado: 08/01/2020
	 * Modificado: 08/01/2020 (Leandro)
	 */
-->
<script>
	var tipos_documentos_table;
	function complete_tipos_documentos_table() {
		agregar_filtros('tipos_documentos_table', tipos_documentos_table, 4);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Tipos de Documentos'; ?></h2>
				<?php echo anchor('gobierno/tipos_documentos/agregar', 'Crear Tipo de Documento', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>