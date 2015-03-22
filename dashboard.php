<?php
require_once("core.php");
if (getrole())
{
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title>Dashboard - <?php echo $appname; ?></title>
</head>
<body>
<div class="content">
	<?php include "nav.php"; ?>
	<article>
		<?php anuncio(); ?>
		<?php require("sidebar.php"); ?>
		<div class="text right large">
		<h1>Panel de Control</h1>
		<?php
		/*if ($_GET['msg'] == "newexamen")
			echo "<div class='alert-success'>Examen creado satisfactoriamente</div>";*/
		?>
		¡Hola <?php echo userdata('username'); ?>! Bienvenido a tu Panel de Control.
		</div>
	</article>
</div>
</body>
</html>
<?php
}
else
{
	header('HTTP/1.0 404 Not Found');
}
?>