<?php 
header("Pragma: public");
header("Expires: 0");
//$filename = "busqueda".$nombre.".xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

?>
<table>
<tbody>
<tr>
<th>
<h2>Listado en tabla excel</h2>
</th>
</tr>
    <?php foreach ($variable as $key => $value) {
        <tr>
        foreach ($value as $key2 => $value2) {
            <td>$value2</td>
        }
        </tr>
    }
</tbody>
</table>