<?php
require_once("core.php");
if (isadmin())
{
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "uniquehyperadmin")
  $msg = '<p class="alert-danger">¡No puedes borrar el único hyperadmin!</p>';
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title>Eliminar problema - <?php echo $appname; ?></title>
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
		<h1>Eliminar problema</h1>
		<?=$msg?>
		<?php
		if (isset($_GET['sent']) && $_GET['sent'] == "1") {
			$query = mysqli_query($con, "SELECT * FROM problems WHERE id = '".mysqli_real_escape_string($con, $_GET['id'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error()."</div>");
			if (!mysqli_num_rows($query))
				die("<div class='alert alert-danger'>Este problema no existe.</div>");
			$row = mysqli_fetch_assoc($query);

			$query2 = mysqli_query($con, "SELECT * FROM contests WHERE id = '".mysqli_real_escape_string($con, $row['contest'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error()."</div>");
			$row2 = mysqli_fetch_assoc($query2);

			$sql = "DELETE FROM problems WHERE id = '".(INT)$_GET['id']."'";
			if (mysqli_query($con, $sql)) {
  				header("Location: contest.php?id=".$row2["id"]."&msg=deletesuccessful");
			}
		} else {
			$query = mysqli_query($con, "SELECT * FROM problems WHERE id = '".mysqli_real_escape_string($con, $_GET['id'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error()."</div>");
			if (!mysqli_num_rows($query))
				die("<div class='alert alert-danger'>Este problema no existe.</div>");
			$row = mysqli_fetch_assoc($query);

			$query2 = mysqli_query($con, "SELECT * FROM contests WHERE id = '".mysqli_real_escape_string($con, $row['contest'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error()."</div>");
			$row2 = mysqli_fetch_assoc($query2);
		?>
		<p>Estás a punto de eliminar el problema <b><?=$row["name"]?></b>.</p>
		<p>¿Estás seguro? <span style="color:red;font-weight:bold;">Esta acción no se puede revertir</span></p>
		<p><a href="deleteproblem.php?id=<?php echo $_GET['id'];?>&sent=1" class="button-link-red">Sí</a> <a href="admincontest.php?id=<?=$row2['id']?>" class="button-link">No</a></p>
		<?php
		}
		?>
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