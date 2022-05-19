<!DOCTYPE html>
<html lang="es">
    <body>
        <?php if (!empty($padron)): ?>
            <?php $padron = $padron[0]; ?>
            <table>
                <tr class="medidas">
                    <td style="width:14%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:16%;"></td>
                    <td style="width:4%;"></td>
                    <td style="width:16%;"></td>
                    <td style="width:16%;"></td>
                    <td style="width:24%;"></td>
                </tr>
                <tr>
                    <td colspan="7" style="padding-top:180px;"></td>
                </tr>
                <tr>
                    <td colspan="7" style="font-size:14px; text-align:right; font-weight:bold;">
                        N° PADRÓN: <?php echo (int) $padron->trib_Cuenta; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="font-size:14px;">
                        <b><?php echo "$padron->pers_Apellido, $padron->pers_Nombre"; ?></b>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="font-size:12px; height:84px;">
                        <?php
                        if (empty($padron->zent_Calle))
                        {
                            echo "$padron->come_Calle ";
                            if ($padron->come_Altura != '0')
                                echo "$padron->come_Altura ";
                            if (!empty($padron->come_Piso) && $padron->come_Piso != ' ')
                                echo "PISO: $padron->come_Piso ";
                            if (!empty($padron->come_Depto) && $padron->come_Depto != ' ')
                                echo "DEPTO: $padron->come_Depto ";
                            if (!empty($padron->come_Lote) && $padron->come_Lote != ' ')
                                echo "LOTE:$padron->come_Lote ";
                            if (!empty($padron->come_Manzana) && $padron->come_Manzana != ' ')
                                echo "M:$padron->come_Manzana ";
                            if (!empty($padron->come_Fraccion) && $padron->come_Fraccion != ' ')
                                echo "F:$padron->come_Fraccion ";
                            if (!empty($padron->come_Local) && $padron->come_Local != ' ')
                                echo "LOCAL:$padron->come_Local ";
                            if (!empty($padron->come_Galeria) && $padron->come_Galeria != ' ')
                                echo "GAL:$padron->come_Galeria ";
                            echo "<br />";
                            echo "($padron->come_CodigoPostal) - $padron->come_Localidad - MENDOZA";
                        }
                        else
                        {
                            echo "$padron->zent_Calle $padron->zent_Altura ";
                            if (!empty($padron->zent_Piso) && $padron->zent_Piso != ' ')
                                echo "PISO: $padron->zent_Piso ";
                            if (!empty($padron->zent_Depto) && $padron->zent_Depto != ' ')
                                echo "DEPTO: $padron->zent_Depto ";
                            if (!empty($padron->zent_Lote) && $padron->zent_Lote != ' ')
                                echo "LOTE:$padron->zent_Lote ";
                            if (!empty($padron->zent_Manzana) && $padron->zent_Manzana != ' ')
                                echo "M:$padron->zent_Manzana ";
                            if (!empty($padron->zent_Fraccion) && $padron->zent_Fraccion != ' ')
                                echo "F:$padron->zent_Fraccion ";
                            echo "<br />";
                            echo "($padron->zent_CodigoPostal) - $padron->zent_Localidad - $padron->zent_Provincia";
                        }
                        echo "<br />";
                        ?>
                    </td>
                </tr>
            </table>
            <table>
                <tr class="medidas">
                    <td style="width:5%; padding-top:68px;"></td>
                    <td style="width:15%;"></td>
                    <td style="width:15%;"></td>
                    <td style="width:15%;"></td>
                    <td style="width:15%;"></td>
                    <td style="width:15%;"></td>
                    <td style="width:15%;"></td>
                    <td style="width:5%;"></td>
                </tr>
                <tr>
                    <td style="font-size:10px;"></td>
                    <td style="font-size:10px; text-align:center; border:1px solid #D8D7D7; background-color:#EBE9E9;">UNI.TRIB.ANUAL</td>
                    <td style="font-size:10px; text-align:center; border:1px solid #D8D7D7;"><?php echo $facturacion[1]; ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid #D8D7D7; background-color:#EBE9E9;">UNI.TRIB.BIMESTRAL</td>
                    <td style="font-size:10px; text-align:center; border:1px solid #D8D7D7;"><?php echo $facturacion[2]; ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid #D8D7D7; background-color:#EBE9E9;">VALOR UNI.TRIB.</td>
                    <td style="font-size:10px; text-align:center; border:1px solid #D8D7D7;"><?php echo $facturacion[3]; ?></td>
                    <td></td>
                </tr>
            </table>
        <?php endif; ?>
        <div style="margin-top:2px; width:100%;">
            <div style="font-size:12px; text-align:left; width:50%; float:left; font-weight:bold;">
                CÓDIGO DE PAGO ELECTRÓNICO: <?php echo "2" . str_pad((int) $padron->trib_Cuenta, 6, "0", STR_PAD_LEFT); ?>
            </div>
            <div style="font-size:12px; text-align:right; width:50%; float:left; font-weight:bold;">
                <?php echo $padron->usua_Clave; ?>
            </div>
        </div>
        <div style="width:100%;">
            <?php if ($deuda <= 10): ?>
                <div style="font-size:18px; text-align:center; width:45%; float:left; font-weight:bold; padding:10px 0 12px 0; ">
                    ¡Gracias por estar al día!
                </div>
                <div style="font-size:18px; text-align:center; width:10%; float:left; font-weight:bold; padding:-4px 0 0 -3px;">
                    <img src="img/major/feliz.png" alt=")" style="width:50%;"/>
                </div>
                <div style="font-size:18px; text-align:center; width:45%; float:left; font-weight:bold; padding:10px 0 12px 0; ">
                    <?php if ($descuento_en_termino > 0): ?>
                        Obtuviste un descuento de $<?= $descuento_en_termino; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div style="font-size:18px; text-align:center; width:45%; float:left; font-weight:bold; padding:10px 0 12px 0; ">
                    ¡Tu padrón registra deuda!
                </div>
                <div style="font-size:18px; text-align:center; width:10%; float:left; font-weight:bold; padding:-4px 0 0 -3px;">
                    <img src="img/major/triste.png" alt=")" style="width:50%;"/>
                </div>
                <div style="font-size:18px; text-align:center; width:45%; float:left; font-weight:bold; padding:10px 0 12px 0; ">
                    $<?= number_format($deuda, 2, ',', '.'); ?> al 31/12/2020
                </div>
            <?php endif; ?>
        </div>
        <table>
            <tr class="medidas">
                <td style="width:100%;"></td>
            </tr>
            <tr>
                <td style="font-size:10px; border:1px solid; padding:5px; height:38px; overflow:hidden;">
                    <b>RUBROS:</b>
                    <?php
                    foreach ($padron->rubros as $Rubro)
                    {
                        echo "$Rubro->rubl_Codigo - $Rubro->rubr_Descripcion // ";
                    }
                    ?>
                </td>
            </tr>
        </table>
        <div style="float:left; width:49%; margin-left:25%; padding-top:8px;">
            <div style="height:100px;">
                <table style="border:1px solid;">
                    <tr class="medidas">
                        <td style="width:35%;"></td>
                        <td style="width:15%;"></td>
                        <td style="width:35%;"></td>
                        <td style="width:15%;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-bottom:1px solid; font-size:17px; text-align:center; vertical-align:middle; padding:0; font-weight:bold;">
                            Comercio
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-right:1px solid; text-align:center; vertical-align:middle; font-weight:bold;">Pago Anual</td>
                        <td colspan="2" style="border-right:1px solid; text-align:center; vertical-align:middle; font-weight:bold;">Pago Bimestral</td>
                    </tr>
                    <tr>
                        <td style="font-size:8px; text-align:right;">Derechos de Comercio</td>
                        <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual[4]; ?></td>
                        <td style="font-size:8px; text-align:right;">Derechos de Comercio</td>
                        <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion[4]; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:8px; text-align:right;">Bomberos</td>
                        <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual[0]; ?></td>
                        <td style="font-size:8px; text-align:right;">Bomberos</td>
                        <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion[0]; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:8px; text-align:right;">Fondo Sostenim Biblioteca</td>
                        <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual[5]; ?></td>
                        <td style="font-size:8px; text-align:right;">Fondo Sostenim Biblioteca</td> 
                        <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion[5]; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;">Total</td>
                        <td style="border-right:1px solid; font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;"><?php echo $facturacion_anual[6]; ?></td>
                        <td style="font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;">Total</td>
                        <td style="border-right:1px solid; font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;"><?php echo $facturacion[6]; ?></td>
                    </tr>
                </table>
            </div>
            <div style="float:left; width:100%; padding:0px; text-align:center;">
                Opción pago anual
            </div>
            <?php if (!empty($boletas)): ?>
                <?php foreach ($boletas as $boleta): ?>
                    <div style="float:left; width:100%; padding:2px;">
                        <table>
                            <tr class="medidas">
                                <td style="width:50%;"></td>
                                <td style="width:50%;"></td>
                            </tr>
                            <tr>
                                <?php
                                $vencimiento = new DateTime($boleta->pago_VtoTermino);
                                $codigo = '425' .
                                        str_pad(number_format($boleta->pago_Importe, 2, '', ''), 8, '0', STR_PAD_LEFT) .
                                        str_pad($vencimiento->format('z') + 1, 3, '0', STR_PAD_LEFT) .
                                        str_pad($boleta->pago_CodigoDelegacion, 13, '0', STR_PAD_LEFT) .
                                        $boleta->pago_Numero . $vencimiento->format('y') . $boleta->pago_DigitoVerificador;
                                ?>
                                <td rowspan="2" style="border:1px solid; font-size:13px; text-align:center; vertical-align:middle; padding:0; font-weight:bold;">
                                    <?php if ($boleta->pago_CodigoDelegacion === 85): ?>
                                        <?php echo $boleta->pago_Periodo . ' Anual'; ?>
                                    <?php else: ?>
                                        <?php echo $boleta->pago_Periodo . ' Cuota ' . substr($boleta->pago_Cuota, -1) . ' de 6'; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="border:1px solid; font-size:11px; text-align:center; vertical-align:middle; padding:0;">
                                    <?php echo 'Vencimiento Único'; ?>
                                </td>	
                            </tr>
                            <tr>
                                <td style="border:1px solid; font-size:11px; text-align:center; vertical-align:middle; padding:0;">
                                    <?php echo $vencimiento->format('d/m/Y'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:1px solid; font-size:12px; text-align:center; font-weight:bold;">
                                    IMPORTE
                                </td>
                                <td style="border:1px solid; font-size:12px; text-align:center; font-weight:bold;">
                                    $<?php echo number_format($boleta->pago_Importe, 2, ',', '.'); ?>
                                </td>
                            </tr>
                            <tr style="border:1px solid;">
                                <td colspan="2" style="font-size:12px; text-align:center; padding-top:5px;">
                            <barcode code="<?php echo $codigo; ?>" type="I25" class="barcode" size="0.65" height="1.0" />
                            <?php echo $codigo; ?>
                            </td>
                            </tr>
                        </table>
                    </div>
                    <?php if ($boleta->pago_CodigoDelegacion === 85): ?>
                        <div style="float:left; width:100%; padding:2px; text-align:center;">
                            Opción pago bimestral
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </body>
</html>