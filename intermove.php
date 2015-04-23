<?php
require_once("core.php");
if (isadmin())
{
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title>Mover problema - <?php echo $appname; ?></title>
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
		<h1>Mover problema</h1>
		<?php
		if (isset($_GET['sent']) && $_GET['sent'] == "1") {
			$query = mysqli_query($con, "SELECT * FROM problems WHERE id = '".mysqli_real_escape_string($con, $_POST['id'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error()."</div>");
			if (!mysqli_num_rows($query))
				die("<div class='alert alert-danger'>Este problema no existe.</div>");
			$row = mysqli_fetch_assoc($query);

			$query2 = mysqli_query($con, "SELECT * FROM contests WHERE id = '".mysqli_real_escape_string($con, $row['contest'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error()."</div>");
			$row2 = mysqli_fetch_assoc($query2);

			$sql = array();
			$sql["problems"] = "UPDATE problems set contest = ".(INT)$row["contest"]." WHERE id = ".(INT)$_POST['id'];
			$sql["submissions"] = "UPDATE submissions SET contest = ".(INT)$row["contest"]." WHERE problem = ".(INT)$_POST['id'];

			$query3 = mysqli_query($con, "SELECT solution FROM submissions WHERE problem = ".(INT)$_GET["id"]);
			while ($row3 = mysqli_fetch_assoc($query3)) {
				if (!empty($row3["solution"])) {
					$solution = json_decode($row3["solution"], true);
					if (!unlink("uploaded_solutions/".$solution["output"])) {
						echo "<div class='alert alert-warning'>No se ha podido eliminar el archivo uploaded_solutions/".$solution["output"].". Bórralo manualmente o prueba después de purgarlo en la página <a href='debug.php'>debug</a>.</div>";
					} else {
						if (!unlink("uploaded_solutions/".$solution["sourcecode"])) {
							echo "<div class='alert alert-warning'>No se ha podido eliminar el archivo uploaded_solutions/".$solution["sourcecode"].". Bórralo manualmente o prueba después de purgarlo en la página <a href='debug.php'>debug</a>.</div>";
						}
					}
				}
			}

			foreach ($sql as $table => $single_sql) {
				if (mysqli_query($con, $single_sql)) {
					// Excellent!
				} else {
					die("<div class='alert alert-danger'>No se han podido eliminar los datos de la tabla ".$table.".</div>");
				}
			}
			header("Location: admincontest.php?id=".$row2["id"]."&msg=intermoveproblemsuccess");
		} else {
			$query = mysqli_query($con, "SELECT * FROM problems WHERE id = '".mysqli_real_escape_string($con, $_GET['id'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error()."</div>");
			if (!mysqli_num_rows($query))
				die("<div class='alert alert-danger'>Este problema no existe.</div>");
			$row = mysqli_fetch_assoc($query);

			$query2 = mysqli_query($con, "SELECT * FROM contests") or die("<div class='alert-danger'>".mysqli_error()."</div>");
			$contests = array();
			while ($row2 = mysqli_fetch_assoc($query2)) {
				$contests[] = array(
					"id" => $row2["id"],
					"name"=> $row2["name"]
				);
			}
			$row2 = mysqli_fetch_assoc($query2);
		?>
		<form action="intermove.php?sent=1" method="POST">
			<p>Mover problema a la siguiente competición:</p>
			<p><select name="contest"><?php foreach ($contests as $contest) { echo "<option value='".$contest["id"]."'>".$contest["name"]."</option>"; } ?></select></p>
			<p><input type="submit" class="button-link-red" value="Sí"> <a href="admincontest.php?id=<?=$row['contest']?>" class="button-link">No</a></p>
		</form>
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