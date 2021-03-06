<?php
require_once("core.php");
initi18n("users");
if (isadmin())
{
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title><?=i18n("users", "title")?> - <?php echo $appname; ?></title>
<style>
td, th
{
	padding:5px;
}
table
{
	border-collapse:collapse;
}
table, th, td
{
	border: 1px solid black;
}
</style>
</head>
<body>
<div class="content">
	<?php include "nav.php"; ?>
	<article>
		<?php anuncio(); ?>
		<?php require("sidebar.php"); ?>
		<div class="text right large">
		<h1><?=i18n("users", "subtitle")?></h1>
		<?php
		$msg = "";
		if (isset($_GET['msg']) && in_array($_GET['msg'], array("newsuccessful", "deletesuccessful"))) {
			$msg = "<p class='alert-success'>".i18n("global", "msg_".$_GET['msg'])."</p>";
		}
		echo $msg;
		?>
		<table>
			<thead>
				<tr>
					<th><?=i18n("global", "ID")?></th>
					<th><?=i18n("global", "username")?></th>
					<th><?=i18n("global", "name")?></th>
					<th><?=i18n("global", "surname")?></th>
					<th><?=i18n("global", "email")?></th>
					<th><?=i18n("global", "role")?></th>
				</tr>
			</thead>
			<tbody>
		<?php
		$query = mysqli_query($con, "SELECT * FROM users ORDER BY ID ASC") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
		while ($row = mysqli_fetch_assoc($query)) {
			$type = i18n("global", "role_".$row['role']);
			echo "<tr><td class='users_id'>".$row['id']."</td><td>".$row['username']."</td><td>".$row['name']."</td><td>".$row['surname']."</td><td>".$row['email']."</td><td>".$type."</td><td><a href='edituser.php?id=".$row['id']."'><span class='icon svg-ic_mode_edit_24px'></span></a></td><td><a href='deleteuser.php?id=".$row['id']."'><span class='icon svg-ic_delete_24px'></span></a></td></tr>";
		}
		?>
			</tbody>
		</table>
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