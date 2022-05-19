<!--
   /*
        * Vista Escritorio
        * Autor: Leandro
        * Creado: 01/06/2018
        * Modificado: 24/02/2021 (Leandro)	
        */
-->
<style>
    .c3-line{
        stroke-width: 3px;
    }
    .label-ini{
        stroke-width: 1px;
    }
    .label-ini line{
        stroke: #26b99a;
    }
    .label-ini text{
        fill: #26b99a;
    }
    .label-fin{
        stroke-width: 1px;
    }
    .label-fin line{
        stroke: #e74c3c;
    }
    .label-fin text{
        fill: #e74c3c;
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Escritorio'; ?><small>Versión 1.1.12</small></h2>
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
        <div class="col-md-8">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Trámites por mes (último año)</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="chart-responsive">
                            <div id="chart_iniciados" style="height:400px;"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Trámites pendientes</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="chart-responsive">
                            <div id="chart_pendientes" style="height:400px;"></div>
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
            chart_iniciados = c3.generate({
                bindto: '#chart_iniciados',
                data: {
                    type: 'line',
                    x: 'x',
                    names: {
                        'iniciados': 'Iniciados',
                        'finalizados': 'Finalizados'
                    },
                    labels: {
                        format: entero
                    },
                    colors: {
                        'iniciados': '#26b99a',
                        'finalizados': '#e74c3c'
                    },
                    columns: <?php echo $graficos_data['grafico_iniciados']; ?>
                },
                axis: {
                    x: {
                        tick: {
                            rotate: -60
                        },
                        type: 'category'
                    },
                    y: {
                        label: {
                            text: 'Trámites',
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
                        show: true,
                        lines: [
                            {value: <?php echo $graficos_data['promedios_iniciados']['iniciados']; ?>, text: 'Promedio: <?php echo number_format($graficos_data['promedios_iniciados']['iniciados']); ?>', position: 'start', class: 'label-ini'},
                            {value: <?php echo $graficos_data['promedios_iniciados']['finalizados']; ?>, text: 'Promedio: <?php echo number_format($graficos_data['promedios_iniciados']['finalizados']); ?>', class: 'label-fin'}
                        ]
                    }
                },
                tooltip: {
                    format: entero
                }
            });
            chart_pendientes = c3.generate({
                bindto: '#chart_pendientes',
                data: {
                    type: 'pie',
                    columns: <?php echo $graficos_data['grafico_pendientes']; ?>
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