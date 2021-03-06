<!--
        /*
         * Vista ABM de curriculum.
         * Autor: Leandro
         * Creado: 10/10/2018 (Leandro)
         * Modificado: 16/10/2018 (Pablo)
         */
-->
<style> legend.group-border {
        width: inherit;
        /* Or auto */
        padding: 0 10px;
        /* To give a bit of padding on the left and right */
        border-bottom: none;
        margin-bottom: 0px;
    }
    fieldset.group-border {
        border: 1px groove #ddd !important;
        padding: 0 1.4em 1.4em 1.4em !important;
        margin: 0 0 1.5em 0 !important;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
    }
</style>
<script>
    	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	$(document).on('click', '[data-toggle="lightbox"]', function(event) {
		event.preventDefault();
		$(this).ekkoLightbox({
			alwaysShowClose: true
		});
	});
       var empleo_id = '<?php echo!empty($cuil) ? $cuil : 0; ?>';
function eliminar_adjunto(adjunto_id, adjunto_nombre, empleo_id) {
        if (empleo_id !== undefined) {
            var name = 'adjunto_eliminar_existente';
        } else {
            var name = 'adjunto_eliminar_existente';
        }
        Swal.fire({
            title: 'Confirmar',
            text: "Se eliminarĂ¡ el adjunto",
            type: 'info',
            showCloseButton: true,
            showCancelButton: true,
            focusCancel: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-primary',
            cancelButtonClass: 'btn btn-default',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                $('#adjunto_' + adjunto_id).find('input').attr('name', name+'['+adjunto_id+']');
                $('#adjunto_' + adjunto_id).attr('style','display:none');
            }
        })
    };

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
        <article class="x_panel">
            <div class="x_title">
                <h2><?php echo (!empty($title_view)) ? $title_view : 'pedir_empleo'; ?></h2>
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
                        <div><div><div>
                        
                        <section>  
<!--  ********************************aca ocurre la magia **************** ************************-->
                        <div class="row" id="row-persona">
                            <h2 class="text-center">Datos personales</h2>
                            <?php foreach ($field as $field): ?>
                                <div class="change_col col-md-6 form-group">
                                    <?php echo $field['label']; ?> 
                                    <?php echo $field['form']; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    
                    
                        <div class="row" id="row-domicilio">
                            <br />
                            <h2 class="text-center form-group">Datos domilicio</h2>
                            <?php foreach ($fields_domicilio as $field_domicilio): ?>
                            <div class="change_col col-md-6 form-group">
                                    <?php echo $field_domicilio['label']; ?> 
                                    <?php echo $field_domicilio['form']; ?>
                            </div>
                            <?php endforeach; ?>
                        <!-- </div> -->
</section>
<section>




                    <div class="ln_solid"></div>



                    <div class="row">
                        <div class="change_col col-md-6 form-group">
                                <label for="capacitacion" class="control-label col-sm-3">Capacitacion</label> 
                            <div class="col-sm-9">
                                <?php echo $fields['capacitacion']['form']; ?> 
                                <input type='checkbox' name="capacitacion" class="selectpicker bootstrap-checkbox capacitacio form-control" id="capacitacio" value="s">
                            </div>
                        </div>
                        
                        <div class="change_col col-md-6 form-group">
                                <label for="horAArio_cap" class="control-label col-sm-3">Horario disponible</label> 
                            <div class="col-sm-9">
                                        <select name="horAArio_cap" class="form-control selectpicker horAArio_cap" id="horAArio_cap" data-selected-text-format="count>5" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                        <?php echo $fields['horario_cap']['form']; ?> 
                                        </select>
                                    
                            </div>
                        </div>

                        <div class="change_col col-md-6 form-group">
                                        <?php echo $fields['intereses_cap']['label']; ?> 
                                        <?php echo $fields['intereses_cap']['form']; ?> 
                        </div>
                    <!-- </div> -->
