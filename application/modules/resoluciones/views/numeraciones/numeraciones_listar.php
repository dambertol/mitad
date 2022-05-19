<!--
	/*
	 * Vista listado de Numeraciones.
	 * Autor: Leandro
	 * Creado: 06/12/2017
	 * Modificado: 03/07/2019 (Leandro)
	 */
-->
<script>
	var numeraciones_table;
	function complete_numeraciones_table() {
		$('#numeraciones_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#numeraciones_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#numeraciones_table thead th').eq(i).text();
			var indice = $('#numeraciones_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 0) { // Tipo
					$(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_tipos)); ?>);
					$(this).find('select').val(numeraciones_table.column(i).search());
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + numeraciones_table.column(i).search() + '"/>');
				}
			}
		});
		$('#numeraciones_table tfoot th').eq(3).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'numeraciones_table\');"><i class="fa fa-eraser"></i></button>');
		numeraciones_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 0) { // Tipo
				$('input,select', numeraciones_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
					if (column.search() !== this.value) {
						column.search(this.value, 'exact').draw();
					}
				});
			} else {
				$('input,select', numeraciones_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#numeraciones_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#numeraciones_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Numeraciones'; ?></h2>
				<?php echo anchor('resoluciones/numeraciones/agregar', 'Crear Numeración', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>