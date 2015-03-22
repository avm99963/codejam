<aside class="text left small">
	<p><span class="icon svg-ic_dashboard_24px"></span> <a href="dashboard.php">Panel de Control</a></p>
	<?php
	if (isadmin()) {
	?>
	<p><span class="icon svg-ic_group_24px"></span> <a href="users.php">Usuarios</a></p>
	<?php
	}
	?>
	<p class="withoutmarginbottom"><span class="icon svg-ic_class_24px"></span> <a href="contests.php">Competiciones</a></p>
		<?php
		$query2 = mysqli_query($con, "SELECT * FROM contests") or die(mysqli_error($con));
		if (mysqli_num_rows($query2))
		{
		while($row = mysqli_fetch_assoc($query2))
		{
			echo '<p class="padding40 withoutmarginbottom" style="margin-top:5px;"><a href="contest.php?id='.$row['id'].'">'.$row['name'].'</a> [<a href="admincontest.php?id='.$row['id'].'">admin</a>] [<a href="leaderboard.php?id='.$row['id'].'">leader</a>]</p>';
			
		}
		}
	if (isadmin()) {
	?>
		<p class="padding40" style="margin-top:5px;"> <a href="newcontest.php">Crear competición</a></p>
	<p><span class="icon svg-ic_settings_24px"></span> <a href="configuracion.php">Configuración</a></p>
	<?php
	}
	?>
	<p><span class="icon svg-ic_security_24px"></span> <a href="2stepverification.php">Verificación en 2 pasos</a></p>
	<p><span class="icon svg-ic_stars_24px"></span> <a href="promote.php">Pasar de ronda</a></p>
</aside>