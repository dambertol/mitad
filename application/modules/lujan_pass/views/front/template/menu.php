<nav id="menu">
    <ul class="links">
        <li><a href="lujan_pass/front/inicio">Inicio</a></li>
        <li><a href="lujan_pass/front/programa">Programa</a></li>
        <li><a href="lujan_pass/front/condiciones">Términos y Condiciones</a></li>
        <?php if (!empty($usuario_logueado)) : ?>
            <li><a href="lujan_pass/front/perfil">Perfil y Credencial</a></li>
        <?php else: ?>
            <li><a href="auth/login/0/com">Acceso prestadores</a></li>
            <li><a href="auth/register/ben">Registrate</a></li>
        <?php endif; ?>
    </ul>
    <ul class="actions stacked">
        <li>
            <?php if (!empty($usuario_logueado)) : ?>
                <a href="auth/logout" class="button primary fit">Salir</a>
            <?php else: ?>
                <a href="auth/login/0/ben" class="button primary fit">Ingresá</a>
            <?php endif; ?>
        </li>
    </ul>
</nav>