<!--
        /*
         * Vista ABM de Consumo Línea Fija.
         * Autor: Leandro
         * Creado: 05/09/2019
         * Modificado: 08/06/2020 (Leandro)
         */
-->
<script>
    $(document).ready(function () {
        $("#estado_todos").on("keyup change", function () {
            var estado = $("#estado_todos option:selected").val();
            $('.selectpicker').selectpicker('val', estado);
        });
    });
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
        <div class="x_panel">
            <div class="x_title">
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Consumos Líneas Fijas'; ?></h2>
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
                    <div class="col-lg-12">
                        <table class="table table-striped table-hover">
                            <?php if (!empty($consumos)): ?>
                                <tr>
                                    <th style="width: 8%;">Línea</th>
                                    <th style="width: 23%;">Domicilio</th>
                                    <th style="width: 23%;">Observaciones</th>
                                    <th style="width: 23%;">Área</th>
                                    <th style="width: 8%;">Monto</th>
                                    <th style="width: 15%;">
                                        Estado 
                                        <?php echo form_dropdown('estado', array('Impago' => 'Impago', 'Pago' => 'Pago'), '', 'class="form-control selectpicker" id="estado_todos" title="-- Cambiar todos --" data-live-search="true"'); ?>
                                    </th>
                                </tr>
                                <?php foreach ($consumos as $consumo): ?>
                                    <tr>
                                        <td style="font-weight:bold; text-align:right;"><?php echo $consumo['campo']->linea; ?></td>
                                        <td><?php echo $consumo['campo']->domicilio; ?></td>
                                        <td><?php echo $consumo['campo']->observaciones; ?></td>
                                        <td><?php echo $consumo['campo']->area; ?></td>
                                        <td style="font-weight:bold; text-align:right;"><?php echo form_input($consumo['form']); ?></td>
                                        <td style="font-weight:bold; text-align:right;"><?php echo form_dropdown($consumo['estado'], $consumo['estado_opt'], $consumo['estado_opt_selected'], 'class="form-control selectpicker"'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (!empty($lineas_periodo)): ?>
                                <tr>
                                    <th style="width: 8%;">Línea</th>
                                    <th style="width: 23%;">Domicilio</th>
                                    <th style="width: 23%;">Observaciones</th>
                                    <th style="width: 23%;">Área</th>
                                    <th style="width: 8%;">Monto</th>
                                    <th style="width: 15%;">
                                        Estado 
                                        <?php echo form_dropdown('estado', array('Impago' => 'Impago', 'Pago' => 'Pago'), '', 'class="form-control selectpicker" id="estado_todos" title="-- Cambiar todos --" data-live-search="true"'); ?>
                                    </th>
                                </tr>
                                <?php foreach ($lineas_periodo as $linea_periodo): ?>
                                    <tr>
                                        <td style="font-weight:bold; text-align:right;"><?php echo $linea_periodo['campo']->linea; ?></td>
                                        <td><?php echo $linea_periodo['campo']->domicilio; ?></td>
                                        <td><?php echo $linea_periodo['campo']->observaciones; ?></td>
                                        <td><?php echo $linea_periodo['campo']->area; ?></td>
                                        <td style="font-weight:bold; text-align:right;"><?php echo form_input($linea_periodo['form']); ?></td>
                                        <td style="font-weight:bold; text-align:right;"><?php echo form_dropdown($linea_periodo['estado'], $linea_periodo['estado_opt'], $linea_periodo['estado_opt_selected'], 'class="form-control selectpicker"'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="text-center">
                    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                    <a href="telefonia/lineas_fijas_consumos/listar/<?php echo $periodo; ?>" class="btn btn-default btn-sm">Cancelar</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>