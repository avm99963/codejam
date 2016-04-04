<?php
require_once("core.php");
initi18n("deleteuser");
if (isadmin()) {
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "uniquehyperadmin")
  $msg = '<p class="alert-danger">¡No puedes borrar el único hyperadmin!</p>';
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title><?=i18n("deleteuser", "title")?> - <?php echo $appname; ?></title>
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
		<h1><?=i18n("deleteuser", "title")?></h1>
		<?=$msg?>
		<?php
		if (isset($_GET['sent']) && $_GET['sent'] == "1")
		{
			if ($_GET["id"] == $_SESSION["id"]) {
              if (mysqli_num_rows(mysqli_query($con, "SELECT id FROM users WHERE role = 3")) < 2) {
                header("Location: deleteuser.php?id=".$_GET['id']."&msg=uniquehyperadmin");
              	exit();
              }
            }

			$sql = "DELETE FROM users WHERE id = '".(INT)$_GET['id']."' LIMIT 1";
			if (mysqli_query($con, $sql))
				{
  					header("Location: users.php?msg=deletesuccessful");
 				}
				else
				{
					die ("<p class='alert-danger'>Error eliminando el usuario: " . mysqli_error($con) . "</p>");
				}
		}
		else
		{
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
		$query = mysqli_query($con, "SELECT * FROM users WHERE ID = '".mysqli_real_escape_string($con, $_GET['id'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
		if (!mysqli_num_rows($query))
			die("<div class='alert alert-danger'>".i18n("deleteuser", "doesntexist")."</div>");
		$row = mysqli_fetch_assoc($query);
		$type = i18n("global", "role_".$row['role']);
		echo "<tr><td class='users_id'>".$row['id']."</td><td>".$row['username']."</td><td>".$row['name']."</td><td>".$row['surname']."</td><td>".$row['email']."</td><td>".$type."</td><td><a href='edituser.php?id=".$row['id']."'><span class='icon svg-ic_mode_edit_24px'></span></a></td><td><a href='deleteuser.php?id=".$row['id']."'><span class='icon svg-ic_delete_24px'></span></a></td></tr>";
		?>
			</tbody>
		</table>
		<p><?=i18n("global", "delete_areyousure")?></p>
		<p><a href="deleteuser.php?id=<?php echo $_GET['id'];?>&sent=1" class="button-link-red"><?=i18n("global", "yes")?></a> <a href="users.php" class="button-link"><?=i18n("global", "no")?></a></p>
		<?php
		}
		?>
		</div>
	</article>
</div>
</body>
</html>
<?php
} else {
	header('HTTP/1.0 404 Not Found');
}
?>