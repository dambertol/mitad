<!DOCTYPE html>
<html>
<head>

</head>
<body>

<table style="width:100%; font-family:serif; margin:5px 0px 10px 0px; font-size:12px;">
    <tr>
        <td style="width:25%; text-align:left;">
            <img src="img/generales/reportes/logo_lujan.png" alt="Luján de Cuyo" style="width:10%;"/>
        </td>
        <td style="width:50%; font-size:22px; font-weight:bold; text-align:center; vertical-align:middle;">
            <?php echo strtoupper($oficina->nombre); ?>
        </td>
        <td style="width:25%; text-align:right;">
            <img src="img/generales/reportes/logo_escudo.png" alt="Luján de Cuyo" style="width:10%;"/>
        </td>
    <tr>
        <td colspan="3" style="font-size:16px; text-align:center;">
            TRAMITE ON-LINE N°:<?php echo $tramite->id; ?>
        </td>
    </tr>
</table>

