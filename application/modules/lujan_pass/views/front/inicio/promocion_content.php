<!--
        /*
         * Vista detalle de Promoción.
         * Autor: Leandro
         * Creado: 21/07/2020
         * Modificado: 21/07/2020 (Leandro)
         */
-->
<div id="main">
    <section id="one">
        <div class="inner">
            <?php if (!empty($promocion)): ?>
                <header class="major">
                    <h4><?php echo $promocion->comercio; ?></h4>
                </header>
                <div class="row gtr-uniform">
                    <div class="col-12">
                        <h5>Categoría</h5> <?php echo $promocion->campania; ?>
                    </div>
                    <?php if (!empty($promocion->descripcion)): ?>
                        <div class="col-12">
                            <h5>Descripción</h5> <?php echo $promocion->descripcion; ?>
                        </div>
                    <?php endif; ?>
                    <div class="col-12">
                        <h5>Dirección</h5> <?php echo "$promocion->calle $promocion->altura - $promocion->localidad"; ?>
                    </div>
                    <div class="col-12">
                        <h5>Teléfono</h5> <?php echo $promocion->telefono; ?>
                    </div>
                    <div class="col-12">
                        <h5>Email</h5> <?php echo $promocion->email; ?>
                    </div>
                    <div class="col-3" style="text-align: center;">
                        <?php if (!empty($promocion->twitter)): ?>
                            <a href="https://<?php echo $promocion->twitter; ?>" target="_blank" title="Twitter" class="icon alt fa fa-twitter"></a>
                        <?php else: ?>
                            <div class="icon alt disabled fa fa-twitter"></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-3" style="text-align: center;">
                        <?php if (!empty($promocion->facebook)): ?>
                            <a href="https://<?php echo $promocion->facebook; ?>" target="_blank" title="Facebook" class="icon alt fa fa-facebook"></a>
                        <?php else: ?>
                            <div class="icon alt disabled fa fa-facebook"></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-3" style="text-align: center;">
                        <?php if (!empty($promocion->instagram)): ?>
                            <a href="https://<?php echo $promocion->instagram; ?>" target="_blank" title="Instagram" class="icon alt fa fa-instagram"></a>
                        <?php else: ?>
                            <div class="icon alt disabled fa fa-instagram"></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-3" style="text-align: center;">
                        <?php if (!empty($promocion->web)): ?>
                            <a href="https://<?php echo $promocion->web; ?>" target="_blank" title="Web" class="icon alt fa fa-globe"></a>
                        <?php else: ?>
                            <div class="icon alt disabled fa fa-globe"></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <h3 style="text-align: center;"><?php echo $error; ?></h3>
            <?php endif; ?>
        </div>
    </section>
</div>