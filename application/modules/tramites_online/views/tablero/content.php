<!--
   /*
        * Vista Tablero
        * Autor: Leandro
        * Creado: 01/04/2020
        * Modificado: 29/04/2020 (Leandro)
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Tablero'; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <iframe style="width: 100%; height: 560px;" src="https://app.powerbi.com/view?r=eyJrIjoiMjlkODEyZDctNDhmNS00NGQ1LWE4MTEtNDgwZGFjMWY5N2M2IiwidCI6IjcxODFmMzdjLTlmM2EtNDFhYy1iNzlhLWIxNzM4YzgxOTE2NCJ9"></iframe>
            </div>
        </div>
    </div>
</div>