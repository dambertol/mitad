<html>
    <body style="font-family:Arial, Helvetica, sans-serif; text-align:left; color:#4e4c4e; background-color:#F7F7F7;">
        <table width="800" cellspacing="0" cellpadding="0" border="0" align="center">
            <tbody>
                <tr>
                    <td style="width:100px;">
                        <img src="https://sistemamlc.lujandecuyo.gob.ar/v2/img/generales/logo_lujan_002.png" width="100" height="100" alt="Luján de Cuyo" />
                    </td>
                    <td style="text-align:center; font-size:20px;">
                        <span style="line-height:100px;">Consulta&nbsp;OnLine&nbsp;Finalizada</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border-top:#F4D800 1px solid; font-weight:bold;">
                        <p style='text-align:justify;'>Estimado <?php echo $nombre; ?></p><br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p style='text-align:justify;'>Tenemos novedades sobre la Consulta OnLine <b>N°<?php echo $tramite; ?></b>. <b>El mismo se encuentra "Finalizado"</b>.</p>
                        <?php if (!empty($observaciones)) : ?>
                            <p style='text-align:justify;'><b>Observaciones:</b> <?php echo $observaciones; ?></p>
                        <?php endif; ?>
                        <?php if ($persona) : ?>
                            <p style='text-align:justify;'>
                                Puede realizar el seguimiento de su consulta a través de la siguiente URL: <a href="https://consultas.lujandecuyo.gob.ar/tramites_online/tramites/ver/<?php echo $tramite; ?>" target="_blank">https://consultas.lujandecuyo.gob.ar/tramites_online/tramites/ver/<?php echo $tramite; ?></a>
                            </p>
                        <?php endif; ?>
                        <p style='text-align:justify;'>
                            Por favor, no conteste a este correo electrónico, no recibiremos su respuesta.
                            Si desea contestar, por favor, hágalo a través de la plataforma de Consultas OnLine.
                        </p>
                        <p style='text-align:justify;'>
                            Nunca le enviaremos un correo electrónico para pedirle que revele o verifique su contraseña. Si recibe algún correo electrónico sospechoso que contenga un enlace para actualizar la información de su cuenta, no haga clic en él. Sin embargo, no deje de informarnos de dicho mensaje para que se investigue.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border-bottom:#F4D800 1px solid; font-weight:bold;">
                        <p style='text-align:justify;'>Atentamente,<br>Municipalidad de Luján de Cuyo</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>