</section>     
                <section>





                    <div class="ln_solid"></div>
                    
                    
                    
                    
                    
                  
                    <div class="row">
                        <div class="change_col col-md-6 form-group">
                            <label for="busca_empleo" class="control-label col-sm-3">Busqueda de empleo</label> 
                            <div class="col-sm-9">
                                        <?php echo $fields['busca_empleo']['form']; ?> 
                                        <input type='checkbox' name="busca_empleo" class="selectpicker form-control bootstrap-checkbox busca_emple" id="busca_emple" value="s">
                            </div>
                        </div>

                        <div class="change_col col-md-6 form-group">
                                        <label for="interes_lAAb" class="control-label col-sm-3">Intereses laborales</label> 
                            <div class="col-sm-9">
                                            <select name="interes_lAAb" class="form-control selectpicker interes_lAAb" id="interes_lAAb" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                            <?php echo $fields['interes_lab']['form']; ?> 
                                            </select>
                                 
                            </div>
                        </div>

                        <div class="change_col col-md-6 form-group">
                                        <label for="disponib_lAAb" class="control-label col-sm-3">Disponibilidad horaria</label> 
                            <div class="col-sm-9">
                                            <select name="disponib_lAAb" class="form-control selectpicker disponib_lAAb" id="disponib_lAAb" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                            <?php echo $fields['disponib_lab']['form']; ?> 
                                            </select>
                            </div>
                        </div>

                        <div class="change_col col-md-6 form-group">
                                        <label for="condicAA " class="control-label col-sm-3">condiciones especiales de trabajo</label> 
                            <div class="col-sm-9">
                                    
                                            <select name="condicAA " class="form-control selectpicker condicAA " id="condicAA " data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                            <?php echo $fields['condic']['form']; ?> 
                                            </select>
                            </div> 
                         </div> 
                    <!-- </div>  -->
</section> 
<section>






                    <div class="ln_solid"></div>




                    <div class="row">

                        <div class="change_col col-md-6 form-group">
                            <label for="movilidAAd" class="control-label col-sm-3">Vehiculo propio</label> 
                            <div class="col-sm-9">
                                                <select name="movilidAAd" class="form-control selectpicker movilidAAd" id="movilidAAd" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                                <?php echo $fields['movilidad']['form']; ?> 
                                                </select>
                            </div> 
                        </div>
                            
                        <div class="change_col col-md-6 form-group">
                                            <label for="movil_cAArnet" class="control-label col-sm-3">Carnet de conducir</label> 
                            <div class="col-sm-9">
                                <select name="movil_cAArnet" class="form-control selectpicker movil_cAArnet" id="movil_cAArnet" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                                <?php echo $fields['movil_carnet']['form']; ?> 
                                                </select>
                            </div> 
                        </div>
                    <!-- </div> -->
</section>
                    <section> 



                    <div class="ln_solid"></div>
                    
                                 
                    <div class="row">

                        <div class="change_col col-md-6 form-group">
                                <label for="discapAAcidad" class="control-label col-sm-3">Tipo de discapacidad</label> 
                            <div class="col-sm-9">
                                                    <select name="discapAAcidad" class="form-control selectpicker discapAAcidad" id="discapAAcidad" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                                    <?php echo $fields['discapacidad']['form']; ?> 
                                                    </select>
                            </div> 
                        </div>

                        <div class="change_col col-md-6 form-group">
                                    <label for="hijos" class="control-label col-sm-3">Hijos a cargo</label> 
                            <div class="col-sm-9">
                                        <?php echo $fields['hijos']['form']; ?> 
                                        <input type='checkbox' name="hijos" class="selectpicker form-control hijo" id="hijo" value="s">
                            </div>

                        </div> 
                    <!-- </div> -->
                            </section> 
                    <section> 
                    
                    
                    
                    <div class="ln_solid"></div>
                                
                    
                    
                    <div class="row">
                        <div class="change_col col-md-6 form-group">
                                                <?php echo $fields['estudio']['label']; ?> 
                                                <?php echo $fields['estudio']['form']; ?> 
                        </div>

                        <div class="change_col col-md-6 form-group">
                                                <label for="estudiosOtAA " class="control-label col-sm-3">Titulo secundario</label> 
                            <div class="col-sm-9">
                                                    <select name="estudiosOtAA " class="form-control selectpicker titsecundario" id="estudiosOtAA " data-live-search="true" title="-- Seleccionar --" tabindex="null" >
                                                    <?php echo $fields['estudiosOt']['form']; ?> 
                                                    </select>
                            </div> 
                        </div>

                        <div class="change_col col-md-6 form-group">
                                                <label for="grAAdo" class="control-label col-sm-3">Titulo de grado</label> 
                            <div class="col-sm-9">
                                                    <select name="grAAdo" class="form-control selectpicker grAAdo" id="grAAdo" data-live-search="true" title="-- Seleccionar --" tabindex="null" >
                                                    <?php echo $fields['grado']['form']; ?> 
                                                    </select>
                            </div> 
                        </div>
                                
                        <div class="change_col col-md-6 form-group">
                                            <label for="grAAdoo" class="control-label col-sm-3">Rubro</label> 
                            <div class="col-sm-9">
                                                <select name="grAAdoo" class="form-control selectpicker grAAdoo" id="grAAdoo" data-live-search="true" title="-- Seleccionar --" tabindex="null" >
                                                <?php echo $fields['gradoo']['form']; ?> 
                                                </select>
                            </div> 
                        </div>
                    <!-- </div> -->
