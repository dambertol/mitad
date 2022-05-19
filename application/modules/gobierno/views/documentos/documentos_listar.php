<!--
	/*
	 * Vista listado de Documentos.
	 * Autor: Leandro
	 * Creado: 08/01/2020
	 * Modificado: 23/01/2020 (Leandro)
	 */
-->
<script>
	var documentos_table;
	function complete_documentos_table() {
		$('#documentos_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#documentos_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#documentos_table thead th').eq(i).text();
			var indice = $('#documentos_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 0) { // Tipo
					$(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_tipos)); ?>);
					$(this).find('select').val(documentos_table.column(i).search());
				} else if (indice === 4) { // Fecha Inicio
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(documentos_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + documentos_table.column(i).search() + '"/>');
				}
			}
		});
		$('#documentos_table tfoot th').eq(9).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'documentos_table\');"><i class="fa fa-eraser"></i></button>');
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
		documentos_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 4) { // Fecha Inicio
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
			} else if (this[0][0] === 0 || this[0][0] === 9) { // Tipo y Estado
				$('input,select', documentos_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
					if (column.search() !== this.value) {
						column.search(this.value, 'exact').draw();
					}
				});
			} else {
				$('input,select', documentos_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#documentos_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#documentos_table thead').append(r);
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
				<?php echo anchor('gobierno/documentos/agregar', 'Crear Documento', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>