<!--
	/*
	 * Vista listado de Tipos de Parentezco.
	 * Autor: Leandro
	 * Creado: 09/09/2019
	 * Modificado: 09/09/2019 (Leandro)
	 */
-->
<script>
	var tipos_parentezcos_table;
	function complete_tipos_parentezcos_table() {
		agregar_filtros('tipos_parentezcos_table', tipos_parentezcos_table, 2);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Tipos de Parentezco'; ?></h2>
				<?php echo anchor('ninez_adolescencia/tipos_parentezcos/agregar', 'Crear Tipo de Parentezco', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>