</section>
<section>


                    <div class="ln_solid"></div>
                        


                    <div id="poliglota" class="row">
                        <div class="change_col col-md-6 form-group" >
                                <label for="idioma" class="control-label col-sm-3">Idiomas</label> 
                            <div class="col-sm-9">
                                        <select name="idioma" class="form-control selectpicker idiomass idioma" id="idioma" data-live-search="true" title="-- Seleccionar --" tabindex="null">
                                            <?php echo $fields['idiomas']['form']; ?>
                                        </select>
                            </div> 
                        
                                <label for="idiomaN" class="control-label col-sm-3">Nivel</label> 
                            <div class="col-sm-9">
                                    <input name="idiomaN" value="" tipe="number" maxlength="1" id="idiomaN" class="form-control Nidioma idioma">
                                    <button id="addCampo" type="button" class="btn btn-info">Agregar idioma</button>
                            </div>
                        </div>
                    </div>



                    <div id="tecno" class="row">
                        <div class="change_col col-md-6 form-group" >
                                <label for="computacio" class="control-label col-sm-3">programas de computacion</label> 
                            <div class="col-sm-9">
                                        <select name="computacio" class="form-control selectpicker computacio computacioss" id="computacio" data-live-search="true" title="-- Seleccionar --" tabindex="null">
                                        <?php echo $fields['computacion']['form']; ?>
                                        </select>
                            </div> 
                        
                                <label for="computacioN" class="control-label col-sm-3">Nivel</label> 
                            <div class="col-sm-9">
                                    <input name="computacioN" value="" tipe="number" maxlength="1" id="computacioN" class="form-control computacio Ncomputacio">
                                    <button id="addCampoCompu" type="button" class="btn btn-info">Agregar programa</button>
                            </div>       
                        </div>
                    </div>

                    <div class="row">
                        <div class="change_col col-md-6 form-group">
                                        <?php echo $fields['cursos']['label']; ?> 
                                        <?php echo $fields['cursos']['form']; ?> 
                        </div>
                    </div>
</section>
<section>
                    <div calss="row">
                        <div class="change_col col-md-6 form-group">
                                    <label for="oficiosAA " class="control-label col-sm-3">oficios</label> 
                            <div class="col-sm-9">
                                        <select name="oficiosAA " class="form-control selectpicker form-control oficiosAA " id="oficiosAA " data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                        <?php echo $fields['oficios']['form']; ?> 
                                        </select>
                            </div> 
                        </div>

                        <div class="change_col col-md-6 form-group">
                                    <?php echo $fields['experiencia']['label']; ?> 
                                    <?php echo $fields['experiencia']['form']; ?> 
                        </div>
                    <!-- </div> -->
</section>

                    <section>
                        


                    <div class="ln_solid"></div>
                    
                    
                    
                    <div class="row">
                        <div class="change_col col-md-6 form-group">
                            <label for="exmuni " class="control-label col-sm-3">TrabajĂ³ en la municipalidad</label> 
                            <div class="col-sm-9">

                                    <?php echo $fields['exmuni']['form']; ?> 
                                    <input type='checkbox' name="exmuni" class="selectpicker form-control exmun" id="exmun" value="s">       
                            </div>
                        <div class="change_col col-md-6 form-group">
                            <label for="famimuni " class="control-label col-sm-3">Familiares en la municipalidad</label> 
                            <div class="col-sm-9">
                                    <?php echo $fields['famimuni']['form']; ?> 
                                    <input type='checkbox' name="famimuni" class="selectpicker form-control famimun" id="famimun" value="s">
                            </div> 
                        </div>
                    </div>


                    <div class="ln_solid"></div>





                    <div class="row">
                        <div class="change_col col-md-9 form-group">
                                    <?php echo $fields['aclaraciones']['label']; ?> 
                                    <?php echo $fields['aclaraciones']['form']; ?> 
                        </div>
                    </div>
</section>
  
