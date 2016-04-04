<?php
require_once("core.php");
if (isadmin())
{
initi18n("deleteproblem");
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title><?=i18n("deleteproblem", "title")?> - <?php echo $appname; ?></title>
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
		<h1><?=i18n("deleteproblem", "title")?></h1>
		<?php
		if (isset($_GET['sent']) && $_GET['sent'] == "1") {
			$query = mysqli_query($con, "SELECT * FROM problems WHERE id = '".mysqli_real_escape_string($con, $_GET['id'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
			if (!mysqli_num_rows($query))
				die("<div class='alert alert-danger'>".i18n("deleteproblem", "problemdoesntexist")."</div>");
			$row = mysqli_fetch_assoc($query);

			$query2 = mysqli_query($con, "SELECT * FROM contests WHERE id = '".mysqli_real_escape_string($con, $row['contest'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
			$row2 = mysqli_fetch_assoc($query2);

			$sql = array();
			$sql["problems"] = "DELETE FROM problems WHERE id = ".(INT)$_GET['id'];
			$sql["submissions"] = "DELETE FROM submissions WHERE problem = ".(INT)$_GET['id'];

			$query3 = mysqli_query($con, "SELECT solution FROM submissions WHERE problem = ".(INT)$_GET["id"]);
			while ($row3 = mysqli_fetch_assoc($query3)) {
				if (!empty($row3["solution"])) {
					$solution = json_decode($row3["solution"], true);
					if (!unlink("uploaded_solutions/".$solution["output"])) {
						echo "<div class='alert alert-warning'>".i18n("deleteproblem", "error_file", "uploaded_solutions/".$solution["output"])."</div>";
					} else {
						if (!unlink("uploaded_solutions/".$solution["sourcecode"])) {
							echo "<div class='alert alert-warning'>".i18n("deleteproblem", "error_file", "uploaded_solutions/".$solution["sourcecode"])."</div>";
						}
					}
				}
			}

			foreach ($sql as $table => $single_sql) {
				if (mysqli_query($con, $single_sql)) {
					if (!empty($row["io"])) {
						$io = json_decode($row["io"], true);
						foreach ($io["files"] as $file) {
							if (!unlink("uploaded_img/".$file)) {
								echo "<div class='alert alert-warning'>".i18n("deleteproblem", "error_file", "uploaded_img/".$file)."</div>";
							} else {
								header("Location: admincontest.php?id=".$row2["id"]."&msg=deleteproblemsuccess");
							}
						}
					}
				} else {
					die("<div class='alert alert-danger'>No se han podido eliminar los datos de la tabla ".$table.".</div>");
				}
			}
		} else {
			$query = mysqli_query($con, "SELECT * FROM problems WHERE id = '".mysqli_real_escape_string($con, $_GET['id'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
			if (!mysqli_num_rows($query))
				die("<div class='alert alert-danger'>Este problema no existe.</div>");
			$row = mysqli_fetch_assoc($query);

			$query2 = mysqli_query($con, "SELECT * FROM contests WHERE id = '".mysqli_real_escape_string($con, $row['contest'])."' LIMIT 1") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
			$row2 = mysqli_fetch_assoc($query2);
		?>
		<p><?=i18n("deleteproblem", "aboutto", array($row["name"]))?></p>
		<p><?=i18n("global", "delete_areyousure")?></p>
		<p><a href="deleteproblem.php?id=<?php echo $_GET['id'];?>&sent=1" class="button-link-red"><?=i18n("global", "yes")?></a> <a href="admincontest.php?id=<?=$row2['id']?>" class="button-link"><?=i18n("global", "no")?></a></p>
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