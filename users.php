<?php
require_once("core.php");
if (isadmin())
{
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title>Usuarios - <?php echo $appname; ?></title>
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
		<h1>Lista de usuarios</h1>
		<?php
		$msg = "";
		if (isset($_GET['msg']) && $_GET['msg'] == "newsuccessful")
			$msg = "<p class='alert-success'>Usuario editado.</p>";
		if (isset($_GET['msg']) && $_GET['msg'] == "deletesuccessful")
			$msg = "<p class='alert-success'>Usuario eliminado.</p>";
		echo $msg;
		?>
		<table>
			<thead>
				<tr>
					<th>ID</th>
					<th>Usuario</th>
					<th>Nombre</th>
					<th>Apellidos</th>
					<th>Email</th>
					<th>Rol</th>
				</tr>
			</thead>
			<tbody>
		<?php
		$query = mysqli_query($con, "SELECT * FROM users ORDER BY ID ASC") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
		while ($row = mysqli_fetch_assoc($query))
		{
			if ($row['role'] == "0") {
				$type = "Contestant";
			} elseif ($row['role'] == "1") {
				$type = "Judge";
			} elseif ($row['role'] == "2") {
				$type = "Problem writer";
			} elseif ($row['role'] == "3") {
				$type = "Hyperadmin";
			}
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