<section>
                  




                <div class="ln_solid otro"></div>



                <div class="row" id="row-adjuntos">
                    <br />
                    <h2 class="text-center">
                        GalerĂ­a de Adjuntos
                    </h2>
                    <div id="adjuntos-container" class="col-sm-12">

                    <?php if (!empty($txt_btn) && ($txt_btn === 'Agregar'|| $txt_btn ==='Editar')): ?>
							 <div class="text-center" style="margin-bottom:10px;">
							<a class="btn btn-primary btn-sm" href="oficina_de_empleo/Adjuntos/modal_agregar/pedir_empleo/<?php echo $cuil; ?>" data-remote="false" data-toggle="modal" data-target="#remote_modal" title="Agregar adjunto"><i class="fa fa-plus"></i> Agregar adjunto</a>
							</div> 
					<?php endif; ?>

                        <?php if (!empty($array_adjuntos)): ?>
                            <?php foreach ($array_adjuntos as $Adjunto): ?>
                                    
                                    <?php if ($Adjunto->extension === 'pdf'): ?>
                                        <?php $preview = '<object type="application/pdf" data="' . $Adjunto->ruta . $Adjunto->nombre . '#toolbar=0" width="100%" height="170">PDF</object>'; ?>
                                        <?php $extra = ' data-type="url" data-disable-external-check="true"'; ?>
                                    <?php endif; ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 adjunto_<?php echo $Adjunto->tipo_id; ?>" id="adjunto_<?php echo $Adjunto->id; ?>">
                                    <input  type="hidden" name='adjunto_agregar[<?php echo $Adjunto->id; ?>]' value='<?php echo $Adjunto->nombre; ?>'> 
                                        <div class="thumbnail">
                                            <div class="image view view-first">
                                                <?php echo $preview; ?>
                                                <div class="mask">
                                                    <p>&nbsp;</p>
                                                    <div class="tools tools-bottom">
                                                           <a href="<?php echo $Adjunto->ruta . $Adjunto->nombre; ?>" title="Ver Adjunto" data-toggle="lightbox"<?php echo $extra; ?> data-gallery="empleo-gallery" data-title="<?php echo "$Adjunto->tipo_adjunto "; ?>"><i class="fa fa-search"></i></a>
                                                            <a href="oficina_de_empleo/adjuntos/descargar/pedir_empleo/<?php echo $Adjunto->id; ?>"  title="Descargar Adjunto"><i class="fa fa-download"></i></a>
                                                            <a href="javascript:eliminar_adjunto(<?php echo $Adjunto->id; ?>, '<?php echo $Adjunto->nombre; ?>', <?php echo $cuil; ?>)" title="Eliminar adjunto"><i class="fa fa-remove"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="caption" style="height:60px;">
                                                <p>
                                                    <b><?php echo $Adjunto->tipo_adjunto; ?></b><br>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($array_adjuntos_eliminar)): ?>
                            <?php foreach ($array_adjuntos_eliminar as $Adjunto): ?>
                                <input  name='adjunto_eliminar[<?php echo $Adjunto->id; ?>]' value='<?php echo $Adjunto->nombre; ?>'>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
</section>
<section>
                <div class="text-center envi">
                        <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                        <?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $cuil) : ''; ?>
                        <a href="oficina_de_empleo/pedir_empleo/listar" class="btn btn-default btn-sm">Cancelar</a> 
                </div>
            </section>
                <?php echo form_close(); ?>
                
            
        </article>
    </div>
