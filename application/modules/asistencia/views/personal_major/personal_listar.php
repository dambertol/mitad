<!--
   /*
	* Vista Listado de Personal
	* Autor: Leandro
	* Creado: 26/09/2016
	* Modificado: 07/10/2019 (Leandro)
	*/
-->
<script>
	var personal_table;
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	function complete_personal_table() {
		$('#personal_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#personal_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#personal_table thead th').eq(i).text();
			var indice = $('#personal_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 5) { //Inicio Secuencia
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(personal_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + personal_table.column(i).search() + '"/>');
				}
			}
		});
		$('#personal_table tfoot th').eq(6).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'personal_table\');"><i class="fa fa-eraser"></i></button>');
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
		personal_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 5) {
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
				$('input,select', personal_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#personal_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#personal_table thead').append(r);
	}
	$(document).ready(function() {
		$('#secretaria').on('changed.bs.select', function(e) {
			actualizar_oficinas();
		});
		$("#oficina").on("keyup change", function() {
			var secretaria = $("#secretaria option:selected").val();
			var oficina = $("#oficina option:selected").val();
			window.location.replace(CI.base_url + "asistencia/personal_major/listar/" + secretaria + "/" + oficina);
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
							<?php echo form_label('Secretaría *', 'secretaria', array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-10">
								<?php echo form_dropdown('secretaria', $secretaria_opt, $secretaria_id, 'class="form-control selectpicker" id="secretaria" title="-- Seleccionar --" data-live-search="true"'); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo form_label('Oficina *', 'oficina', array('class' => 'col-sm-2 control-label')); ?>
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
