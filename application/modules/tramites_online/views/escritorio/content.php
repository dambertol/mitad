<!--
   /*
        * Vista Escritorio
        * Autor: Leandro
        * Creado: 16/03/2020
        * Modificado: 23/08/2021 (Leandro)
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Escritorio'; ?><small>Versión 1.0.122</small></h2> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			
<!--				
 /*
        * Vista Buscador
        * Autor: Pablo
        * Creado: 01/11/2021 (Pablo Frigolé)
        */
-->	
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" /> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>

<select class="itemName form-control" style="width: 27%; max-width: 300px" name="itemName"></select>
<input type="button" value="Iniciar" id="boton_iniciar" class="btn btn-primary btn-sm" title="Iniciar">
	
<script type="text/javascript">
      $('.itemName').select2({
	minimumInputLength: 3,
        allowClear: true,
	placeholder: '--- Busque un Trámite ---',
        ajax: {
          url: '/tramites_online/ajax/search',
          dataType: 'json',
          delay: 250,
          processResults: function (data) {
            return {
              results: data
            };
          },
//          cache: true
        }
      });

</script>
<script type="text/javascript">
    
    $('#boton_iniciar').on('click', function(event) {
        event.preventDefault(); 
        var data= $('.itemName').val();
        if (data === null){
            alert("Elija una opcion valida");
            return;
        }
        console.log(data);
        var url = '/tramites_online/tramites/agregar/' + data;

        location.replace(url);
    });
</script>
<!--				
 /*
        * Termina Vista Buscador
        * Autor: Pablo
        * Creado: 01/11/2021 (Pablo Frigolé)
        */       
-->		    
        </div>
            <div class="x_content">
                <div class="row">
                    <?php if (!empty($accesos_esc)) : ?>
                        <?php if (!empty($agregar)) : ?>
                            <div class="animated flipInY col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <a href="tramites_online/tramites/modal_iniciar" class="tile-stats" data-remote="false" data-toggle="modal" data-target="#remote_modal">
                                    <div class="icon fa fa-file"></div>
                                    <h3>Iniciar Trámite</h3>
                                </a>
                            </div>
                        <?php endif; ?>
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
<?php if (!empty($tramites_frecuentes_data)) : ?>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Trámites frecuentes</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <?php foreach ($tramites_frecuentes_data as $Tramite) : ?>
                            <script>
                                $(document).ready(function() {
                                    if(parseInt(<?php echo $Tramite['iniciador']?>) !== 1){
                                        $('.init<?php echo $Tramite['iniciador']?>').addClass('init-prof')
                                    }
                                })
                            </script>          
                            <div class="animated flipInY col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="tile-stats tile-stats-mini init<?php echo $Tramite['iniciador']?>" onclick="location.href = CI.base_url + '<?php echo $Tramite['href']; ?>'">
                                    <?php echo $Tramite['span']; ?>
                                    <h3 class=""><?php echo $Tramite['title']; ?></h3>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if (!empty($graficos_data)) : ?>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Trámites por mes (último año)</h2>
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
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Trámites pendientes</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="chart-responsive">
                            <div id="chart_pendientes" style="height:450px;"></div>
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
                        show: true
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
        
