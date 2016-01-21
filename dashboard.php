<?php
require_once("core.php");
initi18n("dashboard");
if (getrole())
{
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title><?=i18n("dashboard", "title")?> - <?php echo $appname; ?></title>
</head>
<body>
<div class="content">
	<?php include "nav.php"; ?>
	<article>
		<?php anuncio(); ?>
		<?php require("sidebar.php"); ?>
		<div class="text right large">
		<h1><?=i18n("dashboard", "title")?></h1>
		<?=i18n("dashboard", "msg", array(userdata("username")))?>
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