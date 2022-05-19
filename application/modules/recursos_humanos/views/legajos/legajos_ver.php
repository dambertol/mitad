<!--
	/*
	 * Vista Ver Legajo.
	 * Autor: Leandro
	 * Creado: 21/02/2017
	 * Modificado: 09/10/2019 (Leandro)
	 */
-->
<?php if (!empty(json_decode($anios))): ?>
	<script>
		$(document).ready(function() {
			var treeData = <?php echo $anios; ?>;
			var $aniostree = $('#aniostree').treeview({
				levels: 1,
				nodeIcon: "glyphicon glyphicon-folder-open",
				onhoverColor: '#AAAAAA',
				showTags: true,
				enableLinks: true,
				highlightSelected: false,
				data: treeData
			});
			var search = function(e) {
				var pattern = $('#input-search').val();
				var results = $aniostree.treeview('search', [pattern]);
			}
			$('#btn-search').on('click', search);
			$('#btn-clear-search').on('click', function(e) {
				$aniostree.treeview('clearSearch');
				$aniostree.treeview('collapseAll');
				$('#input-search').val('');
			});
		});
	</script>
<?php endif; ?>
<script>
	var embargos_table;
	function complete_embargos_table() {
		$('#embargos_table tfoot th').each(function(i) {
			var clase = '';
			var tdclass = $('#embargos_table thead th').eq(i)[0]['attributes']['class']['value'];
			if (tdclass.indexOf("dt-body-right") >= 0) {
				clase = ' text-right';
			}
			var title = $('#embargos_table thead th').eq(i).text();
			var indice = $('#embargos_table thead th').eq(i).index();
			if (title !== '') {
				if (indice === 2) {	//Fecha
					$(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(embargos_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
				} else {
					$(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + embargos_table.column(i).search() + '"/>');
				}
			}
		});
		$('#embargos_table tfoot th').eq(9).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'embargos_table\');"><i class="fa fa-eraser"></i></button>');
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
		embargos_table.columns().every(function() {
			var column = this;
			if (this[0][0] === 2) { //Fecha
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
				$('input,select', embargos_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
					if (e.type === 'change' || e.which === 13) {
						if (column.search() !== this.value) {
							column.search(this.value).draw();
						}
						e.preventDefault();
					}
				});
			}
		});
		var r = $('#embargos_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#embargos_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Legajos'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="col-md-2 col-sm-2 col-xs-12 profile_left">
					<div class="profile_img">
						<div id="crop-avatar">
							<img class="img-responsive-avatar avatar-view" src="<?php echo (empty($empleado->labo_Codigo) || !file_exists('img/recursos_humanos/fotos/' . $empleado->labo_Codigo . '.jpg')) ? 'img/generales/user.png' : 'img/recursos_humanos/fotos/' . $empleado->labo_Codigo . '.jpg'; ?>" alt="Foto" title="Foto">
						</div>
					</div>
					<h4><?php echo "$legajo->nombre $legajo->apellido"; ?></h4>
					<ul class="list-unstyled user_data" style="font-size:11px;">
						<li>
							<span style="font-weight:bold;">DATOS LABORALES</span>
						</li>
						<li>
							<i class="fa fa-id-card-o"></i> <?php echo empty($empleado->labo_Codigo) ? 'Sin conexión a Major' : $empleado->labo_Codigo; ?>
						</li>
						<li>
							<i class="fa fa-building"></i> <?php echo empty($empleado->ofi_Descripcion) ? 'Sin conexión a Major' : $empleado->ofi_Descripcion; ?>
						</li>
						<li>
							<i class="fa fa-briefcase"></i> <?php echo empty($empleado->cate_Descripcion) ? 'Sin conexión a Major' : $empleado->cate_Descripcion; ?>
						</li>
						<li>
							<i class="fa fa-clock-o"></i> <?php echo empty($empleado->hora_Descripcion) ? 'Sin conexión a Major' : $empleado->hora_Descripcion; ?>
						</li>
						<li>
							<i class="fa fa-sign-in"></i> <?php echo empty($empleado->labo_FechaIngreso) ? 'Sin conexión a Major' : date_format(new DateTime($empleado->labo_FechaIngreso), 'd/m/Y'); ?>
						</li>
						<li>
							<br />
							<span style="font-weight:bold;">DATOS PERSONALES</span>
						</li>
						<li>
							<i class="fa fa-phone"></i> <?php echo empty($empleado->telefono) ? 'Sin conexión a Major' : $empleado->telefono; ?>
						</li>
						<li>
							<i class="fa fa-mobile"></i> <?php echo empty($empleado->celular) ? 'Sin conexión a Major' : $empleado->celular; ?>
						</li>
						<li>
							<i class="fa fa-tint"></i> <?php echo empty($empleado->grupo_sanguineo) ? 'Sin conexión a Major' : $empleado->grupo_sanguineo; ?>
						</li>
						<li>
							<i class="fa fa-birthday-cake"></i> <?php echo empty($empleado->pers_FechaNacimiento) ? 'Sin conexión a Major' : date_format(new DateTime($empleado->pers_FechaNacimiento), 'd/m/Y'); ?>
						</li>
					</ul>
				</div>
				<div class="col-md-10 col-sm-10 col-xs-12">
					<div class="form-horizontal" role="tabpanel" data-example-id="togglable-tabs">
						<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
							<li role="presentation" class="active">
								<a href="#tab_content_documentos" role="tab" id="documentos-tab" data-toggle="tab" aria-expanded="true">Documentos</a>
							</li>
							<li role="presentation">
								<a href="#tab_content_embargos" role="tab" id="embargos-tab" data-toggle="tab" aria-expanded="true">Embargos</a>
							</li>
							<li role="presentation">
								<a href="#tab_content_datos" role="tab" id="datos-tab" data-toggle="tab" aria-expanded="true">Datos Extra</a>
							</li>
						</ul>
						<div id="myTabContent" class="tab-content">
							<div role="tabpanel" class="tab-pane fade active in" id="tab_content_documentos" aria-labelledby="documentos-tab">
								<?php if ($edicion) : ?>
									<div class="row">
										<div class="col-md-12">
											<div class="text-center">
												<a href="recursos_humanos/documentos_legajo/agregar/<?php echo $legajo->id; ?>" class="btn btn-primary btn-sm">Agregar Documento</a>
											</div>
										</div>
									</div>
									<br />
								<?php endif; ?>
								<div class="row">
									<div class="col-md-12">
										<?php if (!empty(json_decode($anios))): ?>
											<div id="aniostree" class=""></div>
										<?php else: ?>
											<h3 class="text-center">Sin Documentos</h3>
										<?php endif; ?>
									</div>
								</div>
								<?php if (!empty(json_decode($anios))): ?>
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="input-search" class="sr-only">Buscar:</label>
												<input type="input" class="form-control" id="input-search" placeholder="Buscar documento..." value="">
											</div>
											<div class="text-center">
												<button type="button" class="btn btn-primary btn-sm" id="btn-search">Buscar</button>
												<button type="button" class="btn btn-default btn-sm" id="btn-clear-search">Limpiar</button>
											</div>
										</div>
									</div>
								<?php endif; ?>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tab_content_embargos" aria-labelledby="embargos-tab">
								<div class="row">
									<?php echo $js_table; ?>
									<?php echo $html_table; ?>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tab_content_datos" aria-labelledby="datos-tab">
								<?php if ($edicion) : ?>
									<div class="row">
										<div class="col-md-12">
											<div class="text-center">
												<a href="recursos_humanos/datos_extra/editar/<?php echo $legajo->id; ?>/<?php echo $datos_extra->id; ?>" class="btn btn-primary btn-sm">Editar Datos Extra</a>
											</div>
										</div>
									</div>
									<br />
								<?php endif; ?>
								<div class="row">
									<?php foreach ($fields_datos as $field): ?>
										<div class="form-group">
											<?php echo $field['label']; ?> 
											<?php echo $field['form']; ?>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="ln_solid"></div>
					<div class="text-center">
						<a href="recursos_humanos/legajos/listar" class="btn btn-default btn-sm">Cancelar</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>