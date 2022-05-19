<!--
 /*
        * Vista Adjunto Modal
        * Autor: Leandro
        * Creado: 29/04/2021
        * Modificado: 30/04/2021 (Leandro)
        */
-->
<?php $data_submit = array('id' => 'btn-iniciar', 'class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
<?php echo form_open(uri_string(), 'id="form-tramite"'); ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></h4>
</div>
<div class="modal-body">
    <div class="form-horizontal">
        <div class="row">
            <?php foreach ($fields as $field): ?>
                <div class="form-group">
                    <?php echo $field['label']; ?> 
                    <?php echo $field['form']; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
</div>
<?php echo form_close(); ?>
<script>
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    $(document).ready(function() {
        $('.selectpicker').selectpicker();

        $('#oficina').on('changed.bs.select', function(e) {
            buscar_procesos_oficina();
        });

        buscar_procesos_oficina();
    });
</script>