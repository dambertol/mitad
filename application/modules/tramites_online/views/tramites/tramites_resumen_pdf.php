<!DOCTYPE html>
<html>
<head>

</head>
<body>

<!--<table style="width:100%; font-family:serif; margin:5px 0px 25px 0px; font-size:14px;">-->
<!--    <tr>-->
<!--        <td style="width:25%; text-align:left;">-->
<!--            <img src="img/generales/reportes/logo_lujan.png" alt="Luján de Cuyo" style="width:10%;"/>-->
<!--        </td>-->
<!--        <td style="width:50%; font-size:22px; font-weight:bold; text-align:center; vertical-align:middle;">-->
<!--            --><?php //echo strtoupper($oficina->nombre); ?>
<!--        </td>-->
<!--        <td style="width:25%; text-align:right;">-->
<!--            <img src="img/generales/reportes/logo_escudo.png" alt="Luján de Cuyo" style="width:10%;"/>-->
<!--        </td>-->
<!--    <tr>-->
<!--        <td colspan="3" style="font-size:16px; text-align:center;">-->
<!--            TRAMITE ON-LINE N°:--><?php //echo $tramite->id; ?>
<!--        </td>-->
<!--    </tr>-->
<!--</table>-->


<table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:10px 0px 25px 0px; font-size:14px;">
    <thead>
    <tr>
        <th style="border:1px solid; background-color:#CCC;" colspan="2">TRAMITE</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="border:1px solid; padding:2px; width:40%; font-weight:bold;">Tipo</td>
        <td style="border:1px solid; padding:2px;"><?php echo $tramite->proceso; ?></td>
    </tr>
    <tr>
        <td style="border:1px solid; padding:2px; font-weight:bold;">INICIO</td>
        <td style="border:1px solid; padding:2px;"><?php echo date_format(new DateTime($tramite->fecha_inicio), 'd/m/Y'); ?></td>
    </tr>
    <tr>
        <td style="border:1px solid; padding:2px; font-weight:bold;">FIN</td>
        <td style="border:1px solid; padding:2px;"><?php echo empty($tramite->fecha_fin) ? '' : date_format(new DateTime($tramite->fecha_fin), 'd/m/Y'); ?></td>
    </tr>

    </tbody>
</table>

<table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:5px 0px 25px 0px; font-size:14px;">
    <thead>
    <tr>
        <th style="border:1px solid; background-color:#CCC;" colspan="2">INICIADOR</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="border:1px solid; padding:2px; width:40%; font-weight:bold;">CUIL</td>
        <td style="border:1px solid; padding:2px;"><?php echo substr($iniciador->cuil, 0, 2) . "-" . substr($iniciador->cuil, 2, 8) . "-" . substr($iniciador->cuil, 10); ?></td>
    </tr>
    <tr>
        <td style="border:1px solid; padding:2px; font-weight:bold;">APELLIDO y NOMBRE</td>
        <td style="border:1px solid; padding:2px;"><?php echo "$iniciador->apellido, $iniciador->nombre"; ?></td>
    </tr>
    <tr>
        <td style="border:1px solid; padding:2px; font-weight:bold;">TELÉFONO</td>
        <td style="border:1px solid; padding:2px;"><?php echo $iniciador->telefono; ?></td>
    </tr>
    <tr>
        <td style="border:1px solid; padding:2px; font-weight:bold;">CELULAR</td>
        <td style="border:1px solid; padding:2px;"><?php echo $iniciador->celular; ?></td>
    </tr>
    <tr>
        <td style="border:1px solid; padding:2px; font-weight:bold;">EMAIL</td>
        <td style="border:1px solid; padding:2px;"><?php echo $iniciador->email; ?></td>
    </tr>
    </tbody>
</table>

