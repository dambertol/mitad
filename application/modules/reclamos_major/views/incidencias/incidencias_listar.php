<!--
	/*
	 * Vista listado de Incidencias.
	 * Autor: Leandro
	 * Creado: 17/12/2019
	 * Modificado: 17/12/2019 (Leandro)
	 */
-->
<script>
	var incidencias_table;
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	function complete_incidencias_table() {
		$('#incidencias_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#incidencias_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#incidencias_table thead th').eq(i).text();
			var indice = $('#incidencias_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 1 || indice === 9) { // Inicio || Finalización
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(incidencias_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else if (indice === 8) { // Estado
					$(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_estados)); ?>);
					$(this).find('select').val(incidencias_table.column(i).search());
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + incidencias_table.column(i).search() + '"/>');
				}
			}
		});
		$('#incidencias_table tfoot th').eq(10).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'incidencias_table\');" title="Limpiar filtros"><i class="fa fa-eraser"></i></button>');
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
		incidencias_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 1 || this[0][0] === 9) { // Inicio || Finalización
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
			} else if (this[0][0] === 8) { // Estado
				$('input,select', incidencias_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
					if (column.search() !== this.value) {
						column.search(this.value, 'exact').draw();
					}
				});
			} else {
				$('input,select', incidencias_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#incidencias_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#incidencias_table thead').append(r);
	}
	function finalizar_incidencia(incidencia_id) {
		Swal.fire({
			title: 'Confirmar',
			text: "Se finalizará la incidencia",
			type: 'info',
			showCloseButton: true,
			showCancelButton: true,
			focusCancel: true,
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			cancelButtonClass: 'btn btn-default',
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar'
		}).then((result) => {
			if (result.value) {
				window.location.href = CI.base_url + 'incidencias/incidencias/finalizar/' + incidencia_id;
			}
		});
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Incidencias'; ?></h2>
				<?php echo anchor("reclamos_major/incidencias/$add_url", 'Crear Incidencia', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>