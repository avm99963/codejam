<?php
require_once("core.php");
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "loginwrong")
	$msg = '<p class="alert-danger">Usuario y/o contraseña incorrecto</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "empty")
	$msg = '<p class="alert-warning">Por favor, rellena todos los campos</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "logoutsuccess")
	$msg = '<p class="alert-success">¡Has cerrado la sesión correctamente!</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "registersuccess")
	$msg = '<p class="alert-success">¡Has creado tu perfil de concursante correctamente! Ya puedes iniciar sesión.</p>';
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title><?=$appname?></title>
</head>
<body>
<div class="content">
	<?php include "nav.php"; ?>
	<article>
		<?php anuncio(); ?>
		<div class="text" style='text-align:center;'>
		<h1>Iniciar sesión</h1>
		<?=$msg?>
		<?php
		if (loggedin())
		{
		?>
		<p>¡Hola <?php echo userdata('username'); ?>!</p>
		<?php
		if (isadmin())
		{
		?>
		<a href="contests.php" class="button-link">Competiciones</a> <a href="dashboard.php" class="button-link">Panel de Control</a><br>&nbsp;
		<?php
		}
		else
		{
		?>
		<a href="contests.php" class="button-link">Competiciones</a><br>&nbsp;
		<?php
		}
		}
		else
		{
		?>
		<form action="login.php" method="POST" autocomplete="off" id="formulario">
			<p><label for="username">Usuario:</label> <input type="text" name="username" id="username" required="required"></p>
			<p><label for="password">Contraseña:</label> <input type="password" name="password" id="password" required="required"></p>
			<p><input type="submit" value="Login" class="button-link"></p>
		</form>
		<script>
		document.getElementById("username").focus();
		</script>
		<p>
		<a href="http://validator.w3.org/check?uri=http%3A%2F%2Favm99963.tk%2Fpedagogia%2F"><img style="border:0;width:88px;height:31px"
		        src="img/valid_html5.png"
		        alt="¡HTML Válido!" /></a>
		<a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border:0;width:88px;height:31px"
		        src="img/vcss-blue.gif"
		        alt="¡CSS Válido!" /></a>
		</p>
		<?php
		}
		?>
<?php
// Select * from table_name will return false if the table does not exist.
$val = mysqli_query($con, "select * from users");
if($val === FALSE)
{
	echo "<a href='install.php' style='color:red;'>¡Instala la aplicación antes de usarla!</a>";
}
?>
		</div>
	</article>
</div>
</body>
</html>