<!--
 /**
  * Vista listado de Reclamos Potrerillos.
  * Autor: Leandro
  * Creado: 07/10/2019
  * Modificado: 03/07/2019 (Leandro)
  */
-->
<script>
	var reclamos_table;
	function complete_reclamos_table() {
		$('#reclamos_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#reclamos_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#reclamos_table thead th').eq(i).text();
			var indice = $('#reclamos_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 7) { // Fecha
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(reclamos_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + reclamos_table.column(i).search() + '"/>');
				}
			}
		});
		$('#reclamos_table tfoot th').eq(8).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'reclamos_table\');"><i class="fa fa-eraser"></i></button>');
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
		reclamos_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 7) { // Fecha
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
			} else {
				$('input,select', reclamos_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#reclamos_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#reclamos_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Reclamos Potrerillos'; ?></h2>
				<?php echo anchor('reclamos_gis/reclamos_potrerillos/agregar', 'Crear Reclamo', 'class="btn btn-primary btn-sm"') ?>
				<?php echo anchor('reclamos_gis/reclamos_potrerillos/exportar', 'Exportar Datos', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>