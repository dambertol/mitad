<!--
   /*
	* Vista Escritorio
	* Autor: Leandro
	* Creado: 04/12/2019
	* Modificado: 04/12/2019 (Leandro)	
	*/
-->
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Escritorio'; ?><small>Versión Beta1</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="row">
					<?php if (!empty($accesos_esc)) : ?>
						<?php foreach ($accesos_esc as $Acceso) : ?>
							<div class="animated flipInY col-lg-3 col-md-6 col-sm-6 col-xs-12">
								<div class="tile-stats" onclick="location.href = CI.base_url + '<?php echo $Acceso['href']; ?>'">
									<div class="icon fa <?php echo $Acceso['icon']; ?>"></div>
									<h3><?php echo $Acceso['title']; ?></h3>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="x_panel">
			<div id="bg-loader-ajax-tablero" class="bg-loader-ajax">
				<div id="loader-ajax-tablero" class="loader-ajax"><img alt="LC" src="img/generales/logo_lujan_001.png"/></div>
			</div>
			<div class="x_title">
				<h2>Turnero <span id="turnero-fecha">(-)</span></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-search"></i></a>
					</li>
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="row tile_count" style="margin-bottom:0px; margin-top:0px;">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<h4>Parque Cívico</h4>
						<div class="col-md-3 col-sm-3 col-xs-6 tile_stats_count" style="border-bottom:none;">
							<span class="count_top"><i class="fa fa-clock-o"></i> Media Atención</span>
							<div class="count green" id="turnero-3-tma">-</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-6 tile_stats_count" style="border-bottom:none;">
							<span class="count_top"><i class="fa fa-clock-o"></i> Media Espera</span>
							<div class="count green" id="turnero-3-tme">-</div>
						</div>
						<div class="col-md-2 col-sm-2 col-xs-4 tile_stats_count" style="border-bottom:none;">
							<span class="count_top"><i class="fa fa-user"></i> Atendidas</span>
							<div class="count green" id="turnero-3-ate">-</div>
						</div>
						<div class="col-md-2 col-sm-2 col-xs-4 tile_stats_count" style="border-bottom:none;">
							<span class="count_top"><i class="fa fa-user"></i> En Proceso</span>
							<div class="count yellow" id="turnero-3-pro">-</div>
						</div>
						<div class="col-md-2 col-sm-2 col-xs-4 tile_stats_count" style="border-bottom:none;">
							<span class="count_top"><i class="fa fa-user"></i> En Espera</span>
							<div class="count red" id="turnero-3-esp">-</div>
						</div>
					</div>
				</div>
				<div class="row tile_count" style="margin-bottom:0px; margin-top:0px;">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<h4>Edificio XX Setiembre</h4>
						<div class="col-md-3 col-sm-3 col-xs-6 tile_stats_count" style="border-bottom:none;">
							<span class="count_top"><i class="fa fa-clock-o"></i> Media Atención</span>
							<div class="count green" id="turnero-1-tma">-</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-6 tile_stats_count" style="border-bottom:none;">
							<span class="count_top"><i class="fa fa-clock-o"></i> Media Espera</span>
							<div class="count green" id="turnero-1-tme">-</div>
						</div>
						<div class="col-md-2 col-sm-2 col-xs-4 tile_stats_count" style="border-bottom:none;">
							<span class="count_top"><i class="fa fa-user"></i> Atendidas</span>
							<div class="count green" id="turnero-1-ate">-</div>
						</div>
						<div class="col-md-2 col-sm-2 col-xs-4 tile_stats_count" style="border-bottom:none;">
							<span class="count_top"><i class="fa fa-user"></i> En Proceso</span>
							<div class="count yellow" id="turnero-1-pro">-</div>
						</div>
						<div class="col-md-2 col-sm-2 col-xs-4 tile_stats_count" style="border-bottom:none;">
							<span class="count_top"><i class="fa fa-user"></i> En Espera</span>
							<div class="count red" id="turnero-1-esp">-</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div id="bg-loader-ajax-liquidaciones" class="bg-loader-ajax">
				<div id="loader-ajax-liquidaciones" class="loader-ajax"><img alt="LC" src="img/generales/logo_lujan_001.png"/></div>
			</div>
			<div class="x_title">
				<h2>Sueldos <span id="liquidaciones-fecha">(-)</span></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-search"></i></a>
					</li>
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="row">
					<div class="col-md-6 col-sm-12 col-xs-12">
						<h4 class="text-center">Cantidad Empleados</h4>
						<div class="chart-responsive">
							<div id="chart_cantidades" style="height:450px;"></div>
						</div>
					</div>
					<div class="col-md-6 col-sm-12 col-xs-12">
						<h4 class="text-center">Sueldo Neto</h4>
						<div class="chart-responsive">
							<div id="chart_importes" style="height:450px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-8 col-sm-8 col-xs-12">
		<div class="x_panel">
			<div id="bg-loader-ajax-recaudaciones" class="bg-loader-ajax">
				<div id="loader-ajax-recaudaciones" class="loader-ajax"><img alt="LC" src="img/generales/logo_lujan_001.png"/></div>
			</div>
			<div class="x_title">
				<h2>Recaudaciones <span id="recaudaciones-fecha">(-)</span></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-search"></i></a>
					</li>
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<h4 class="text-center">Mensual</h4>
						<div class="chart-responsive">
							<div id="chart_recaudaciones" style="height:450px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel">
			<div id="bg-loader-ajax-transferencias" class="bg-loader-ajax">
				<div id="loader-ajax-transferencias" class="loader-ajax"><img alt="LC" src="img/generales/logo_lujan_001.png"/></div>
			</div>
			<div class="x_title">
				<h2>Transferencias ON-LINE <span id="transferencias-fecha">(-)</span></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-search"></i></a>
					</li>
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="row tile_count">
					<div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count">
						<span class="count_top"><i class="fa fa-clock-o"></i> Iniciadas</span>
						<div class="count green" id="transferencias-ini">-</div>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count">
						<span class="count_top"><i class="fa fa-clock-o"></i> Finalizadas</span>
						<div class="count red" id="transferencias-fin">-</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel">
			<div id="bg-loader-ajax-vales-combustible" class="bg-loader-ajax">
				<div id="loader-ajax-vales-combustible" class="loader-ajax"><img alt="LC" src="img/generales/logo_lujan_001.png"/></div>
			</div>
			<div class="x_title">
				<h2>Vales de combustible emitidos <span id="vales-combustible-fecha">(-)</span></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-search"></i></a>
					</li>
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="row">
					<h4 class="text-center">Litros</h4>
					<div class="chart-responsive">
						<div id="chart_tipos_combustible" style="height:230px;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	//var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	$(document).ready(function() {
		var AR = d3.formatLocale({
			"decimal": ",",
			"thousands": ".",
			"grouping": [3],
			"currency": ["$", ""]
		});
		var entero = AR.format(",.0f");
		var porcentaje = AR.format(".2%");
		var pesos_int = AR.format("$,.0f");

		//TABLERO
		$.ajax({
			type: "GET",
			url: CI.base_url + 'tablero/escritorio/turnero_data',
			dataType: "json",
			success: function(response) {
				$('#turnero-fecha').html(response.fecha ? '(' + response.fecha + ')' : '(-)');
				if (response[3]) {
					$('#turnero-3-tma').html(response[3][8] ? response[3][8]['tma'] : '-');
					$('#turnero-3-tme').html(response[3][8] ? response[3][8]['tme'] : '-');
					$('#turnero-3-ate').html(response[3][8] ? response[3][8]['cantidad'] : '-');
					$('#turnero-3-pro').html((response[3][2] || response[3][3]) ? ((response[3][2] ? parseInt(response[3][2]['cantidad']) : 0) + (response[3][3] ? parseInt(response[3][3]['cantidad']) : 0)) : '-');
					$('#turnero-3-esp').html(response[3][1] ? response[3][1]['cantidad'] : '-');
				}
				if (response[1]) {
					$('#turnero-1-tma').html(response[1][8] ? response[1][8]['tma'] : '-');
					$('#turnero-1-tme').html(response[1][8] ? response[1][8]['tme'] : '-');
					$('#turnero-1-ate').html(response[1][8] ? response[1][8]['cantidad'] : '-');
					$('#turnero-1-pro').html((response[1][2] || response[1][3]) ? ((response[1][2] ? response[1][2]['cantidad'] : 0) + (response[1][3] ? response[1][3]['cantidad'] : 0)) : '-');
					$('#turnero-1-esp').html(response[1][1] ? response[1][1]['cantidad'] : '-');
				}
			},
			error: function(response) {
				console.log(response);
			},
			complete: function() {
				$('#bg-loader-ajax-tablero').hide();
			}
		});

		//LIQUIDACIONES
		$.ajax({
			type: "GET",
			url: CI.base_url + 'tablero/escritorio/liquidaciones_data',
			dataType: "json",
			success: function(response) {
				$('#liquidaciones-fecha').html(response.fecha ? '(' + response.fecha + ')' : '(-)');
				if (response.grafico_cantidades) {
					chart_cantidades = c3.generate({
						bindto: '#chart_cantidades',
						data: {
							type: 'pie',
							columns: response.grafico_cantidades
						},
						pie: {
							label: {
								threshold: 0.1,
								format: function(value, ratio, id) {
									return entero(value);
								}
							}
						},
						tooltip: {
							format: {
								value: function(value, ratio, id) {
									var tt = entero(value) + ' (' + porcentaje(ratio) + ')';
									return tt;
								}
							}
						}
					});
				}
				if (response.grafico_importes) {
					chart_importes = c3.generate({
						bindto: '#chart_importes',
						data: {
							type: 'pie',
							columns: response.grafico_importes
						},
						pie: {
							label: {
								threshold: 0.1,
								format: function(value, ratio, id) {
									return pesos_int(value);
								}
							}
						},
						tooltip: {
							format: {
								value: function(value, ratio, id) {
									var tt = pesos_int(value) + ' (' + porcentaje(ratio) + ')';
									return tt;
								}
							}
						}
					});
				}
			},
			error: function(response) {
				console.log(response);
			},
			complete: function() {
				$('#bg-loader-ajax-liquidaciones').hide();
			}
		});

		//RECAUDACIONES
		$.ajax({
			type: "GET",
			url: CI.base_url + 'tablero/escritorio/recaudaciones_data',
			dataType: "json",
			success: function(response) {
				$('#recaudaciones-fecha').html(response.fecha ? '(al ' + response.fecha + ')' : '(-)');
				if (response.grafico_recaudaciones) {
					chart_recaudaciones = c3.generate({
						bindto: '#chart_recaudaciones',
						data: {
							type: 'pie',
							columns: response.grafico_recaudaciones
						},
						pie: {
							label: {
								threshold: 0.1,
								format: function(value, ratio, id) {
									return pesos_int(value);
								}
							}
						},
						tooltip: {
							format: {
								value: function(value, ratio, id) {
									var tt = pesos_int(value) + ' (' + porcentaje(ratio) + ')';
									return tt;
								}
							}
						}
					});
				}
			},
			error: function(response) {
				console.log(response);
			},
			complete: function() {
				$('#bg-loader-ajax-recaudaciones').hide();
			}
		});

		//TRANSFERENCIAS
		$.ajax({
			type: "GET",
			url: CI.base_url + 'tablero/escritorio/transferencias_data',
			dataType: "json",
			success: function(response) {
				$('#transferencias-ini').html(response.iniciadas ? response.iniciadas : '-');
				$('#transferencias-fin').html(response.finalizadas ? response.finalizadas : '-');
				$('#transferencias-fecha').html(response.fecha ? '(' + response.fecha + ')' : '(-)');
			},
			error: function(response) {
				console.log(response);
			},
			complete: function() {
				$('#bg-loader-ajax-transferencias').hide();
			}
		});

		//VALES COMBUSTIBLE
		$.ajax({
			type: "GET",
			url: CI.base_url + 'tablero/escritorio/vales_combustible_data',
			dataType: "json",
			success: function(response) {
				$('#vales-combustible-fecha').html(response.fecha ? '(' + response.fecha + ')' : '(-)');
				if (response.grafico_tipos_combustible) {
					chart_tipos_combustible = c3.generate({
						bindto: '#chart_tipos_combustible',
						data: {
							type: 'pie',
							columns: response.grafico_tipos_combustible
						},
						color: {
							pattern: ['#9467bd', '#8c564b', '#c49c94', '#bcbd22', '#e377c2', '#f7b6d2', '#c7c7c7', '#7f7f7f', '#dbdb8d', '#17becf', '#9edae5', '#c5b0d5']
						},
						pie: {
							label: {
								format: function(value, ratio, id) {
									return value;
								}
							}
						},
						tooltip: {
							format: {
								value: function(value, ratio, id) {
									var tt = entero(value) + ' (' + porcentaje(ratio) + ')';
									return tt;
								}
							}
						}
					});
				}
			},
			error: function(response) {
				console.log(response);
			},
			complete: function() {
				$('#bg-loader-ajax-vales-combustible').hide();
			}
		});
	});
</script>