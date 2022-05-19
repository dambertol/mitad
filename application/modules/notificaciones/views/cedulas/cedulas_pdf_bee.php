<style>
    .contenido *{
        max-width: 800px;
    }
    .cuerpo-notificacion {
        text-align: justify;
        text-indent: 30%;
    }
</style>

<div class="container-fluid contenido">
    <div class="row">
        <div class="col-md-12 text-center">
            <img alt="Luján de Cuyo" src="img/generales/logo_lujan_001.png" />
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
        </div>
        <div class="col-md-4">
            <b>Cedula Nº:</b> <?php if (!empty($cedula->n_cedula)) echo $cedula->n_cedula; ?><br/>
            <b>Fecha:</b> <?php if (!empty($cedula->fecha_creacion)) echo $cedula->fecha_creacion; ?><br/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <b>Destinatario: </b><br/>
            <b>Direccion:</b> <?php ?><br/>
            <b>Direccion Alternativa:</b><br/>
        </div>
        <div class="col-md-4">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p class="cuerpo-notificacion"><?php if (!empty($cedula->texto)) echo $cedula->texto; ?><br/></p>
            <p class="cuerpo-notificacion">Atentamente.-<br/></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
        </div>
        <div class="col-md-4 text-center">
            <b>
                Encargado<br/>
                Verificaciones - Notificaciones<br/>
                Municipalidad de Lujan de Cuyo<br/>
            </b>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            Notificación generada el: <?php echo date_format(new DateTime(), 'd/m/Y h:i:s'); ?><br/>
        </div>
    </div>
</div>

<footer>
    <div class="no-print">
        <a href="notificaciones/cedulas/imprimir/<?php echo $cedula->id; ?>" class="btn btn-primary btn-sm ">Descargar</a>
    </div>
</footer>