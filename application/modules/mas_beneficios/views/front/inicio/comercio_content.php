<!--
        /*
         * Vista detalle de Comercio.
         * Autor: Leandro
         * Creado: 13/09/2018
         * Modificado: 27/04/2020 (Leandro)
         */
-->
<div id="main">
    <section id="one">
        <div class="inner">
            <?php if (!empty($comercio)): ?>
                <header class="major">
                    <h4><?php echo $comercio->nombre; ?></h4>
                </header>
                <div class="row gtr-uniform">
                    <div class="col-12">
                        <h5>Categoría</h5> <?php echo $comercio->categoria; ?>
                    </div>
                    <?php if (!empty($comercio->comentarios)): ?>
                        <div class="col-12">
                            <h5>Comentarios</h5> <?php echo $comercio->comentarios; ?>
                        </div>
                    <?php endif; ?>
                    <div class="col-12">
                        <h5>Envío a domicilio</h5> <?php echo $comercio->envio_domicilio; ?>
                    </div>
                    <div class="col-12">
                        <h5>Dirección</h5> <?php echo "$comercio->calle $comercio->altura - $comercio->localidad"; ?>
                    </div>
                    <div class="col-12">
                        <h5>Teléfono</h5> <?php echo $comercio->telefono; ?>
                    </div>
                    <div class="col-12">
                        <h5>Email</h5> <?php echo $comercio->email; ?>
                    </div>
                    <div class="col-3" style="text-align: center;">
                        <?php if (!empty($comercio->twitter)): ?>
                            <a href="https://<?php echo $comercio->twitter; ?>" target="_blank" title="Twitter" class="icon alt fa fa-twitter"></a>
                        <?php else: ?>
                            <div class="icon alt disabled fa fa-twitter"></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-3" style="text-align: center;">
                            <?php if (!empty($comercio->facebook)): ?>
                                <a href="https://<?php echo $comercio->facebook; ?>" target="_blank" title="Facebook" class="icon alt fa fa-facebook"></a>
                            <?php else: ?>
                                <div class="icon alt disabled fa fa-facebook"></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-3" style="text-align: center;">
                                <?php if (!empty($comercio->instagram)): ?>
                                    <a href="https://<?php echo $comercio->instagram; ?>" target="_blank" title="Instagram" class="icon alt fa fa-instagram"></a>
                                <?php else: ?>
                                    <div class="icon alt disabled fa fa-instagram"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-3" style="text-align: center;">
                                    <?php if (!empty($comercio->web)): ?>
                                        <a href="https://<?php echo $comercio->web; ?>" target="_blank" title="Web" class="icon alt fa fa-globe"></a>
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