<!--
   /*
	* Vista Escritorio
	* Autor: Leandro
	* Creado: 21/03/2019
	* Modificado: 02/07/2020 (Leandro)	
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Escritorio'; ?><small>Versión 1.0.4</small></h2>
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
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Torres por estado</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_estados" style="height:450px;"></div>
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
			chart_estados = c3.generate({
				bindto: '#chart_estados',
				data: {
					type: 'pie',
					columns: <?php echo $graficos_data['grafico_estados']; ?>
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
		});
	</script>
<?php endif; ?>