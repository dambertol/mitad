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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'dar_empleo'; ?></h2>
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
                    
<!--  ********************************aca ocurre la magia **************** ************************-->

                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['cuil']['label']; ?> 
                                <?php echo $fields['cuil']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['nombre']['label']; ?> 
                                <?php echo $fields['nombre']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['telefono']['label']; ?> 
                                <?php echo $fields['telefono']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['email']['label']; ?> 
                                <?php echo $fields['email']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['genero']['label']; ?> 
                                <?php echo $fields['genero']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['fecha_nac']['label']; ?> 
                                <?php echo $fields['fecha_nac']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['domicilio']['label']; ?> 
                                <?php echo $fields['domicilio']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['distrito']['label']; ?> 
                                <?php echo $fields['distrito']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['otro_tel']['label']; ?> 
                                <?php echo $fields['otro_tel']['form']; ?> 
                    </div>
                    <div class="border-group form-gruop">
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['capacitacion']['label']; ?> 
                                <?php echo $fields['capacitacion']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['horario_cap']['label']; ?> 
                                <?php echo $fields['horario_cap']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['intereses_cap']['label']; ?> 
                                <?php echo $fields['intereses_cap']['form']; ?> 
                    </div>
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['busca_empleo']['label']; ?> 
                                <?php echo $fields['busca_empleo']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['interes_lab']['label']; ?> 
                                <?php echo $fields['interes_lab']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['disponib_lab']['label']; ?> 
                                <?php echo $fields['disponib_lab']['form']; ?> 
                    </div>
                    <div class="border-group form-gruop">
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['movil_tipo']['label']; ?> 
                                <?php echo $fields['movil_tipo']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['movil_carnet']['label']; ?> 
                                <?php echo $fields['movil_carnet']['form']; ?> 
                    </div>
                    </div>
                    <div class="border-group">
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['discapacidad']['label']; ?> 
                                <?php echo $fields['discapacidad']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['cud']['label']; ?> 
                                <?php echo $fields['cud']['form']; ?> 
                    </div>
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['nivel']['label']; ?> 
                                <?php echo $fields['nivel']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['estudiosOt']['label']; ?> 
                                <?php echo $fields['estudiosOt']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['grado']['label']; ?> 
                                <?php echo $fields['grado']['form']; ?> 
                    </div>
                    <div class="border-group form-gruop">
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['idiomas']['label']; ?> 
                                <?php echo $fields['idiomas']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['idiomas_niv']['label']; ?> 
                                <?php echo $fields['idiomas_niv']['form']; ?> 
                    </div>                    
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['computacion']['label']; ?> 
                                <?php echo $fields['computacion']['form']; ?> 
                    </div>                    
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['compu_niv']['label']; ?> 
                                <?php echo $fields['compu_niv']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['cursos']['label']; ?> 
                                <?php echo $fields['cursos']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['experiencia']['label']; ?> 
                                <?php echo $fields['experiencia']['form']; ?> 
                    </div>
                    
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['freelance']['label']; ?> 
                                <?php echo $fields['freelance']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['teletrabajo']['label']; ?> 
                                <?php echo $fields['teletrabajo']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['viajante']['label']; ?> 
                                <?php echo $fields['viajante']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['cama_adentro']['label']; ?> 
                                <?php echo $fields['cama_adentro']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['casero']['label']; ?> 
                                <?php echo $fields['casero']['form']; ?> 
                    </div>
                    <div class="group-border ">
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['aclaraciones']['label']; ?> 
                                <?php echo $fields['aclaraciones']['form']; ?> 
                    </div>
                    </div>

                </div>
                <div class="ln_solid"></div>
                <div class="text-center">
                    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                    <?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $empleo->id) : ''; ?>
                    <a href="oficina_de_empleo/dar_empleo/listar" class="btn btn-default btn-sm">Cancelar</a> 
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>

<script>
    $(document).ready(function () {
        //Modificar el style por defecto
        $('form .change_col').find('div.col-sm-10').removeClass('col-sm-10').addClass('col-sm-9');
        $('form .change_col').find('label.col-sm-2').removeClass('col-sm-2').addClass('col-sm-3');
        $('.obs').find('div.col-sm-10').removeClass('col-sm-10').addClass('col-sm-12');
    });
</script>