<!--
   /*
	* Vista Escritorio
	* Autor: Leandro
	* Creado: 02/09/2019
	* Modificado: 02/07/2019 (Leandro)	
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Escritorio'; ?><small>Versión 1.0.3</small></h2>
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
<?php if (!empty($graficos_data)) : ?>
	<div class="row">
		<div class="col-xs-6">
			<div class="x_panel">
				<div class="x_title">
					<h2>Equipos por estado</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_equipos" style="height:400px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="x_panel">
				<div class="x_title">
					<h2>Líneas por estado</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_lineas" style="height:400px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
	<script>
		$(document).ready(function() {
			var AR = d3.formatLocale({
				"decimal": ",",
				"thousands": ".",
				"grouping": [3],
				"currency": ["$", ""]
			});
			var entero = AR.format(",.0f");
			var porcentaje = AR.format(".2%");
			chart_equipos = c3.generate({
				bindto: '#chart_equipos',
				data: {
					type: 'pie',
					columns: <?php echo $graficos_data['grafico_equipos']; ?>
				},
				color: {
					pattern: ['#5cb85c', '#5bc0de', '#f0ad4e', '#d9534f']
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
			chart_lineas = c3.generate({
				bindto: '#chart_lineas',
				data: {
					type: 'pie',
					columns: <?php echo $graficos_data['grafico_lineas']; ?>
				},
				color: {
					pattern: ['#d9534f', '#5cb85c', '#5bc0de', '#f0ad4e', '#777777']
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
							var format = d3.format('.2%');
							var tt = entero(value) + ' (' + porcentaje(ratio) + ')';
							return tt;
						}
					}
				}
			});
		});
	</script>
<?php endif; ?>