</div>
      

                <script>  
               
                //***estas funciones combinan los select para poder ser leidos por el controlador***
                const LisHorarioCap = document.getElementById('horAArio_cap');
                const LisoficiosAA = document.getElementById("oficiosAA ");
                const LisgrAAdoo = document.getElementById("grAAdoo");
                const LisgrAAdo = document.getElementById("grAAdo");
                const LisCarnet = document.getElementById("movil_cAArnet");
                const LismovilidAAd = document.getElementById("movilidAAd");
                const LisDisc = document.getElementById("discapAAcidad");
                const LisEstOt = document.getElementById("estudiosOtAA ");
                const LisIntLab = document.getElementById("interes_lAAb");
                const LisDispLab = document.getElementById("disponib_lAAb");
                const LiscondicAA = document.getElementById("condicAA ");

                function combi(elemento,clase){     //para los campos agregables
                    arrayy = document.querySelectorAll("."+ clase);
                    console.log("ejecuta combi")
                    let bastardo=document.getElementById(elemento)
                    let compi=" ";
                    for (let i = 1; i < arrayy.length; i++) {
                        let eleme=arrayy[i];
                        if(!(eleme==undefined||eleme==null||eleme==""||eleme==" ")){
                           compi =compi +","+ (eleme['value']);
                        console.log(compi) 
                        }
                    };
                     bastardo.value=compi;
                };

                function combinnna(e,elemento){     //para los select multiples
                    let bueno=document.getElementById(elemento);
                    let bastardo=elemento.replace("AA ","");
                    bastardo=bastardo.replace("AA","a");
                    bastardo=document.getElementById(bastardo);
                    let valo=[...e.target.selectedOptions].map(o => o.value)
                    bastardo.value=valo;
                };

                LisHorarioCap.onchange = (e) => { combinnna(e,'horAArio_cap')};
                LisoficiosAA.onchange = (e) => { combinnna(e,'oficiosAA ')};
                LisgrAAdoo.onchange = (e) => { combinnna(e,'grAAdoo')};
                LisgrAAdo.onchange = (e) => { combinnna(e,'grAAdo')};
                LisCarnet.onchange = (e) => { combinnna(e,'movil_cAArnet')};
                LismovilidAAd.onchange = (e) => { combinnna(e,'movilidAAd')};
                LisDisc.onchange = (e) => { combinnna(e,'discapAAcidad')};
                LisEstOt.onchange = (e) => { combinnna(e,'estudiosOtAA ')};
                LisIntLab.onchange = (e) => { combinnna(e,'interes_lAAb')};
                LisDispLab.onchange = (e) => { combinnna(e,'disponib_lAAb')};
                LiscondicAA.onchange = (e) => { combinnna(e,'condicAA ')};
                $('#poliglota').change(function(event){ combi('idiomas','idioma')});
                $('#tecno').change(function(event){ combi('computacion','computacio')});
                </script>

<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>

