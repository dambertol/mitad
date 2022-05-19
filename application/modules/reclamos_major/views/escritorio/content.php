<!--
   /*
    * Vista Escritorio
    * Autor: Leandro
    * Creado: 17/12/2019
    * Modificado: 04/11/2020 (Leandro)	
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Escritorio'; ?><small>Versión 1.0.2</small></h2>
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
                    <h2>Incidencias por mes (último año)</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="chart-responsive">
                            <div id="chart_iniciados" style="height:300px;"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Incidencias iniciadas por areas (último mes)</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="chart-responsive">
                            <div id="chart_areas" style="height:450px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
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
                        'iniciadas': 'Iniciadas',
                        'solucionadas': 'Solucionadas',
                        'cerradas': 'Cerradas'
                    },
                    labels: {
                        format: entero
                    },
                    colors: {
                        'iniciadas': '#26b99a',
                        'solucionadas': '#e74c3c',
                        'cerradas': '#5bc0de'
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
                            text: 'Incidencias',
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
                },
                tooltip: {
                    format: entero
                }
            });
            chart_areas = c3.generate({
                bindto: '#chart_areas',
                data: {
                    type: 'pie',
                    columns: <?php echo $graficos_data['grafico_areas']; ?>
                },
                pie: {
                    label: {
                        threshold: 0.1,
                        format: function (value, ratio, id) {
                            return entero(value);
                        }
                    }
                },
                tooltip: {
                    format: {
                        value: function (value, ratio, id) {
                            var tt = entero(value) + ' (' + porcentaje(ratio) + ')';
                            return tt;
                        }
                    }
                }
            });
        });
    </script>
<?php endif; ?>