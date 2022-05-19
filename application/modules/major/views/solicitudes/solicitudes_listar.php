<!--
   /*
	* Vista Listado de Solicitudes
	* Autor: Leandro
	* Creado: 13/08/2019
	* Modificado: 07/10/2019 (Leandro)
	*/
-->
<script>
	var solicitudes_table;
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	function complete_solicitudes_table() {
		$('#solicitudes_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#solicitudes_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#solicitudes_table thead th').eq(i).text();
			var indice = $('#solicitudes_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 0) { //Fecha
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(solicitudes_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + solicitudes_table.column(i).search() + '"/>');
				}
			}
		});
		$('#solicitudes_table tfoot th').eq(10).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'solicitudes_table\');"><i class="fa fa-eraser"></i></button>');
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
		solicitudes_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 0) {
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
				$('input,select', solicitudes_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#solicitudes_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#solicitudes_table thead').append(r);
	}
	$(document).ready(function() {
		$('#ejercicio').on('changed.bs.select', function(e) {
			var ejercicio = $("#ejercicio option:selected").val();
			var oficina = $("#oficina option:selected").val();
			window.location.replace(CI.base_url + "major/solicitudes/listar/" + ejercicio + "/" + oficina);
		});
		$("#oficina").on("keyup change", function() {
			var ejercicio = $("#ejercicio option:selected").val();
			var oficina = $("#oficina option:selected").val();
			window.location.replace(CI.base_url + "major/solicitudes/listar/" + ejercicio + "/" + oficina);
		});
	});
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Personal'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="form-horizontal">
					<div class="row">
						<div class="form-group">
							<?php echo form_label('Ejercicio', 'ejercicio', array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-10">
								<?php echo form_dropdown('ejercicio', $ejercicio_opt, $ejercicio_id, 'class="form-control selectpicker" id="ejercicio" title="-- Seleccionar --" data-live-search="true"'); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo form_label('Oficina', 'oficina', array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-10">
								<?php echo form_dropdown('oficina', $oficina_opt, $oficina_id, 'class="form-control selectpicker" id="oficina" title="-- Seleccionar --" data-live-search="true"'); ?>
							</div>
						</div>
					</div>
				</div>
				<br />
				<div class="col-lg-12">
					<?php echo (!empty($js_table) ? $js_table : ''); ?>
					<?php echo (!empty($html_table) ? $html_table : ''); ?>
				</div>
			</div>
		</div>
	</div>
</div>