<script>
    $(document).ready(function () {
        //Modificar el style por defecto
        $('form .change_col').find('div.col-sm-10').removeClass('col-sm-10').addClass('col-sm-9');
        $('form .change_col').find('label.col-sm-2').removeClass('col-sm-2').addClass('col-sm-3');
        $('.obs').find('div.col-sm-10').removeClass('col-sm-10').addClass('col-sm-12');
    });

    //estos metodos crean las opciones de los select
    const grAAdo_carr = ['Analista','Auxiliar','Doctorado','IngenierĂ­a','Licenciatura','Maestro','Profesorado','Tecnicatura'];
    const carreras = ['AbogacĂ­a','AcompaĂ±ante Terapeutico','AdministraciĂ³n','AdministraciĂ³n Contable','Agente Sanitario','AgronomĂ­a','Anestesista','Arquitectura','BioimĂ¡gen','BioquĂ­mica','BromatologĂ­a',
    'Ciencias PolĂ­ticas y AdministraciĂ³n PĂºblica','Cine y Video','Civil','Comercio Exterior','ComunicaciĂ³n Digital','ComunicaciĂ³n Social','Contador','CriminologĂ­a',
    'DiagnĂ³stico por ImĂ¡gen','DiseĂ±o Grafico','DiseĂ±o Industrial','DiseĂ±o y AnimaciĂ³n','EconomĂ­a','EducaciĂ³n FĂ­sica','EducaciĂ³n Inicial','EducaciĂ³n Primaria','EducaciĂ³n Secundaria','EducaciĂ³n Terciaria/Universitaria',
    'ElectromecĂ¡nica','ElectrĂ³nica','EnfermerĂ­a','EnologĂ­a','Farmacia','FonoaudiologĂ­a','GastronomĂ­a','GeologĂ­a','GestiĂ³n Ambiental','Gestor','GrafologĂ­a','GuĂ­a Turismo','Higiene y Seguridad Laboral',
    'HotelerĂ­a y Turismo','Industrial','Instrumentista QuirĂºrgico','Internacionales','Jardin Maternal','KinesiologĂ­a','Laboratorio','Letras','LogĂ­stica y Transporte','Marketing','Martillero PĂºblico, Corredor Inmobiliario',
    'Masoterapia','MecĂ¡nica y ProducciĂ³n Automatizada','Medicina','MetalmecĂ¡nico','MinerĂ­a y Gas Sustentable','NiĂ±ez, Adolescencia y Familia','NutriciĂ³n','Obstetricia','OdontologĂ­a','Otros',
    'PetrĂ³leo y Gas','Programador','Protesis Dental','PsicologĂ­a','PsicopedagogĂ­a','Publicidad','QuĂ­mica Industrial','RadiologĂ­a','Recursos Humanos','Recursos Naturales','Relaciones Humanas',
    'Seguridad PĂºblica y PenitenciarĂ­a','Sistemas','Sonido','Trabajo Social'];
    const idiomas = ['AlbanĂ©s', 'AlemĂ¡n', 'Arabe', 'Bielorruso', 'BĂºlgaro', 'CatalĂ¡n', 'Checo', 'Chino', 'Coreano', 'Croata',
    'DanĂ©s', 'Eslovaco', 'Esloveno', 'EspaĂ±ol', 'Estonio', 'Frances', 'FilandĂ©s', 'Griego', 'HĂºngaro', 'Idish', 'Indonesio',
    'Indonesio', 'InglĂ©s', 'IslandĂ©s', 'Italiano','JaponĂ©s', 'Ladino', 'LatĂ­n', 'LetĂ³n', 'Lituano', 'NeerlandĂ©s', 'Noruego',
    'Polaco', 'Portugues', 'Rumano', 'Ruso', 'Serbio', 'Sueco', 'Turco', 'Ucraniano', 'Vasco'];
    const oficiosAA = ['AdministraciĂ³n','AtenciĂ³n al PĂºblico','Bachero','Barbero','Bodega','Cadete','Cajero','CarpinterĂ­a','ChaperĂ­a y Pintura',
    'Chofer','Cosecha','Cocina','Community Manager','ConstrucciĂ³n','Control de Stock','Costura/Textil','Cuidado de Personas',
    'Delivery','Deposito','DiseĂ±o','Electricidad','EstĂ©tica','Gomero','InformĂ¡tica','JardinerĂ­a','Limpieza','Liquidador de Sueldos',
    'Mantenimiento','Marketing','Masoterapeuta','MecĂ¡nico','MetalĂºrgico','Mozo','Operario','PanaderĂ­a','PeluquerĂ­a','Pintor','Poda',
    'RecepciĂ³n','Repositor','Secretariado','Seguridad','Tapicero','Telefonista','Telemarketing','Turismo','Ventas','ViĂ±a'];
    const computacion = ['AdministraciĂ³n', 'Arquitectura', 'DiseĂ±o', 'Contable', 'ProgramaciĂ³n', 'Stock', 'Ventas','Word', 'Excel',
     'Access','Bejerman', 'Bigsys', 'AFIP', 'Tango GestiĂ³n', '3D Studio', 'Autocad', 'Corel Draw', 'Illustrator', 'Photoshop', 'Publisher', 'Otros'];
    const categorias = ['A1', 'A2', 'A3', 'B1', 'B2', 'C1', 'C2','C3', 'D1', 'D2','D3', 'D4', 'E1', 'E2', 'F', 'G1', 'G2','G3'];
    const movilidAAd = ['Auto','Bicicleta','Camioneta','Moto'];
    const discapAAcidad =['Auditiva', 'Intelectual', 'Motriz', 'Visceral','Visual', 'Otra'];
    const interese = ['AtenciĂ³n al PĂºblico', 'BarberĂ­a', 'CarpinterĂ­a', 'Club Empleo Joven', 'Costura/Textil', 'InformĂ¡tica', 'IntroducciĂ³n al Mundo del Trabajo', 'MetalĂºrgica', 'PanaderĂ­a', 'PastelerĂ­a', 'PeluquerĂ­a', 'Otro'];
    const titsecundario = ['Bachiller', 'Perito Mercantil/GestiĂ³n Administrativa', 'TĂ©cnico/aux (otras orientaciones)', 'TĂ©cnico/aux Electricista', 'TĂ©cnico/aux ElectromecĂ¡nico', 'TĂ©cnico/aux ElectrĂ³nico', 'TĂ©cnico/aux en AeronĂ¡utica', 'TĂ©cnico/aux en Automotores', 
    'TĂ©cnico/aux en AviĂ³nica', 'TĂ©cnico/aux en ComputaciĂ³n', 'TĂ©cnico/aux en EnologĂ­a', 'TĂ©cnico/aux en Industria de Alimentos', 'TĂ©cnico/aux en Industria de Proceso', 'TĂ©cnico/aux en InformĂ¡tica', 'TĂ©cnico/aux en Madera y Muebles', 
    'TĂ©cnico/aux en MecanizaciĂ³n Agropecuaria', 'TĂ©cnico/aux en MinerĂ­a', 'TĂ©cnico/aux en ProducciĂ³n Agropecuaria', 'TĂ©cnico/aux en ProgramaciĂ³n', 'TĂ©cnico/aux Maestro Mayor de Obra','TĂ©cnico/aux MecĂ¡nico', 'TĂ©cnico/aux Ă“ptico', 'TĂ©cnico/aux QuĂ­mico'];
    const horarios = ['maĂ±ana','tarde','noche','rotativo','franquero'];
    const condiciones = ['freelance','teletrabajo','viajante','cama adentro','casero'];
    const horCap = ['maĂ±ana','tarde','noche'];

    function optar(opciones,clase){
        const contenedor = document.getElementById(clase);
        const fragmento = document.createDocumentFragment();  
        for (let i = 0; i < opciones.length; i++) {
            const oficio = opciones[i];
            const item = document.createElement("OPTION");
            item.innerHTML=oficio;
            item.setAttribute("value",oficio);
            fragmento.appendChild(item);   
            contenedor.appendChild(fragmento);
        };  
    };
    optar(oficiosAA,"oficiosAA ");
    optar(idiomas,"idioma");
    optar(carreras,"grAAdoo");
    optar(grAAdo_carr,"grAAdo");
    optar(categorias,"movil_cAArnet");
    optar(computacion,"computacio");
    optar(movilidAAd,"movilidAAd");
    optar(discapAAcidad,"discapAAcidad");
    optar(titsecundario,"estudiosOtAA ");
    optar(interese,"interes_lAAb");
    optar(carreras,"interes_lAAb");
    optar(horarios,"disponib_lAAb");
    optar(condiciones,"condicAA ");
    optar(horCap,"horAArio_cap");

    const crearCampo=function(objetivo,clase,arraii,nombre){
        let classse=clase+(Math.floor(Math.random()*1000));
        let cont = document.createDocumentFragment(); 
        let conten=document.getElementById(objetivo);  
        let newDiv = document.createElement("DIV");
        newDiv.setAttribute("id","poliglota1");
        newDiv.setAttribute("class","change_col col-md-6 form-group");

        let newLeb = document.createElement("LABEL");
        newLeb.setAttribute("for",classse);
        newLeb.innerHTML=nombre;
        newLeb.setAttribute("class","control-label col-sm-3")

        let newDiv2 = document.createElement("DIV");
        newDiv2.setAttribute("class","dropdown  form-control bs3"); //este es el que caga tooo  ***  bootstrap-select

        let newSelect = document.createElement("SELECT");
        newSelect.setAttribute("id",classse);
        newSelect.setAttribute("name",classse);
        newSelect.setAttribute("class","form-select "+" "+clase+" "+clase+"ss");
        newSelect.setAttribute("title","--"+nombre+"--");
        newSelect.setAttribute("placeholder","--"+nombre+"--");
        newSelect.setAttribute("data-live-search","true");
        newSelect.setAttribute("aria-label","Default select example");
        newSelect.setAttribute("tabindex","null");

        let newOpcion = document.createElement("OPTION");
        newOpcion.setAttribute("class","bs-title-option");
        newSelect.appendChild(newOpcion);
        newDiv2.appendChild(newSelect);

        let newDiv4 = document.createElement("DIV");
        newDiv4.setAttribute("class","col-sm-9 ");
        newDiv4.appendChild(newDiv2);

        let newLeb2 = document.createElement("LABEL");
        newLeb2.setAttribute("for","idiomaN");
        newLeb2.innerHTML="Nivel";
        newLeb2.setAttribute("class","control-label col-sm-3");

        let newDiv3 = document.createElement("DIV");
        newDiv3.setAttribute("class","col-sm-9");

        let newInput= document.createElement("INPUT");
        newInput.setAttribute("name","idiomaN");
        newInput.setAttribute("type","number");
        newInput.setAttribute("maxlength","1");
        newInput.setAttribute("class","form-control N"+clase+ " "+ clase);
        newDiv3.appendChild(newInput);

        let newBoton = document.createElement("BUTTON");
        newBoton.setAttribute("id","removeRow");
        newBoton.setAttribute("class","btn btn-danger");
        newBoton.innerHTML="borrar";
        newDiv3.appendChild(newBoton);

        newDiv.appendChild(newLeb);
        newDiv.appendChild(newDiv4);
        newDiv.appendChild(newLeb2);
        newDiv.appendChild(newDiv3);

        cont.appendChild(newDiv);
        conten.appendChild(cont);

        optar(arraii,(classse));
    };
