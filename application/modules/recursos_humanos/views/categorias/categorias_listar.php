<!--
	/*
	 * Vista listado de Categorías.
	 * Autor: Leandro
	 * Creado: 02/02/2017
	 * Modificado: 03/07/2019 (Leandro)
	 */
-->
<script>
	var categorias_table;
	function complete_categorias_table() {
		$('#categorias_table tfoot th').each(function(i) {
			var title = $('#categorias_table thead th').eq(i).text();
			if (title !== '') {
				$(this).html('<input class="form-control input-xs" style="width: 100%;" type="text" placeholder="' + title + '" value="' + categorias_table.column(i).search() + '"/>');
			}
		});
		$('#categorias_table tfoot th').eq(1).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'categorias_table\');"><i class="fa fa-eraser"></i></button>');
		categorias_table.columns().every(function() {
			var column = this;
			$('input,select', categorias_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
				if (e.type === 'change' || e.which === 13) {
					if (column.search() !== this.value) {
						column.search(this.value).draw();
					}
					e.preventDefault();
				}
			});
		});
		var r = $('#categorias_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#categorias_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Categorías'; ?></h2>
				<?php echo anchor('recursos_humanos/categorias/agregar', 'Crear Categoría', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>