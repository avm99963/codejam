<?php
require_once("core.php");
require_once("contest_helper.php");
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "newsuccess")
  $msg = '<p class="alert-success">Competición creada correctamente</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "deletesuccessful")
  $msg = '<p class="alert-success">Competición eliminada correctamente</p>';
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title>Competiciones - <?php echo $appname; ?></title>
</head>
<body>
<div class="content">
	<?php include "nav.php"; ?>
	<article>
		<?php
		if (!loggedin())
		{
			die ("<div class='alert-danger'><p>¡No estás connectado! <a href='index.php'>Conéctate</a></p></div>");
		}
		?>
		<?php anuncio(); ?>
		<?php if (getrole() > 0) {
			require("sidebar.php");
		?>
		<div class="text right large">
		<?=$msg?>
		<?php } else { ?>
		<div class="text">
		<?php } ?>
			<?php
			if (getrole() > 0) {
				$query = mysqli_query($con, "SELECT * FROM contests");
				if (mysqli_num_rows($query)) {
					$output = array();
					while ($row = mysqli_fetch_assoc($query)) {
						if ($row["privacy"] == 2) {
							$img = "public";
						} elseif ($row["privacy"] == 1) {
							$img = "lock_open";
						} elseif ($row["privacy"] == 0) {
							$img = "lock";
						}
						$output[] = "<h2><span class='icon svg-ic_".$img."_24px'></span> ".$row["name"]."</h2><p>".$row["description"]."</p><p class='padding10'><a href='contest.php?id=".$row["id"]."'><span class='icon svg-ic_open_in_browser_24px'></span></a> <a href='contest.php?id=".$row["id"]."'>Abrir el panel de competición</a><br><a href='leaderboard.php?id=".$row["id"]."'><span class='icon svg-ic_format_list_numbered_24px'></span></a> <a href='leaderboard.php?id=".$row["id"]."'>Ver la clasificación</a><br><a href='admincontest.php?id=".$row["id"]."'><span class='icon svg-ic_mode_edit_24px'></span></a> <a href='admincontest.php?id=".$row["id"]."'>Administrar la competición</a></p>";
					}
					echo implode("<hr>", $output);
				} else {
					echo "<p style='text-align:center;'>No hay competiciones disponibles. <a href='newcontest.php'>Crea una</a></p>";
				}
			} else {
				?>
				<h2><span style="border-bottom: 1px solid #BDBDBD; padding-bottom: 5px;"><span class='icon svg-ic_public_24px'></span> <span style="vertical-align:middle;">Competiciones públicas</span></span></h2>
				<?php
				$query = mysqli_query($con, "SELECT * FROM contests WHERE privacy = 2");
				if (mysqli_num_rows($query)) {
					$output = array();
					while ($row = mysqli_fetch_assoc($query)) {
						$output[] = "<h3>".$row["name"]."</h3><p>".$row["description"]."</p><p class='padding10'><a href='contest.php?id=".$row["id"]."'><span class='icon svg-ic_open_in_browser_24px'></span></a> <a href='contest.php?id=".$row["id"]."'>Abrir el panel de competición</a><br><a href='leaderboard.php?id=".$row["id"]."'><span class='icon svg-ic_format_list_numbered_24px'></span></a> <a href='leaderboard.php?id=".$row["id"]."'>Ver la clasificación</a></p>";
					}
					echo implode("", $output);
				} else {
					echo "<p style='text-align:center;'>No hay competiciones públicas disponibles.</p>";
				}
				?>
				<hr><h2><span style="border-bottom: 1px solid #BDBDBD; padding-bottom: 5px;"><span class='icon svg-ic_lock_open_24px'></span> <span style="vertical-align:middle;">Competiciones semiprivadas</span></span></h2>
				<?php
				$query3 = mysqli_query($con, "SELECT * FROM contests WHERE privacy = 1"); // TODO: Adapt query when inviting contestants is implemented
				if (mysqli_num_rows($query3)) {
					$output = array();
					while ($row = mysqli_fetch_assoc($query3)) {
						$output[] = "<h3>".$row["name"]."</h3><p>".$row["description"]."</p><p class='padding10'>".(isinvited($row["id"]) ? "<a href='contest.php?id=".$row["id"]."'><span class='icon svg-ic_open_in_browser_24px'></span></a> <a href='contest.php?id=".$row["id"]."'>Abrir el panel de competición</a><br>" : "")."<a href='leaderboard.php?id=".$row["id"]."'><span class='icon svg-ic_format_list_numbered_24px'></span></a> <a href='leaderboard.php?id=".$row["id"]."'>Ver la clasificación</a></p>";
					}
					echo implode("", $output);
				} else {
					echo "<p style='text-align:center;'>No hay ninguna competición semiprivada.</p>";
				}
				?>
				<hr><h2><span style="border-bottom: 1px solid #BDBDBD; padding-bottom: 5px;"><span class='icon svg-ic_lock_24px'></span> <span style="vertical-align:middle;">Competiciones privadas</span></span></h2>
				<?php
				$query2 = mysqli_query($con, "SELECT * FROM contests WHERE privacy = 0");
				if (mysqli_num_rows($query2)) {
					$output = array();
					while ($row = mysqli_fetch_assoc($query2)) {
						if (isinvited($row["id"])) {
							$output[] = "<h3>".$row["name"]."</h3><p>".$row["description"]."</p><p class='padding10'><a href='contest.php?id=".$row["id"]."'><span class='icon svg-ic_open_in_browser_24px'></span></a> <a href='contest.php?id=".$row["id"]."'>Abrir el panel de competición</a><br><a href='leaderboard.php?id=".$row["id"]."'><span class='icon svg-ic_format_list_numbered_24px'></span></a> <a href='leaderboard.php?id=".$row["id"]."'>Ver la clasificación</a></p>";
						}
					}
					if (!count($output)) {
						echo "<p style='text-align:center;'>No has sido invitado a ninguna competición privada.</p>";
					} else {
						echo implode("", $output);
					}
				} else {
					echo "<p style='text-align:center;'>No has sido invitado a ninguna competición privada.</p>";
				}
			}
			?>
		</div>
	</article>
</div>
</body>
</html>