<!--
        /*
         * Vista ver Manuales.
         * Autor: Leandro
         * Creado: 02/06/2020
         * Modificado: 02/06/2020 (Leandro)
         */
-->
<script>
    var manuales_table;
    function complete_manuales_table() {
        agregar_filtros('manuales_table', manuales_table, 3);
    }
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Manuales'; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-md-12">
                        <?php if (!empty($manuales)): ?>
                            <?php $categoria = 0; ?>
                            <?php foreach ($manuales as $manual): ?>
                                <?php if ($categoria !== $manual->categoria_id): ?>
                                    <?php if ($categoria !== 0): ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php $categoria = $manual->categoria_id; ?>
                <div class="col-md-6 col-sm-12">
                    <div class="pricing">
                        <div class="title">
                            <h1><?= $manual->categoria; ?></h1>
                        </div>
                        <div class="x_content">
                            <div class="">
                                <div class="pricing_features">
                                    <ul class="list-unstyled text-left">
                                        <li><i class="fa fa-check text-success"></i> <a href="<?= $manual->link; ?>" target="_blank"><?= $manual->nombre; ?></a></li>
                                    <?php else: ?>
                                        <li><i class="fa fa-check text-success"></i> <a href="<?= $manual->link; ?>" target="_blank"><?= $manual->nombre; ?></a></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</div>