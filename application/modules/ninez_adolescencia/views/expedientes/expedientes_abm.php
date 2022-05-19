<!--
	/*
	 * Vista ABM de Expediente.
	 * Autor: Leandro
	 * Creado: 12/09/2019
	 * Modificado: 07/10/2019 (Leandro)
	 */
-->
<script>
	var adultos_responsables_table;
	var menores_table;
	var intervenciones_table;
	var adjuntos_table;
	function complete_adultos_responsables_table() {
		$('#adultos_responsables_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#adultos_responsables_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#adultos_responsables_table thead th').eq(i).text();
			var indice = $('#adultos_responsables_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 1) { // Hasta
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(adultos_responsables_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + adultos_responsables_table.column(i).search() + '"/>');
				}
			}
		});
		$('#adultos_responsables_table tfoot th').eq(2).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'adultos_responsables_table\');"><i class="fa fa-eraser"></i></button>');
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
		adultos_responsables_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 1) { // Hasta
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
				$('input,select', adultos_responsables_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#adultos_responsables_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#adultos_responsables_table thead').append(r);
	}
	function complete_menores_table() {
		agregar_filtros('menores_table', menores_table, 1);
	}
	function complete_intervenciones_table() {
		$('#intervenciones_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#intervenciones_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#intervenciones_table thead th').eq(i).text();
			var indice = $('#intervenciones_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 0) { // Fecha
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(intervenciones_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + intervenciones_table.column(i).search() + '"/>');
				}
			}
		});
		$('#intervenciones_table tfoot th').eq(2).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'intervenciones_table\');"><i class="fa fa-eraser"></i></button>');
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
		intervenciones_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 0) { // Fecha
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
				$('input,select', intervenciones_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#intervenciones_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#intervenciones_table thead').append(r);
	}
	function complete_adjuntos_table() {
		$('#adjuntos_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#adjuntos_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#adjuntos_table thead th').eq(i).text();
			var indice = $('#adjuntos_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 0) { // Fecha
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(adjuntos_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + adjuntos_table.column(i).search() + '"/>');
				}
			}
		});
		$('#adjuntos_table tfoot th').eq(3).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'adjuntos_table\');"><i class="fa fa-eraser"></i></button>');
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
		adjuntos_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 0) { // Fecha
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
				$('input,select', adjuntos_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#adjuntos_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#adjuntos_table thead').append(r);
	}
	function openWindow(percent) {
		var w = 1280, h = 720, l = 0, t = 0; // default sizes
		if (window.screen) {
			w = window.screen.availWidth * percent / 100;
			h = window.screen.availHeight * percent / 100;
			l = (window.screen.availWidth - w) / 2;
			t = (window.screen.availHeight - h) / 2;
		}

		window.open('ninez_adolescencia/expedientes/visualizar/<?php echo $expedient->id; ?>', 'popup', 'height=' + h + ',width=' + w + ',left=' + l + ',top=' + t + ',scrollbars=no,resizable=no,menubar=no,status=no,titlebar=no,toolbar=no')
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Expedientes'; ?></h2>
				<?php if (empty($txt_btn)): ?>
					<a href="ninez_adolescencia/expedientes/visualizar/<?php echo $expedient->id; ?>" 
						 class="btn btn-primary btn-sm"
						 target="popup" 
						 onclick="openWindow(90); return false;">
						Visualizar
					</a>
				<?php endif; ?>
				<?php if (!empty($audi_modal)): ?>
					<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
						<i class="fa fa-info-circle"></i>
					</button>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>
				<div class="row">
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if (empty($txt_btn)): ?>
					<div class="row">
						<div class="form-horizontal">
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active"><a href="#tab_adultos" aria-controls="tab_adultos" role="tab" data-toggle="tab"><i class="fa fa-user"></i> Adultos Responsables</a></li>
								<li role="presentation"><a href="#tab_menores" aria-controls="tab_menores" role="tab" data-toggle="tab"><i class="fa fa-child"></i> Menores</a></li>
								<li role="presentation"><a href="#tab_intervenciones" aria-controls="tab_intervenciones" role="tab" data-toggle="tab"><i class="fa fa-list-ul"></i> Intervenciones</a></li>
								<li role="presentation"><a href="#tab_adjuntos" aria-controls="tab_adjuntos" role="tab" data-toggle="tab"><i class="fa fa-files-o"></i> Adjuntos</a></li>
							</ul>
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="tab_adultos">
									<div class="x_panel">
										<div class="x_title">
											<h2>Adultos Responsables</h2>
											<?php echo anchor("ninez_adolescencia/adultos_responsables/agregar/$expedient->id", 'Agregar Adulto Responsable', 'class="btn btn-primary btn-sm"') ?>
											<div class="clearfix"></div>
										</div>
										<div class="x_content">
											<?php echo $js_table_adultos; ?>
											<?php echo $html_table_adultos; ?>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="tab_menores">
									<div class="x_panel">
										<div class="x_title">
											<h2>Menores</h2>
											<?php echo anchor("ninez_adolescencia/menores/agregar/$expedient->id", 'Agregar Menor', 'class="btn btn-primary btn-sm"') ?>
											<div class="clearfix"></div>
										</div>
										<div class="x_content">
											<?php echo $js_table_menores; ?>
											<?php echo $html_table_menores; ?>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="tab_intervenciones">
									<div class="x_panel">
										<div class="x_title">
											<h2>Intervenciones</h2>
											<?php echo anchor("ninez_adolescencia/intervenciones/agregar/$expedient->id", 'Agregar Intervención', 'class="btn btn-primary btn-sm"') ?>
											<div class="clearfix"></div>
										</div>
										<div class="x_content">
											<?php echo $js_table_intervenciones; ?>
											<?php echo $html_table_intervenciones; ?>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="tab_adjuntos">
									<div class="x_panel">
										<div class="x_title">
											<h2>Adjuntos</h2>
											<?php echo anchor("ninez_adolescencia/adjuntos/agregar/$expedient->id", 'Agregar Adjunto', 'class="btn btn-primary btn-sm"') ?>
											<div class="clearfix"></div>
										</div>
										<div class="x_content">
											<?php echo $js_table_adjuntos; ?>
											<?php echo $html_table_adjuntos; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $expedient->id) : ''; ?>
					<a href="ninez_adolescencia/expedientes/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>