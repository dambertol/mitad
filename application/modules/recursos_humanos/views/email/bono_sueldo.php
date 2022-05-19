<html>
    <body style="font-family:Arial, Helvetica, sans-serif; text-align:left; color:#4e4c4e; background-color:#F7F7F7;">
        <table width="800" cellspacing="0" cellpadding="0" border="0" align="center" style="padding:0 20px;">
            <tbody>
                <tr>
                    <td style="width:100px;">
                        <img src="https://sistemamlc.lujandecuyo.gob.ar/v2/img/generales/logo_lujan_002.png" width="100" height="100" alt="Luján de Cuyo" />
                    </td>
                    <td style="text-align:center; font-size:20px;">

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border-top:#F4D800 1px solid; font-weight:bold;">
                        <br>
                        <p style='text-align:justify;'>¡Hola <?php echo "$nombre $apellido"; ?>!</p><br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h3>Tenés documentación disponible:</h3>
                        <p style='text-align:justify;'>Queremos informarte que ya tenés disponible tu recibo de sueldo digital. Hacé clic en el siguiente link y obtenelo de forma inmediata:</p>
                        <p style='text-align:center;'>
                            <a href="http://e-legajo.lujandecuyo.gob.ar/V.aspx?P=<?php echo "$codigo"; ?>" target="_blank" style="-webkit-appearance:button; -moz-appearance:button; appearance:button; text-decoration:none; color:initial; padding:10px 15px; font-size:14px; line-height:1.5; border-radius:3px; background-color:#4E4C4E; border-color:#4E4C4E; color:#FFD318; font-weight: bold;">Descargá tu documentación</a>
                        </p>
                        <p style='text-align:justify;'>
                            Recordá que también podés descargar tu recibo accediendo a tu <a href="http://e-legajo.lujandecuyo.gob.ar/" target="_blank">e-legajo</a> aquí: <a href="http://e-legajo.lujandecuyo.gob.ar/" target="_blank">http://e-legajo.lujandecuyo.gob.ar/</a>
                        </p>
                        <?php if (!empty($leyenda)): ?>
                            <h3>Información importante:</h3>
                            <p style='text-align:justify;'><?php echo "$leyenda"; ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-weight:bold;">
                        <br>
                        <br>
                        <p>Saludos cordiales</p>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border:#4E4C4E 1px solid;">
                        <p style='text-align:center;'>
                            Por cualquier consulta, ponete en contacto con Recursos Humanos<br>
                            llamándonos al <b>498-9926/55</b>, o enviándonos Whatsapp al <b>261-5927929</b>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border-bottom:#F4D800 1px solid; font-weight:bold;">
                        <br>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>