// borrar campo
    $(document).on('click', '#removeRow', function(){
        $(this).closest('#poliglota1').remove();
    });
    
    var LisIdiomAAs = document.querySelectorAll(".idioma");
    var LisComputAAs = document.querySelectorAll(".computacio");

    let botonIdiom=document.getElementById("addCampo");
    botonIdiom.addEventListener("click",functi=>crearCampo("poliglota","idioma",idiomas,"idioma"));       

    let botonCompu=document.getElementById("addCampoCompu");
    botonCompu.addEventListener("click",functi=>crearCampo("tecno","computacio",computacion,"programa"));


	$(document).ready(function() {
		domicilio_row();
		$('#cuil').inputmask({
			mask: '99-99999999-9',
			removeMaskOnSubmit: true
		});
			$('#carga_domicilio').on('changed.bs.select', function(e) {
				domicilio_row();
			});
			function domicilio_row() {
					$('#row-domicilio :input').attr("disabled", false);
					$("#localidad").selectpicker('refresh');
					$("#row-domicilio").show();
			}
	});
                 
//***esto toma los datos que no se cargan bien y reescribe los campos select, que ya son creados por ala funcion de mas arriba

    function reemplazar(elemento){              
        let bueno=document.getElementById(elemento);
        let bastardo=elemento.replace("AA ","");
        bastardo=bastardo.replace("AA","a");
        bastardo=document.getElementById(bastardo);
        let valores=(bastardo.getAttribute('value')!=null)?bastardo.getAttribute('value'):"";
        let valorArr=valores.split(", ");
        if(valores!=""){
            for (let index = 0; index <valorArr.length ; index++) {
                    let primo=bueno.firstElementChild;
            for (let inde = 0; inde < bueno.children.length; inde++) {
                    if (valorArr[index]==primo.getAttribute('value')) {
                        primo.setAttribute("selected","selected");        
                        primo=primo.nextElementSibling;     
                        break;
                    };
                    (primo==bueno.lastElementChild)?'break':(primo=primo.nextElementSibling);
                };
            };
        };
    }
