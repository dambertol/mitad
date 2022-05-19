<!--
	/*
	 * Vista listado de Líneas.
	 * Autor: Leandro
	 * Creado: 03/09/2019
	 * Modificado: 03/09/2019 (Leandro)
	 */
-->
<script>
	var lineas_table;
	function complete_lineas_table() {
		$('#lineas_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#lineas_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#lineas_table thead th').eq(i).text();
			var indice = $('#lineas_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 12) { // Estado
					$(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_estados)); ?>);
					$(this).find('select').val(lineas_table.column(i).search());
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + lineas_table.column(i).search() + '"/>');
				}
			}
		});
		$('#lineas_table tfoot th').eq(13).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'lineas_table\');" title="Limpiar filtros"><i class="fa fa-eraser"></i></button>');
		lineas_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 8) { // Estado
				$('input,select', lineas_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
					if (column.search() !== this.value) {
						column.search(this.value, 'exact').draw();
					}
				});
			} else {
				$('input,select', lineas_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#lineas_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#lineas_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Líneas'; ?></h2>
				<?php echo anchor('telefonia/lineas/agregar', 'Crear Línea', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>