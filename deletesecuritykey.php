<?php
require_once("core.php");
initi18n("deletesecuritykey");
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
<title><?=i18n("deletesecuritykey", "title")?> - <?php echo $appname; ?></title>
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
		<h1><?=i18n("deletesecuritykey", "title")?></h1>
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
              <tr><td colspan="2"><?=i18n("deletesecuritykey", "addeddevice")?></td><td colspan="2"><?=i18n("deletesecuritykey", "inserteddevice")?></td></tr>
              <tr><td><?=i18n("deletesecuritykey", "ipaddress")?></td><td><?=i18n("deletesecuritykey", "date")?></td><td><?=i18n("deletesecuritykey", "ipaddress")?></td><td><?=i18n("deletesecuritykey", "date")?></td></tr>
            </thead>
			<tbody>
		<?php
		$query = mysqli_query($con, "SELECT * FROM securitykeys WHERE id = ".(INT)$_GET['id']." AND user_id = ".$_SESSION['id']." LIMIT 1") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
		if (!mysqli_num_rows($query))
			die("<div class='alert alert-danger'>Esta llave de seguridad no existe.</div>");
		$row = mysqli_fetch_assoc($query);
		if (empty($row["lastuseddate"])) {
	      $lastuseddevice = "-";
	      $lastuseddate = "-";
	    } else {
	      $lastuseddevice = $row["lastuseddevice"];
	      $lastuseddate = date("d/m/Y H:i:s", $row["lastuseddate"]);
	    }
		echo "<tr><td>".$row["deviceadded"]."</td><td>".date("d/m/Y H:i:s", $row["dateadded"])."</td><td>".$lastuseddevice."</td><td>".$lastuseddate."</td></tr>";
		?>
			</tbody>
		</table>
		<p><?=i18n("global", "delete_areyousure")?></p>
		<p><a href="deletesecuritykey.php?id=<?php echo $_GET['id'];?>&sent=1" class="button-link-red"><?=i18n("global", "yes")?></a> <a href="securitykeys.php" class="button-link"><?=i18n("global", "no")?></a></p>
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