<?php if (isset($padrones) && $padrones): ?>
    <?php foreach ($padrones as $padron): ?>
        <table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:5px 0px 25px 0px; font-size:14px;">
            <thead>
            <tr>
                <th style="border:1px solid; background-color:#CCC;" colspan="2">INMUEBLE</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="border:1px solid; padding:2px; width:40%; font-weight:bold;">PADRÓN MUNICIPAL</td>
                <td style="border:1px solid; padding:2px;"><?php echo $padron->padron; ?></td>
            </tr>
            <tr>
                <td style="border:1px solid; padding:2px; font-weight:bold;">NOMENCLATURA</td>
                <td style="border:1px solid; padding:2px;"><?php echo $padron->nomenclatura; ?></td>
            </tr>
            <tr>
                <td style="border:1px solid; padding:2px; font-weight:bold;">DOCUMENTO TITULAR</td>
                <td style="border:1px solid; padding:2px;"><?php echo $padron->tit_dni; ?></td>
            </tr>
            <tr>
                <td style="border:1px solid; padding:2px; font-weight:bold;">APELLIDO TITULAR</td>
                <td style="border:1px solid; padding:2px;"><?php echo $padron->tit_apellido; ?></td>
            </tr>
            <tr>
                <td style="border:1px solid; padding:2px; font-weight:bold;">NOMBRE TITULAR</td>
                <td style="border:1px solid; padding:2px;"><?php echo $padron->tit_nombre; ?></td>
            </tr>

            <!--            <tr>-->
            <!--                <td style="border:1px solid; padding:2px; font-weight:bold;">SUPERFICIE TÍTULO</td>-->
            <!--                <td style="border:1px solid; padding:2px;">-->
            <?php //echo number_format($padron->sup_titulo, 2, ',', '.'); ?><!--</td>-->
            <!--            </tr>-->
            <!--            <tr>-->
            <!--                <td style="border:1px solid; padding:2px; font-weight:bold;">SUPERFICIE MENSURA</td>-->
            <!--                <td style="border:1px solid; padding:2px;">-->
            <?php //echo number_format($padron->sup_mensura, 2, ',', '.'); ?><!--</td>-->
            <!--            </tr>-->
            <!--            <tr>-->
            <!--                <td style="border:1px solid; padding:2px; font-weight:bold;">SUPERFICIE AFECTADA</td>-->
            <!--                <td style="border:1px solid; padding:2px;">-->
            <?php //echo number_format($padron->sup_afectada, 2, ',', '.'); ?><!--</td>-->
            <!--            </tr>-->
            <!--            <tr>-->
            <!--                <td style="border:1px solid; padding:2px; font-weight:bold;">SUPERFICIE CUBIERTA</td>-->
            <!--                <td style="border:1px solid; padding:2px;">-->
            <?php //echo number_format($padron->sup_cubierta, 2, ',', '.'); ?><!--</td>-->
            <!--            </tr>-->

            </tbody>
        </table>
    <?php endforeach; ?>
<?php endif ?>

<?php foreach ($formularios as $formulario): ?>
    <table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:5px 0px 25px 0px; font-size:14px;">
        <thead>
        <tr>
            <th style="border:1px solid; background-color:#CCC;"
                colspan="2"><?php echo strtoupper($formulario['formulario_nombre']) ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php $index = 1; ?>
        <?php foreach ($formulario as $repeticion): ?>
            <?php if (is_array($repeticion)): ?>
                <?php if ((count($formulario)) > 2): ?>
                    <tr>
                        <td style="text-align: center"
                            colspan="2"><?php echo strtoupper($formulario['formulario_nombre']) . ' ' . $index ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($repeticion as $campo): ?>
                    <tr>
                        <td style="border:1px solid; padding:2px; width:40%; font-weight:bold;"><?php echo strtoupper($campo->label); ?></td>
                        <td style="border:1px solid; padding:2px;"><?php echo nl2br($campo->value); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php $index++; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endforeach; ?>

<br>
<div style="padding:0;">
    <div style="text-align:right; vertical-align:bottom;">
        <barcode code="https://tad.lujandecuyo.gob.ar/tramites_online/tramites/ver/<?php echo $tramite->id ?>/" type="QR" class="barcode"
                 size="1.3"
                 error="M" disableborder="1"/>
        <br>
        <a href="https://tad.lujandecuyo.gob.ar/tramites_online/tramites/ver/<?php echo $tramite->id ?>" target="_blank">Ver Datos
            ON-LINE</a>
    </div>
</div>
</body>
</html>
<?php // exit;?>