<div class="col-md-3 left_col">
	<div class="left_col scroll-view">
		<div class="navbar nav_title" style="border: 0;">
			<a href="escritorio" class="site_title">
				<img src="img/generales/logo-header.png" alt="LC" /><!--<span class="logo-lg"><?php echo TITLE; ?></span>-->
			</a>
		</div>
		<div class="clearfix"></div>
		<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
			<div class="menu_section">
				<!--MENÚ PRINCIPAL-->
				<ul class="nav side-menu">
					<?php if (!empty($accesos_nav['nav'])) echo $accesos_nav['nav']; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="top_nav">
	<div class="nav_menu">
		<nav class="" role="navigation">
			<div class="nav toggle">
				<a id="menu_toggle"><i class="fa fa-bars"></i></a>
			</div>
			<ul class="nav navbar-nav navbar-right">
				<?php if (!empty($accesos_nav['user_menu'])) echo $accesos_nav['user_menu']; ?>
				<!--ALERTAS-->
				<li role="presentation" class="dropdown">
					<a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-bell-o"></i>
						<span class="badge bg-red" id="alertas-count">0</span>
					</a>
					<ul id="alertas-menu" class="dropdown-menu list-unstyled msg_list" role="menu">
						<li>
							<i class="fa fa-square"></i>Sin notificaciones pendientes
						</li>
					</ul>
				</li>
			</ul>
		</nav>
	</div>
</div>