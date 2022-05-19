<!--
	/*
	 * Vista listado de Artículos.
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 01/10/2019 (Leandro)
	 */
-->
<script>
	var articulos_table;
	function complete_articulos_table() {
		$('#articulos_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#articulos_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#articulos_table thead th').eq(i).text();
			var indice = $('#articulos_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 5 || indice === 6) { // Cant. Real || Cant. Mínima
					$(this).html('<input class="form-control input-xs numberFilter' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + articulos_table.column(i).search() + '"/>');
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + articulos_table.column(i).search() + '"/>');
				}
			}
		});
		$('#articulos_table tfoot th').eq(9).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'articulos_table\');"><i class="fa fa-eraser"></i></button>');
		$('.numberFilter').each(function(index, element) {
			$(element).inputmask('decimal', {
				radixPoint: ',',
				unmaskAsNumber: true,
				digits: 2,
				autoUnmask: true,
				placeholder: '',
				removeMaskOnSubmit: true,
				positionCaretOnClick: 'select'
			});
		});
		articulos_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 3 || this[0][0] === 4) { // Cant. Real || Cant. Mínima
				$('input,select', articulos_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
					if (column.search() !== this.value) {
						var str_numero = this.value.toString();
						column.search(str_numero).draw();
					}
				});
			} else {
				$('input,select', articulos_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#articulos_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#articulos_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Artículos'; ?></h2>
				<?php echo anchor('desarrollo_social/articulos/agregar', 'Crear Artículo', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>