<!--
	/*
	 * Vista listado de Tipos Resolución.
	 * Autor: Leandro
	 * Creado: 29/11/2017
	 * Modificado: 03/07/2019 (Leandro)
	 */
-->
<script>
	var tipos_resolucion_table;
	function complete_tipos_resolucion_table() {
		$('#tipos_resolucion_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#tipos_resolucion_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#tipos_resolucion_table thead th').eq(i).text();
			if (title !== '') {
				$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + tipos_resolucion_table.column(i).search() + '"/>');
			}
		});
		$('#tipos_resolucion_table tfoot th').eq(2).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'tipos_resolucion_table\');"><i class="fa fa-eraser"></i></button>');
		tipos_resolucion_table.columns().every(function() {
			var column = this;
			$('input,select', tipos_resolucion_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
				if (e.type === 'change' || e.which === 13) {
					if (column.search() !== this.value) {
						column.search(this.value).draw();
					}
					e.preventDefault();
				}
			});
		});
		var r = $('#tipos_resolucion_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#tipos_resolucion_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Tipos de Resolución'; ?></h2>
				<?php echo anchor('resoluciones/tipos_resoluciones/agregar', 'Crear Tipo de Resolución', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>