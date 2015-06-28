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
<title>Eliminar competición - <?php echo $appname; ?></title>
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
		<h1>Eliminar competición</h1>
		<?=$msg?>
		<?php
		if (isset($_GET['sent']) && $_GET['sent'] == "1") {
			$sql = "DELETE FROM problems WHERE contest = ".(INT)$_GET['id']."";
			if (mysqli_query($con, $sql)) {
				$sql2 = "DELETE FROM contests WHERE id = ".(INT)$_GET['id']." LIMIT 1";
				if (mysqli_query($con, $sql2)) {
					$sql3 = "DELETE FROM submissions WHERE contest = ".(INT)$_GET['id']."";
					if (mysqli_query($con, $sql3)) {
  						header("Location: contests.php?msg=deletesuccessful");
  					} else {
  						die ("<p class='alert-danger'>Error eliminando los envíos de la competición: " . mysqli_error($con) . "</p>");
  					}
 				} else {
					die ("<p class='alert-danger'>Error eliminando la competición: " . mysqli_error($con) . "</p>");
				}
			} else {
				die ("<p class='alert-danger'>Error eliminando los problemas de la competición: " . mysqli_error($con) . "</p>");
			}
		} else {
			$query = mysqli_query($con, "SELECT * FROM contests WHERE ID = '".mysqli_real_escape_string($con, $_GET['id'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error()."</div>");
			if (!mysqli_num_rows($query))
				die("<div class='alert alert-danger'>Esta competición no existe.</div>");
			$row = mysqli_fetch_assoc($query);
			if ($row["privacy"] == 2) {
				$img = "public";
			} elseif ($row["privacy"] == 1) {
				$img = "lock_open";
			} elseif ($row["privacy"] == 0) {
				$img = "lock";
			}
			echo "<blockquote><h3><span class='icon svg-ic_".$img."_24px'></span> ".$row["name"]."</h3><p>".$row["description"]."</p></blockquote>";
		?>
		<p>¿Estás seguro? <span style="color:red;font-weight:bold;">Esta acción no se puede revertir</span></p>
		<p><a href="deletecontest.php?id=<?php echo $_GET['id'];?>&sent=1" class="button-link-red">Sí</a> <a href="admincontest.php?id=<?=$_GET['id']?>" class="button-link">No</a></p>
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