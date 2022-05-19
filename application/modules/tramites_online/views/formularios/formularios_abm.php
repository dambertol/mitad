<!--
        /*
         * Vista ABM de Formulario.
         * Autor: Leandro
         * Creado: 22/04/2021
         * Modificado: 08/08/2021 (Leandro)
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Formularios'; ?></h2>
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
                    <?php foreach ($fields as $field): ?>
                        <div class="form-group">
                            <?php echo $field['label']; ?> 
                            <?php echo $field['form']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <section class="panel">
                            <div class="x_title">
                                <h2>Campos</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <table class="table table-bordered table-condensed table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width:12%;">Nombre</th>
                                                <th style="width: 6%;">Sólo lectura</th>
                                                <th style="width: 8%;">Valor Defecto</th>
                                                <th style="width: 5%;">Posición</th>
                                                <th style="width: 6%;">Tipo</th>
                                                <th style="width:12%;">Opciones</th>
                                                <th style="width:12%;">Etiqueta</th>
                                                <th style="width: 6%;">Validación</th>
                                                <th style="width: 5%;">Editable</th>
                                                <th style="width: 5%;">Imprimibile</th>
                                                <th style="width: 5%;">Obligatorio</th>
                                                <th style="width:10%;">Función</th>
                                                <th>Ayuda</th>
                                                <th style="width:50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $cant_rows_nro = 0; ?>
                                            <?php foreach ($fields_detalle_array as $Fields_detalle): ?>
                                                <?php $cant_rows_nro++; ?>
                                                <tr id="detalle_<?php echo $cant_rows_nro; ?>">
                                                    <?php foreach ($Fields_detalle as $Field): ?>
                                                        <?php if (isset($Field['type']) && $Field['type'] == 'hidden'): ?>
                                                            <?php echo $Field['form']; ?>
                                                        <?php else: ?>
                                                            <td><?php echo $Field['form']; ?></td>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                    <td>
                                                        <?php if ($txt_btn === 'Agregar' || $txt_btn === 'Editar'): ?>
                                                            <button name="quitar_detalle_<?php echo $cant_rows_nro; ?>" type="button" id="quitar_detalle_<?php echo $cant_rows_nro; ?>" onclick="quitar_detalle(this, null)" class="btn btn-danger btn-sm" title="Quitar Detalle">
                                                                <i class="fa fa-remove"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php if ($txt_btn === 'Agregar' || $txt_btn === 'Editar'): ?>
                                        <a href="javascript:void(0);" onclick="insertar_detalle()" title="Agregar Campo" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Campo</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="text-center">
                    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                    <?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $formulario->id) : ''; ?>
                    <a href="tramites_online/formularios/listar" class="btn btn-default btn-sm">Cancelar</a>
                </div>
                <?php echo form_input($cant_rows); ?>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>
<script>
    var base_tr;
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    $(document).ready(function() {
        base_tr = $('#detalle_1').clone();
    });

    function insertar_detalle() {
        var cant_rows = parseInt($('#cant_rows').val());
        var new_tr = base_tr.clone().attr('id', 'detalle_' + (cant_rows + 1));
        $('input,select,button', new_tr).each(function() {
            if ($(this).attr('id')) {
                var id = $(this).attr('id');
                var name = $(this).attr('name');
                $(this).attr('id', id.replace(/([0-9]+)/g, (cant_rows + 1)));
                $(this).attr('name', name.replace(/([0-9]+)/g, (cant_rows + 1)));
            }
        });
        $('input', new_tr).each(function() {
            $(this).val('');
        });
        $('.bootstrap-select', new_tr).each(function() {
            $(this).replaceWith(function() {
                return $('select', this);
            });
        });
        $('select', new_tr).each(function() {
            $(this).find('.bs-title-option').remove();
            $(this).selectpicker();
            $(this).val([]).val('default').selectpicker("refresh");
        });
        $('#detalle_' + cant_rows).after(new_tr);
        cant_rows++;
        $('#cant_rows').val(cant_rows);
        aplicar_formatos();
    }

    function quitar_detalle(btn, id) {
        var cant_rows = parseInt($('#cant_rows').val());
        if (typeof id !== 'undefined' && id !== null) {
            var nro_id = parseInt(id);
        } else {
            var id = $(btn).attr('id');
            var regExp = /([0-9]+)/g;
            var matches = regExp.exec(id);
            var nro_id = parseInt(matches[1]);
        }
        if (cant_rows <= 1) {
            Swal.fire({
                type: 'error',
                title: 'Error.',
                text: 'Debe ingresar al menos un detalle',
                buttonsStyling: false,
                confirmButtonClass: 'btn btn-primary',
                confirmButtonText: 'Aceptar'
            })
        } else {
            $('#detalle_' + nro_id).remove();
            for (var i = (nro_id + 1); i <= cant_rows; i++) {
                var tr = $('#detalle_' + i);
                $('input,select,button', tr).each(function() {
                    if ($(this).attr('id')) {
                        var id = $(this).attr('id');
                        var name = $(this).attr('name');
                        $(this).attr('id', id.replace(/([0-9]+)/g, (i - 1)));
                        $(this).attr('name', name.replace(/([0-9]+)/g, (i - 1)));
                    }
                });
                tr.attr('id', 'detalle_' + (i - 1));
            }
            cant_rows--;
            $('#cant_rows').val(cant_rows);
        }
    }
</script>