<!--
   /*
    * Vista Parte Diario de Personal
    * Autor: Leandro
    * Creado: 11/10/2017
    * Modificado: 03/11/2020 (Leandro)
    */
-->
<script>
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    $(document).ready(function () {
        <?php if (!empty($secretaria_sel)) : ?>
            actualizar_oficinas();
        <?php endif; ?>
        $('#secretaria').on('changed.bs.select', function (e) {
            actualizar_oficinas();
        });
        var hoy = moment();
        $("#btn_exportar").click(function () {
            $("#form_parte").data('submitted', false);
            $("#form_parte").submit();
        });
        $('#desde').datetimepicker({
            locale: 'es',
            format: 'L',
            useCurrent: false,
            showClear: true,
            showTodayButton: true,
            showClose: true,
            maxDate: hoy.endOf('day')
        });
        $('#hasta').datetimepicker({
            locale: 'es',
            format: 'L',
            useCurrent: false,
            showClear: true,
            showTodayButton: true,
            showClose: true,
            maxDate: hoy.endOf('day')
        });
        $("#desde").on("dp.change", function (e) {
            $('#hasta').data("DateTimePicker").minDate(e.date.startOf('day'));
            var hasta = moment(e.date);
            hasta.add(2, 'M').subtract(1, 'd');
            if (hoy.isBefore(hasta)) {
                $('#hasta').data("DateTimePicker").maxDate(hoy.endOf('day'));
            } else {
                $('#hasta').data("DateTimePicker").maxDate(hasta.endOf('day'));
            }

        });
        $("#hasta").on("dp.change", function (e) {
            $('#desde').data("DateTimePicker").maxDate(e.date.endOf('day'));
            var desde = moment(e.date);
            desde.subtract(2, 'M').add(1, 'd');
            $('#desde').data("DateTimePicker").minDate(desde.startOf('day'));
        });
        $('#fecha').datetimepicker({
            locale: 'es',
            format: 'L',
            useCurrent: false,
            showClear: true,
            showTodayButton: true,
            showClose: true,
            maxDate: 'now'
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Diario'; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php $data_submit = array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
                <?php echo form_open(uri_string(), 'id="form_parte" class="form-horizontal"'); ?>
                <div class="row">
                    <?php foreach ($fields as $field): ?>
                        <div class="form-group">
                            <?php echo $field['label']; ?> 
                            <?php echo $field['form']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="ln_solid"></div>
                <div class="text-center">
                    <?php echo form_submit($data_submit, $txt_btn, 'id="btn_exportar"'); ?>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>