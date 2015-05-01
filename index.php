<?php
require_once("core.php");
initi18n("index");
$msg = "";
if (isset($_GET['msg']) && in_array($_GET['msg'], array("loginwrong", "empty"))) {
	$msg = '<p class="alert-danger">'.i18n("global", "msg_".$_GET['msg']).'</p>';
}
if (isset($_GET['msg']) && in_array($_GET['msg'], array("logoutsuccess", "registersuccess"))) {
	$msg = '<p class="alert-success">'.i18n("global", "msg_".$_GET['msg']).'</p>';
}
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
		<h1><?=i18n("index", "title")?></h1>
		<?=$msg?>
		<?php
		if (loggedin())
		{
		?>
		<p><?=i18n("index", "salute", [userdata('username')])?></p>
		<?php
		if (isadmin())
		{
		?>
		<a href="contests.php" class="button-link"><?=i18n("index", "contests_btn")?></a> <a href="dashboard.php" class="button-link"><?=i18n("index", "dashboard_btn")?></a><br>&nbsp;
		<?php
		}
		else
		{
		?>
		<a href="contests.php" class="button-link"><?=i18n("index", "contests_btn")?></a><br>&nbsp;
		<?php
		}
		}
		else
		{
		?>
		<form action="login.php" method="POST" autocomplete="off" id="formulario">
			<p><label for="username"><?=i18n("index", "user_field")?></label> <input type="text" name="username" id="username" required="required"></p>
			<p><label for="password"><?=i18n("index", "password_field")?></label> <input type="password" name="password" id="password" required="required"></p>
			<p><input type="submit" value="<?=i18n("index", "form_button")?>" class="button-link"></p>
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
if($val === FALSE) {
	echo "<a href='install.php' style='color:red;'>".i18n("index", "install_promotion")."</a>";
}
?>
		</div>
	</article>
</div>
</body>
</html>