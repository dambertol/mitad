<div class="x_panel">
    <div class="x_content">
        <div class="contenido">

            <div class="clearfix"></div>
            <div class="content-fichadas">
                <img src="img/generales/logo_lujan_001.png" alt="Luján de Cuyo"/><br>
                <b style="text-decoration: underline;">MUNICIPALIDAD DE LUJÁN DE CUYO</b><br/>
            </div>
            <br>

            <div class="content-left">
                <span class="text-uppercase" style="text-decoration: underline;">Formulario de datos para notificaciones</span>
                <br/>
            </div>

            <div class="content-left">
                <b>Cedula Nº:</b> <?php if (!empty($cedula->n_cedula)) echo $cedula->n_cedula; ?><br/>
                <b>Fecha:</b> <?php if (!empty($cedula->fecha_creacion)) echo date('d/m/Y   H:i', strtotime($cedula->fecha_creacion)); ?> hs<br/>
                <b>Tipo:</b> <?php if (!empty($tipo_cedula->descripcion)) echo $tipo_cedula->descripcion; ?><br/>
                <b>Numero:</b> <?php if (!empty($cedula->n_documento)) echo $cedula->n_documento . "/" . $cedula->anio; ?><br/>
            </div>

            <div class="content-left">
                <span class="text-uppercase" style="text-decoration: underline;">Datos de persona a notificar</span>
                <br/>
            </div>


            <div class="content-left">
                <b>Destinatario: </b> <span
                        class="text-uppercase"><?php echo($destinatario->nombre . " " . $destinatario->apellido); ?></span><br/>
                <b>Tipo y Numero Doc: </b>
                <?php
                switch ($destinatario->tipo_identificacion) {
                    case 1:
                        $tipo_identificacion = "DNI";
                        break;
                    case 2:
                        $tipo_identificacion = "L.C.";
                        break;
                    case 3:
                        $tipo_identificacion = "L.E.";
                        break;
                    case 4:
                        $tipo_identificacion = "L.F.";
                        break;
                    case 5:
                        $tipo_identificacion = "PASAPORTE";
                        break;
                    case 6:
                        $tipo_identificacion = "C.U.I.T.";
                        break;
                    case 7:
                        $tipo_identificacion = "EXTRANJEROS";
                        break;
                    case 9:
                        $tipo_identificacion = "SIN DOCUMENTO";
                        break;
                    case 10:
                        $tipo_identificacion = "C.I.";
                        break;
                    case 11:
                        $tipo_identificacion = "C.E.";
                        break;
                    case 12:
                        $tipo_identificacion = "C.F.";
                        break;
                    case 13:
                        $tipo_identificacion = "C.I.F.";
                        break;
                    case 14:
                        $tipo_identificacion = "C.U.I.L.";
                        break;
                    case 15:
                        $tipo_identificacion = "EXTRANJEROS NO RESIDENTES";
                        break;
                    case 20:
                        $tipo_identificacion = "ORGANISMOS OFICIALES";
                        break;
                    case 30:
                        $tipo_identificacion = "VERIFICADORES ESPECIALES";
                        break;
                }
                ?>
                <?php echo $tipo_identificacion . " " . $destinatario->n_identificacion; ?>
                <br/>
                <b>Domicilio: </b> <span
                        class="text-uppercase"><?php echo($domicilio->direccion . " " . $domicilio->num . ", " . $domicilio->localidad); ?></span>
                <br/>
                <?php if (!empty($domicilio->alternativo)): ?>
                    <b>Domicilio a Notificar: </b> <span
                            class="text-uppercase"><?php echo($domicilio->alternativo); ?></span>
                    <br/>
                <?php endif; ?>
            </div>
            <br/>
            <br/>
            <div class="content-justify">
                Se adjunta pieza administrativa y sus relacionados.
            </div>


            <div class="content-justify">
                <p>Atentamente.-<br/></p>
            </div>
            <br/>
            <br/>

            <div class="pull-right" style="width: 200px; padding-right: 50px">
                <div class="text-center">
                    <p>
                        <span class="text-uppercase"><?php echo($oficina->nombre); ?></span><br />
                        P/ Municipalidad de<br/>
                        Lujan de Cuyo<br/>
                    </p>
                </div>
            </div>

            <div class="clearfix"></div>
            <br/>
            <br/>


            <br/>
            <br/>
            <br/>
            <div class="well">
                <b>Observaciones:</b><br>
                <small>
                    <?php echo(empty($cedula->observaciones) ? "Sin datos" : $cedula->observaciones); ?>
                </small>
            </div>


            <small>Notificación generada el: <?php echo date_format(new DateTime(), 'd/m/Y h:i:s'); ?></small>
        </div>


        <div class="no-print">
            <div class="row">
                <div class="col-xs-12">
                    <div class="ln_solid"></div>
                    <div class="text-center">
                        <a href="notificaciones/cedulas/imprimir/<?php echo $cedula->id; ?>" class="btn btn-primary btn-sm"
                           target="_blank">Descargar</a>
                        <a href="notificaciones/cedulas/ver/<?php echo $cedula->id; ?>" class="btn btn-info btn-sm">Volver</a>
                        <a href="notificaciones/cedulas/listar" class="btn btn-default btn-sm">Volver al listado</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
