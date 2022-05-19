<html>
	<body style="font-family:Arial, Helvetica, sans-serif; text-align:left; color:#4e4c4e; background-color:#F7F7F7;">
		<table width="800" cellspacing="0" cellpadding="0" border="0" align="center">
			<tbody>
				<tr>
					<td style="width:100px;">
						<img src="https://sistemamlc.lujandecuyo.gob.ar/v2/img/generales/logo_lujan_002.png" width="100" height="100" alt="Luján de Cuyo" />
					</td>
					<td style="text-align:center; font-size:20px;">
						<span style="line-height:100px;">Recuperar contraseña de Sistema MLC 2</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="border-top:#F4D800 1px solid; font-weight:bold;">
						<p style='text-align:justify;'>Estimado usuario,</p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p style='text-align:justify;'>Hemos recibido una solicitud para recuperar la contraseña de la cuenta asociada a esta dirección de correo electrónico.</p>
						<p style='text-align:justify;'><?php echo sprintf(lang('email_forgot_password_subheading'), anchor('auth/reset_password/' . $forgotten_password_code, lang('email_forgot_password_link'))); ?></p>
						<p style='text-align:justify;'>Si no solicitó el restablecimiento de su contraseña, puede hacer caso omiso de este mensaje.</p>
						<p style='text-align:justify;'>Nunca le enviaremos un correo electrónico para pedirle que revele o verifique su contraseña. Si recibe algún correo electrónico sospechoso que contenga un enlace para actualizar la información de su cuenta, no haga clic en él. Sin embargo, no deje de informarnos de dicho mensaje para que se investigue.</p>
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