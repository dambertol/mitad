<!--
    /*
     * Vista Ver Trámite.
     * Autor: Leandro
     * Creado: 31/05/2021
     * Modificado: 21/06/2021 (Leandro)
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
                <h2>Información del Trámite: <?php echo $tramite->proceso; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <!-- prueba de inserción 25/01-->
            <h2 class="text-center">Progreso de Trámite</h2>
                        <div class="container-progress">                           
                            <?php foreach ($pases as $Pase): ?>
                            <?php  
                            $arr = array();                                                       
                                foreach ($estados as $Estado) {
                                    if($Estado->nombre !== "00 - Anulado"){
                                        array_push($arr, $Estado->nombre);
                                    }
                                }               
                                    for ($i=1; $i <= count($arr) ; $i++) {                                                                                                    
                                        if($i == substr($Pase->estado_destino,0,strpos($Pase->estado_destino, " "))){
                                            $mult = $i * 100;
                                            $media = $mult/count($arr);
                                            $entero = round($media);    
                                            break;                                                                                                                                               
                                        }else{
                                            $entero = 0;
                                        }                                                                                                                                                                   
                                    }                        
                            ?>    
                                <?php if($Pase === end($pases)): ?>
                                <?php if( $entero === 0): ?> 
                                    <strong class="active"></strong>                                                          
                                <?php else: ?> 
                                    <strong class="active">Se encuentra en:</strong>
                                    <p><?=substr($Pase->estado_destino,strpos($Pase->estado_destino, "-")+1); ?></p>                                   
                                <?php endif; ?>                                                                                              
                                <?php endif; ?>  
                            <?php endforeach; ?>                        
                                <div id="<?= $entero == 0 ? "Anulado" : "progressbar" ?>" aria-valuemin="0" aria-valuemax="100" style="--value: <?php echo $entero ?>"> <?= $entero == 0 ? "TRÁMITE ANULADO" : "" ?></div>                                                            
                        </div>
              
                <div class="ln_solid"></div>                    
                <!-- fin prueba de inserción 25/01 -->
                <div class="form-horizontal">
                    <h2 class="text-center">Historial de Pases</h2>
                    <table class="table table-hover table-bordered table-condensed table-striped dt-responsive dataTable no-footer dtr-inline"
                           role="grid">
                        <thead>
                        <tr>
                            <th style="width:12%;">Fecha</th>
                            <th style="width:24%;">Origen</th>
                            <th style="width:24%;">Destino</th>
                            <th style="width:34%;">Observaciones</th>
                            <th style="width:6%;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($pases)): ?>
                            <?php $cant_pases = 0; ?>
                            <?php $style = 'color:black;'; ?>
                            <?php foreach ($pases as $Pase): ?>
                                <?php $cant_pases++; ?>
                                <?php if ($cant_pases === sizeof($pases)): ?>
                                    <?php $style = 'color:#DEC10E; font-weight:bold;'; ?>
                                <?php endif; ?>
                                <?php if (substr($Pase->estado_destino,strpos($Pase->estado_destino,"-")+2) === 'Finalizado ()'): ?>
                                    <?php $style = 'color:green; font-weight:bold;'; ?>
                                    <?php endif; ?>
                                <?php if (substr($Pase->estado_destino,strpos($Pase->estado_destino,"-")+2) === 'Anulado ()'): ?>
                                    <?php $style = 'color:red; font-weight:bold;'; ?>
                                    <?php endif; ?>
                                <tr style="<?php echo $style; ?>">
                                    <td><?= empty($Pase->fecha_inicio) ? '' : date_format(new DateTime($Pase->fecha_inicio), 'd/m/Y H:i:s'); ?></td>
                                    <td><?= substr($Pase->estado_origen,strpos($Pase->estado_origen, "-")+1); ?></td>
                                    <td><?= substr($Pase->estado_destino,strpos($Pase->estado_destino, "-")+1); ?></td>
                                    <td><?= $Pase->observaciones; ?></td>
                                    <td>
                                        <a style="<?php echo $style; ?>" class="btn btn-xs btn-default" data-remote="false"
                                           data-toggle="modal" data-target="#remote_modal"
                                           href="tramites_online/pases/modal_ver/<?= $Pase->id; ?>"><i class="fa fa-search"></i></a>

                                        <?php if ($tramite->editable && $Pase->estado_origen_editable === 'SI'): ?>
                                            <?php if ($grupo === 'admin'): ?>
                                                <a style="<?php echo $style; ?>" class="btn btn-xs btn-default" data-remote="false"
                                                   data-toggle="modal" data-target="#remote_modal"
                                                   href="tramites_online/pases/modal_editar/<?= $Pase->id; ?>"><i class="fa fa-pencil"></i></a>
                                            <?php elseif ($grupo === 'publico' && is_null($Pase->estado_origen_oficina)): ?>
                                                <a style="<?php echo $style; ?>" class="btn btn-xs btn-default" data-remote="false"
                                                   data-toggle="modal" data-target="#remote_modal"
                                                   href="tramites_online/pases/modal_editar/<?= $Pase->id; ?>"><i class="fa fa-pencil"></i></a>
                                            <?php elseif ($grupo === 'area' && is_null($Pase->estado_origen_oficina)): ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">-- Sin pases --</td>
                            </tr>

                        <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($txt_btn)): ?>
                        <div class="ln_solid"></div>
                        <div class="text-center">
                            <?php if ($allow_edit_pases): ?>
                                <?php if (!$tramite->editable): ?>
                                    <a href="tramites_online/tramites/enable_edit/<?php echo $tramite->id; ?>"
                                       class="btn btn-warning btn-sm">Habilitar Edicion de pases</a>
                                <?php else: ?>
                                    <a href="tramites_online/tramites/enable_edit/<?php echo $tramite->id; ?>"
                                       class="btn btn-primary btn-sm">Desactivar Edicion de pases</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="tramites_online/tramites/<?php echo $back_url; ?>" class="btn btn-default btn-sm">Cancelar</a>
                        </div>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo(!empty($audi_modal) ? $audi_modal : ''); ?>
<script>
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';

    $(document).ready(function () {
        $('#smartwizard').smartWizard({
            selected: <?= $cant_pases; ?>,
            theme: 'arrows',
            transitionEffect: 'fade',
            keyNavigation: false,
            useURLhash: false,
            showStepURLhash: false,
            lang: {
                next: 'Siguiente',
                previous: 'Anterior'
            },
            anchorSettings: {
                markDoneStep: true,
                markAllPreviousStepsAsDone: true,
                removeDoneStepOnNavigateBack: true,
                enableAnchorOnDoneStep: true
            }
        });
    });


// Actualiza el color de progressbar según su porcentaje  
let getId = { 
        id : function(str){return document.getElementById(str)} 
};
let pb;
const stylesheet = document.styleSheets[8];
for(let i = 0; i < stylesheet.cssRules.length; i++) {
  if(stylesheet.cssRules[i].selectorText === '#progressbar') {
    pb = stylesheet.cssRules[i];
  }
}
	if(getComputedStyle(getId.id('progressbar')).getPropertyValue('--percentage') == 0){
		pb.style.setProperty('--primary','#d9534f');
        pb.style.setProperty('--secondary','#ffafad');
	}
	else if(getComputedStyle(getId.id('progressbar')).getPropertyValue('--percentage') >= 75){
        pb.style.setProperty('--secondary','#caffca');
		pb.style.setProperty('--primary','#5cb85c');
	}else{
		pb.style.setProperty('--primary','#FFD318');
	}

</script> 