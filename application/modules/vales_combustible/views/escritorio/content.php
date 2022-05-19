<!--
   /*
	* Vista Escritorio
	* Autor: Leandro
	* Creado: 03/11/2017
	* Modificado: 24/02/2021 (Leandro)
	*/
-->
<style>
	.c3-line{
		stroke-width: 3px;
	}
</style>
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Escritorio'; ?><small>Versión 2.2.9</small></h2>
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
<?php if (!empty($graficos_areas_data)) : ?>
	<div class="row">
		<?php foreach ($graficos_areas_data as $Area) : ?>
			<?php foreach ($Area as $Tipo) : ?>
				<div class="col-md-4">
					<div class="x_panel">
						<div class="x_title">
							<h2>Cupo mensual <?php echo $Tipo['tipo_nombre']; ?><small>(<?php echo $Tipo['area_nombre']; ?>)</small></h2>
							<div class="clearfix"></div>
						</div>
						<div class="x_content">
							<div class="row">
								<div class="chart-responsive">
									<div id="chart_areas_<?php echo $Tipo['area_id']; ?>_<?php echo $Tipo['tipo_id']; ?>" style="height:300px;"></div>
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
						chart_areas = c3.generate({
							bindto: '#chart_areas_<?php echo $Tipo['area_id']; ?>_<?php echo $Tipo['tipo_id']; ?>',
							data: {
								columns: [
									['Litros/M3', <?php echo $Tipo['cupo_mensual_usado']; ?>]
								],
								type: 'gauge'
							},
							gauge: {
								label: {
									format: function(value, ratio) {
										return entero(value);
									},
									show: true
								},
								min: 0,
								max: <?php echo $Tipo['cupo_mensual']; ?>,
								width: 39
							},
							color: {
								pattern: ['#FFD318'],
							},
							size: {
								height: 180
							}
						})
					});
				</script>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
<?php if (!empty($graficos_data)) : ?>
	<div class="row">
		<div class="col-md-8">
			<div class="x_panel">
				<div class="x_title">
					<h2>Vales por mes (último año)</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_vales" style="height:300px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="x_panel">
				<div class="x_title">
					<h2>Vales por estado (último año)</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_estados" style="height:300px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<div class="x_panel">
				<div class="x_title">
					<h2>Litros por mes (último año)</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_combustible" style="height:300px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="x_panel">
				<div class="x_title">
					<h2>Litros por combustible (último año)</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_tipos" style="height:300px;"></div>
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
			chart_vales = c3.generate({
				bindto: '#chart_vales',
				data: {
					x: 'x',
					names: {
						'vales': 'Vales'
					},
					colors: {
						'vales': '#4e4c4e'
					},
					columns: <?php echo $graficos_data['grafico_vales']; ?>
				},
				axis: {
					x: {
						tick: {
							rotate: -75
						},
						type: 'category'
					},
					y: {
						label: {
							text: 'Vales',
							position: 'outer-middle'
						},
						padding: {
							top: 20,
							bottom: 0
						},
						tick: {
							format: entero
						}
					}
				},
				grid: {
					y: {
						show: true
					}
				}
			});
			chart_estados = c3.generate({
				bindto: '#chart_estados',
				data: {
					type: 'pie',
					columns: <?php echo $graficos_data['grafico_estados']; ?>
				},
				color: {
					pattern: ['#d9534f', '#5cb85c', '#777777', '#5bc0de', '#f0ad4e']
				},
				pie: {
					label: {
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
			chart_combustible = c3.generate({
				bindto: '#chart_combustible',
				data: {
					x: 'x',
					names: {
						<?php foreach ($graficos_data['tipos_combustible'] as $Tipo) : ?>
							<?php echo "'$Tipo->nombre': '$Tipo->nombre', "; ?>
						<?php endforeach; ?>
					},
					columns: <?php echo $graficos_data['grafico_combustible']; ?>
				},
				color: {
					pattern: ['#9467bd', '#8c564b', '#c49c94', '#bcbd22', '#e377c2', '#f7b6d2', '#c7c7c7', '#7f7f7f', '#dbdb8d', '#17becf', '#9edae5', '#c5b0d5']
				},
				tooltip: {
					contents: function(d, defaultTitleFormat, defaultValueFormat, color) {
						var total = d.reduce(function(subTotal, b) {
							return subTotal + b.value;
						}, 0);
						d.push({value: total, id: "TOTAL", name: "TOTAL VALES", x: d[0].x, index: d[0].index});
						return this.getTooltipContent(d, defaultTitleFormat, defaultValueFormat, color);
					}
				},
				axis: {
					x: {
						tick: {
						rotate: -75
					},
					type: 'category'
				},
					y: {
						label: {
							text: 'Litros',
							position: 'outer-middle'
						},
						padding: {
							top: 20,
							bottom: 0
						},
						tick: {
							format: entero
						}
					}
				},
				grid: {
					y: {
						show: true
					}
				}
			});
			chart_tipos = c3.generate({
				bindto: '#chart_tipos',
				data: {
					type: 'pie',
					columns: <?php echo $graficos_data['grafico_tipos']; ?>
				},
				color: {
					pattern: ['#9467bd', '#8c564b', '#c49c94', '#bcbd22', '#e377c2', '#f7b6d2', '#c7c7c7', '#7f7f7f', '#dbdb8d', '#17becf', '#9edae5', '#c5b0d5']
				},
				pie: {
					label: {
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
<?php if (!empty($graficos_autorizaciones_data)) : ?>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Autorizaciones (Cantidad) por mes (último año)</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_autorizaciones" style="height:300px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Autorizaciones (Litros) por mes (último año)</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_autorizaciones_litros" style="height:300px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			chart_autorizaciones = c3.generate({
				bindto: '#chart_autorizaciones',
				data: {
					x: 'x',
					names: {
						'autorizaciones': 'Autorizadas',
						'cargas': 'Cargadas',
					},
					colors: {
						'autorizaciones': '#26b99a',
						'cargas': '#e74c3c'
					},
					columns: <?php echo $graficos_autorizaciones_data['grafico_autorizaciones']; ?>
				},
				axis: {
					x: {
						tick: {
							rotate: -75
						},
						type: 'category'
					},
					y: {
						label: {
							text: 'Cantidad',
							position: 'outer-middle'
						},
						padding: {
							top: 20,
							bottom: 0
						}
					}
				},
				grid: {
					y: {
						show: true
					}
				}
			});
			chart_autorizaciones_litros = c3.generate({
				bindto: '#chart_autorizaciones_litros',
				data: {
					x: 'x',
					names: {
						'cargas': 'Litros Cargados',
					},
					colors: {
						'cargas': '#e74c3c'
					},
					columns: <?php echo $graficos_autorizaciones_data['grafico_autorizaciones_litros']; ?>
				},
				axis: {
					x: {
						tick: {
							rotate: -75
						},
						type: 'category'
					},
					y: {
						label: {
							text: 'Litros',
							position: 'outer-middle'
						},
						padding: {
							top: 20,
							bottom: 0
						}
					}
				},
				grid: {
					y: {
						show: true
					}
				}
			});
		});
	</script>
<?php endif; ?>