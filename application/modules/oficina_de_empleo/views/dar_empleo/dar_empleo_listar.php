<!--
 /**
  * Vista listado de dar_empleo .
  * Autor: Leandro
  * Creado: 10/10/2018
  * Modificado: 07/10/2019 (Leandro)
  */
-->
<script>
	var dar_empleo_table; 
	function completedar_empleo table() {
		$('#dar_empleo_table tfoot th').each(function(i) { 
			var clase = '';
			var tdclass = $('#dar_empleo_table thead th').eq(i)[0]['attributes']['class']['value']; 
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#dar_empleo_table thead th').eq(i).text(); 
			var indice = $('#dar_empleo_table thead th').eq(i).index(); 
			if (title !== '') {
				if (indice === 6) { // Fecha
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(dar_empleo _table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + dar_empleo_table.column(i).search() + '"/>'); 
				}
			}
		});
		$('#dar_empleo_table tfoot th').eq(7).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'dar_empleo_table\');"><i class="fa fa-eraser"></i></button>');  
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
		dar_empleo_table.columns().every(function() { 
			var column = this;
			if (this[0][0] === 6) { // Fecha
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
				$('input,select', dar_empleo_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) { 
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#dar_empleo_table tfoot tr'); 
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#dar_empleo_table thead').append(r); 
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'dar_empleo'; ?></h2> 
				<?php echo anchor('oficina_de_empleo/dar_empleo/agregar', 'Crear Reclamo', 'class="btn btn-primary btn-sm"') ?>  
				<?php echo anchor('oficina_de_empleo/dar_empleo/exportar', 'Exportar Datos', 'class="btn btn-primary btn-sm"') ?>  
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>