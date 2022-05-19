<!--
	/*
	 * Vista listado de Stock.
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 04/03/2020 (Leandro)
	 */
-->
<script>
	var stock_table;
	function complete_stock_table() {
		agregar_filtros('stock_table', stock_table, 8);
	}
	$(document).ready(function() {
		$("#area").on("keyup change", function() {
			var area = $("#area option:selected").val();
			window.location.replace(CI.base_url + "stock_informatica/stock/listar/" + area);
		});
	});
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Stock'; ?></h2>
				<?php echo anchor('stock_informatica/stock/ingreso', 'Ingreso a Stock', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="form-horizontal">
					<div class="row">
						<div class="form-group">
							<?php echo form_label('Area *', 'area', array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-10">
								<?php echo form_dropdown('area', $area_opt, $area_id, 'class="form-control selectpicker" id="area" title="-- Seleccionar --" data-live-search="true"'); ?>
							</div>
						</div>
					</div>
				</div>
				<br />
				<div class="col-lg-12">
					<?php echo $js_table; ?>
					<?php echo $html_table; ?>
				</div>
			</div>
		</div>
	</div>
</div>