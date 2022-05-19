<!--
	/*
	 * Vista listado de Documentos.
	 * Autor: Leandro
	 * Creado: 20/02/2017
	 * Modificado: 07/10/2019 (Leandro)
	 */
-->
<script>
	var documentos_legajo_table;
	function complete_documentos_legajo_table() {
		$('#documentos_legajo_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#documentos_legajo_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#documentos_legajo_table thead th').eq(i).text();
			var indice = $('#documentos_legajo_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 1 || indice === 5) {	//Presentacion y Fecha Carga
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(documentos_legajo_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else if (indice === 2) { // Categoria
					$(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_categorias)); ?>);
					$(this).find('select').val(documentos_legajo_table.column(i).search());
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + documentos_legajo_table.column(i).search() + '"/>');
				}
			}
		});
		$('#documentos_legajo_table tfoot th').eq(6).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'documentos_legajo_table\');"><i class="fa fa-eraser"></i></button>');
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
		documentos_legajo_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 2) { // Categoria
				$('input,select', documentos_legajo_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
					if (column.search() !== this.value) {
						column.search(this.value, 'exact').draw();
					}
				});
			} else if (this[0][0] === 1 || this[0][0] === 5) { //Presentacion y Fecha Carga
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
				$('input,select', documentos_legajo_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#documentos_legajo_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#documentos_legajo_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Documentos'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="row tile_count">
					<div class="col-md-3 col-sm-3 col-xs-6 tile_stats_count">
						<span class="count_top"><i class="fa fa-address-card-o"></i> Legajos con documentos</span>
						<div class="count"><?php echo $indicadores['legajos']['total']; ?></div>
						<span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i><?php echo $indicadores['legajos']['variacion']; ?> </i></span>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-6 tile_stats_count">
						<span class="count_top"><i class="fa fa-folder-open-o"></i> Categorías con documentos</span>
						<div class="count"><?php echo $indicadores['categorias']['total']; ?></div>
						<span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i><?php echo $indicadores['categorias']['variacion']; ?> </i></span>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-6 tile_stats_count">
						<span class="count_top"><i class="fa fa-file-o"></i> Documentos cargados</span>
						<div class="count"><?php echo $indicadores['documentos']['total']; ?></div>
						<span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i><?php echo $indicadores['documentos']['variacion']; ?> </i></span>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-6 tile_stats_count">
						<span class="count_top"><i class="fa fa-hdd-o"></i> Espacio ocupado</span>
						<div class="count"><?php echo $indicadores['tamanios']['total']; ?></div>
						<span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i><?php echo $indicadores['tamanios']['variacion']; ?> </i></span>
					</div>
				</div>
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>