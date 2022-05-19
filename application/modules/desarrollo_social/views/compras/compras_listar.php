<!--
	/*
	 * Vista listado de Compras.
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 22/10/2019 (Leandro)
	 */
-->
<script>
	var compras_table;
	function complete_compras_table() {
		$('#compras_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#compras_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#compras_table thead th').eq(i).text();
			var indice = $('#compras_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 1) { // Fecha Recepción
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(compras_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else if (indice === 7) { // Estado
					$(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_estados)); ?>);
					$(this).find('select').val(compras_table.column(i).search());
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + compras_table.column(i).search() + '"/>');
				}
			}
		});
		$('#compras_table tfoot th').eq(8).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'compras_table\');"><i class="fa fa-eraser"></i></button>');
		$('.dateFilter').each(function(index, element) {
			$(element).datetimepicker({
				locale: 'es',
				format: 'L',
				useCurrent: false,
				showClear: true,
				showTodayButton: true,
				showClose: true
			});
		});
		compras_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 1) { // Fecha Recepción
				$("#dateFilter" + this[0][0]).on("dp.change", function(e) {
					if (e.date) {
						var sql_date = moment(e.date._d).format('YYYY-MM-DD');
					} else {
						var sql_date = '';
					}
					if (column.search() !== sql_date) {
						column.search(sql_date).draw();
					}
				});
			} else if (this[0][0] === 7) { // Estado
				$('input,select', compras_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
					if (column.search() !== this.value) {
						column.search(this.value, 'exact').draw();
					}
				});
			} else {
				$('input,select', compras_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#compras_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#compras_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Compras'; ?></h2>
				<?php echo anchor('desarrollo_social/compras/agregar', 'Crear Compra', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>