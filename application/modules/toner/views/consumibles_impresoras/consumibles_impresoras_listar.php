<!--
	/*
	 * Vista listado de Consumibles Impresora.
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 07/05/2019 (Leandro)
	 */
-->
<script>
	var consumibles_impresoras_table;
	function complete_consumibles_impresoras_table() {
		agregar_filtros('consumibles_impresoras_table', consumibles_impresoras_table, 3);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Consumibles Impresora'; ?></h2>
				<?php echo anchor('toner/consumibles_impresoras/agregar', 'Crear Consumible Impresora', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>