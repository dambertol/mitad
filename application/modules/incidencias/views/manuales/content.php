<!--
   /*
        * Vista Escritorio
        * Autor: Leandro
        * Creado: 28/04/2020
        * Modificado: 28/04/2020 (Leandro)	
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Manuales'; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="height:90vh;">
                <?php if (!empty($municipal)) : ?>
                    <object data="<?php echo $municipal; ?>#zoom=100&pagemode=thumbs" type="application/pdf" width="100%" height="100%">
                        alt : <a href="<?php echo $municipal; ?>">Ver Manual de Usuario</a>
                    </object>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>