window.onload = function (){
    reemplazar('horAArio_cap');
    reemplazar("oficiosAA ");
    reemplazar("grAAdoo");
    reemplazar("grAAdo");
    reemplazar("movil_cAArnet");
    reemplazar("movilidAAd");
    reemplazar("discapAAcidad");
    reemplazar("estudiosOtAA ");
    reemplazar("interes_lAAb");
    reemplazar("disponib_lAAb");
    reemplazar("condicAA ");
}

    function reemplazar2(bastard,contenedor,selector,arraySel,nombre){  //elemento es un  string
        let bastardo=document.getElementById(bastard);
        let valores=(bastardo.getAttribute('value')!=null)?bastardo.getAttribute('value'):"";
        let valorArr=valores.split(",");
        if(valores!=""){
            for (let i = 1; i < valorArr.length; i++) {
                 const element = valorArr[i];
                 if(i>1 && i%2!=0 && !(element==""||element==null||element==undefined) && (i+1)<valorArr.length){
                    crearCampo(contenedor,selector,arraySel,nombre); 
                 }
                if(i%2!=0){
let objeto=document.querySelectorAll('.'+selector+'ss');
objeto=objeto[(objeto.length)-1];
                    let primo=objeto.firstElementChild;
                    for (let inde = 0; inde < objeto.children.length; inde++) {
                    if (element==primo.getAttribute('value')) {
                        primo.setAttribute("selected","selected");        
                        break;
                    };
                        primo=primo.nextElementSibling;     
                };
                let inp=document.querySelectorAll('.N'+selector);
                inp=inp[(inp.length)-1];
                inp.setAttribute('value',(valorArr[i+1]))
                }
}
         };
    }

    reemplazar2("idiomas","poliglota","idioma",idiomas,"idioma");
    reemplazar2("computacion",'tecno',"computacio",computacion,"programa");

    function reemplazar3(nombre,nombre2){
        let res=document.getElementById(nombre);
        if(res.getAttribute('value')=="s"){
            let otro=document.getElementById(nombre2);
            res.setAttribute('value',"");
            otro.setAttribute('checked',true);
        }
    }
    reemplazar3('capacitacion','capacitacio');
    reemplazar3('busca_empleo','busca_emple');
    reemplazar3('exmuni','exmun');
    reemplazar3('famimuni','famimun');
    reemplazar3('hijos','hijo');

</script>