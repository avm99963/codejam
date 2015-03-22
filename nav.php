<nav>
	<ul>
			<li><a id="home" href="index.php">Home</a></li>
			<?php
			if (isset($_SESSION['id']))
			{
			if (getrole() > 0)
			{
			?>
			<li><a href="dashboard.php">Panel de Control</a></li>
			<?php
			} if (!isadmin()) {
			?>
			<li><a href="editprofile.php">Editar perfil</a></li>
			<?php
			}
			?>
			<li><a href="contests.php">Competiciones</a></li>
			<li><a href="logout.php">Logout</a></li>
			<li class="stickright firstnavright" style="color:#57B9FF;">Hola <b><?php echo userdata('username'); ?></b></li>
			<?php
			} else {
			?>
			<li><a href="register.php">Formulario de registro</a></li>
			<?php
			}
			?>
	</ul>
</nav>