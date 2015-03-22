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
<title>Eliminar llave de seguridad - <?php echo $appname; ?></title>
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
thead {
  font-weight: bold;
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
		<h1>Eliminar llave de seguridad</h1>
		<?=$msg?>
		<?php
		if (!twostepverification()) {
        	header("Location: 2stepverification.php");
        	exit;
        }
		if (isset($_GET['sent']) && $_GET['sent'] == "1")
		{
			$sql1 = "SELECT id FROM securitykeys WHERE id = ".(INT)$_GET['id']." AND user_id = ".$_SESSION['id'];
			if (mysqli_num_rows(mysqli_query($con, $sql1))) {
				$sql = "DELETE FROM securitykeys WHERE id = ".(INT)$_GET['id']." LIMIT 1";
				if (mysqli_query($con, $sql)) {
  					header("Location: securitykeys.php?msg=deletesuccessful");
 				} else {
					die ("<p class='alert-danger'>Error eliminando la llave de seguridad: " . mysqli_error($con) . "</p>");
				}
			}
		}
		else
		{
		?>
		<table>
			<thead>
              <tr><td colspan="2">Dispositivo donde se añadó</td><td colspan="2">Dispositivo donde se ha iniciado sesión la última vez</td></tr>
              <tr><td>Dirección IP</td><td>Tiempo</td><td>Dirección IP</td><td>Tiempo</td></tr>
            </thead>
			<tbody>
		<?php
		$query = mysqli_query($con, "SELECT * FROM securitykeys WHERE id = ".(INT)$_GET['id']." AND user_id = ".$_SESSION['id']." LIMIT 1") or die("<div class='alert-danger'>".mysqli_error()."</div>");
		if (!mysqli_num_rows($query))
			die("<div class='alert alert-danger'>Esta llave de seguridad no existe.</div>");
		$row = mysqli_fetch_assoc($query);
		echo "<tr><td>".$row["deviceadded"]."</td><td>".$row["dateadded"]."</td><td>".$row["lastuseddevice"]."</td><td>".$row["lastuseddate"]."</td><td><a href='deletesecuritykey.php?id=".$row['id']."'><span class='icon svg-ic_delete_24px'></span></a></td></tr>";
		?>
			</tbody>
		</table>
		<p>¿Estás seguro? <span style="color:red;font-weight:bold;">Esta acción no se puede revertir</span></p>
		<p><a href="deletesecuritykey.php?id=<?php echo $_GET['id'];?>&sent=1" class="button-link-red">Sí</a> <a href="securitykeys.php" class="button-link">No</a></p>
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