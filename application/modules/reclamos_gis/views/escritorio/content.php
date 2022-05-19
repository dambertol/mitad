<!--
   /*
        * Vista Escritorio
        * Autor: Leandro
        * Creado: 10/10/2018
        * Modificado: 25/03/2019 (Leandro)	
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Escritorio'; ?><small>Versión 1.0.0</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <?php if (!empty($accesos_esc)) : ?>
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