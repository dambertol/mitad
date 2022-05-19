<div class="x_panel">
    <div class="x_content">
        <div class="contenido">
            <div class="clearfix"></div>
            <div class="content-fichadas">
                <img src="img/generales/logo_lujan_001.png" alt="Luján de Cuyo"/><br>
                <b>MUNICIPALIDAD DE LUJÁN DE CUYO</b><br/>
            </div>

            <div class="content-right">
                <b>Cedula Nº:</b> <?php if (!empty($cedula->n_cedula)) echo $cedula->n_cedula; ?><br/>
                <b>Fecha:</b> <?php if (!empty($cedula->fecha_creacion)) echo $cedula->fecha_creacion; ?><br/>
            </div>

            <div class="content-left">
                <b>Destinatario: </b> <?php echo strtoupper($destinatario->nombre . " " . $destinatario->apellido); ?><br/>
                <b>Direccion: </b> <?php echo strtoupper($domicilio->direccion . " " . $domicilio->num . ", " . $domicilio->localidad); ?>
                <br/>
                <b>Direccion Alternativa: </b> <?php echo strtoupper($domicilio->alternativo); ?><br/>
            </div>
            <br/>
            <br/>
            <div class="content-justify">
                <?php if (!empty($cedula->texto)) echo $cedula->texto; ?><br/>
            </div>

            <div class="content-justify">
                <p>Atentamente.-<br/></p>
            </div>
            <br/>
            <br/>

            <div class="pull-right" style="width: 200px; padding-right: 50px">
                <div class="text-center">
                    <b>
                        Verificaciones - Notificaciones<br/>
                        Municipalidad de Lujan de Cuyo<br/>
                    </b>
                </div>
            </div>

            <div class="clearfix"></div>
            <br/>
            <br/>

            <small>Notificación generada el: <?php echo date_format(new DateTime(), 'd/m/Y h:i:s'); ?></small>
        </div>


        <div class="no-print">
            <div class="row">
                <div class="col-xs-12">
                    <div class="ln_solid"></div>
                    <div class="text-center">
                        <a href="notificaciones/cedulas/imprimir/<?php echo $cedula->id; ?>" class="btn btn-primary btn-sm" target="_blank">Descargar</a>
                        <a href="notificaciones/cedulas/ver/<?php echo $cedula->id; ?>" class="btn btn-info btn-sm">Volver</a>
                        <a href="notificaciones/cedulas/listar" class="btn btn-default btn-sm">Volver al listado</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
