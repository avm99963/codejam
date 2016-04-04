<?php
require_once("core.php");
if (isadmin())
{
initi18n("deletecontest");
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title><?=i18n("deletecontest", "title")?> - <?php echo $appname; ?></title>
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
		<h1><?=i18n("deletecontest", "title")?></h1>
		<?php
		if (isset($_GET['sent']) && $_GET['sent'] == "1") {
			$problems = mysqli_query($con, "SELECT * FROM problems WHERE contest = ".(int)$_GET["id"]);

			if (mysqli_num_rows($problems)) {
				echo "<p class='alert-danger'>".i18n("deletecontest", "error_thereareproblems")." " . mysqli_error($con) . "</p>";
				exit();
			}

			$sql = "DELETE FROM problems WHERE contest = ".(INT)$_GET['id']."";
			if (mysqli_query($con, $sql)) {
				$sql2 = "DELETE FROM contests WHERE id = ".(INT)$_GET['id']." LIMIT 1";
				if (mysqli_query($con, $sql2)) {
					$sql3 = "DELETE FROM submissions WHERE contest = ".(INT)$_GET['id']."";
					if (mysqli_query($con, $sql3)) {
  						header("Location: contests.php?msg=deletesuccessful");
  					} else {
  						die ("<p class='alert-danger'>".i18n("deletecontest", "error_submissions")." " . mysqli_error($con) . "</p>");
  					}
 				} else {
					die ("<p class='alert-danger'>".i18n("deletecontest", "error_contest")." " . mysqli_error($con) . "</p>");
				}
			} else {
				die ("<p class='alert-danger'>".i18n("deletecontest", "error_problems")." " . mysqli_error($con) . "</p>");
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
		<p><?=i18n("global", "delete_areyousure")?></p>
		<p><a href="deletecontest.php?id=<?php echo $_GET['id'];?>&sent=1" class="button-link-red"><?=i18n("global", "yes")?></a> <a href="admincontest.php?id=<?=$_GET['id']?>" class="button-link"><?=i18n("global", "no")?></a></p>
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