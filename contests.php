<?php
require_once("core.php");
require_once("contest_helper.php");
initi18n("contests");
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
<title><?=i18n("contests", "title")?> - <?php echo $appname; ?></title>
</head>
<body>
<div class="content">
	<?php include "nav.php"; ?>
	<article>
		<?php
		if (!loggedin())
		{
			die ("<div class='alert-danger'>".i18n("global", "notloggedin")."</div>");
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
						$output[] = "<h2><span class='icon svg-ic_".$img."_24px'></span> ".$row["name"]."</h2><p>".nl2br($row["description"], false)."</p><p class='padding10'><span class='icon svg-ic_today_24px'></span> <span style='color: #555;'>".date("j M Y H:i", $row["starttime"])." - ".date("j M Y H:i (e)", $row["endtime"])."</span><br><a href='contest.php?id=".$row["id"]."'><span class='icon svg-ic_open_in_browser_24px'></span></a> <a href='contest.php?id=".$row["id"]."'>".i18n("contests", "dashboard")."</a><br><a href='leaderboard.php?id=".$row["id"]."'><span class='icon svg-ic_format_list_numbered_24px'></span></a> <a href='leaderboard.php?id=".$row["id"]."'>".i18n("contests", "leaderboard")."</a><br><a href='admincontest.php?id=".$row["id"]."'><span class='icon svg-ic_mode_edit_24px'></span></a> <a href='admincontest.php?id=".$row["id"]."'>".i18n("contests", "admin")."</a></p>";
					}
					echo implode("<hr>", $output);
				} else {
					echo "<p style='text-align:center;'>".i18n("contests", "nocontests")."</p>";
				}
			} else {
				?>
				<h2><span style="border-bottom: 1px solid #BDBDBD; padding-bottom: 5px;"><span class='icon svg-ic_public_24px'></span> <span style="vertical-align:middle;"><?=i18n("contests", "publiccontests")?></span></span></h2>
				<?php
				$query = mysqli_query($con, "SELECT * FROM contests WHERE privacy = 2");
				if (mysqli_num_rows($query)) {
					$output = array();
					while ($row = mysqli_fetch_assoc($query)) {
						$output[] = "<h3>".$row["name"]."</h3><p>".nl2br($row["description"], false)."</p><p class='padding10'><span class='icon svg-ic_today_24px'></span> <span style='color: #555;'>".date("j M Y H:i", $row["starttime"])." - ".date("j M Y H:i (e)", $row["endtime"])."</span><br><a href='contest.php?id=".$row["id"]."'><span class='icon svg-ic_open_in_browser_24px'></span></a> <a href='contest.php?id=".$row["id"]."'>".i18n("contests", "dashboard")."</a><br><a href='leaderboard.php?id=".$row["id"]."'><span class='icon svg-ic_format_list_numbered_24px'></span></a> <a href='leaderboard.php?id=".$row["id"]."'>".i18n("contests", "leaderboard")."</a></p>";
					}
					echo implode("", $output);
				} else {
					echo "<p style='text-align:center;'>".i18n("contests", "nopublic")."</p>";
				}
				?>
				<hr><h2><span style="border-bottom: 1px solid #BDBDBD; padding-bottom: 5px;"><span class='icon svg-ic_lock_open_24px'></span> <span style="vertical-align:middle;"><?=i18n("contests", "semiprivatecontests")?></span></span></h2>
				<?php
				$query3 = mysqli_query($con, "SELECT * FROM contests WHERE privacy = 1"); // TODO: Adapt query when inviting contestants is implemented
				if (mysqli_num_rows($query3)) {
					$output = array();
					while ($row = mysqli_fetch_assoc($query3)) {
						$output[] = "<h3>".$row["name"]."</h3><p>".nl2br($row["description"], false)."</p><p class='padding10'><span class='icon svg-ic_today_24px'></span> <span style='color: #555;'>".date("j M Y H:i", $row["starttime"])." - ".date("j M Y H:i (e)", $row["endtime"])."</span><br>".(isinvited($row["id"]) ? "<a href='contest.php?id=".$row["id"]."'><span class='icon svg-ic_open_in_browser_24px'></span></a> <a href='contest.php?id=".$row["id"]."'>".i18n("contests", "dashboard")."</a><br>" : "")."<a href='leaderboard.php?id=".$row["id"]."'><span class='icon svg-ic_format_list_numbered_24px'></span></a> <a href='leaderboard.php?id=".$row["id"]."'>".i18n("contests", "leaderboard")."</a></p>";
					}
					echo implode("", $output);
				} else {
					echo "<p style='text-align:center;'>".i18n("contests", "nosemiprivate")."</p>";
				}
				?>
				<hr><h2><span style="border-bottom: 1px solid #BDBDBD; padding-bottom: 5px;"><span class='icon svg-ic_lock_24px'></span> <span style="vertical-align:middle;"><?=i18n("contests", "privatecontests")?></span></span></h2>
				<?php
				$query2 = mysqli_query($con, "SELECT * FROM contests WHERE privacy = 0");
				if (mysqli_num_rows($query2)) {
					$output = array();
					while ($row = mysqli_fetch_assoc($query2)) {
						if (isinvited($row["id"])) {
							$output[] = "<h3>".$row["name"]."</h3><p>".nl2br($row["description"], false)."</p><p class='padding10'><span class='icon svg-ic_today_24px'></span> <span style='color: #555;'>".date("j M Y H:i", $row["starttime"])." - ".date("j M Y H:i (e)", $row["endtime"])."</span><br><a href='contest.php?id=".$row["id"]."'><span class='icon svg-ic_open_in_browser_24px'></span></a> <a href='contest.php?id=".$row["id"]."'>".i18n("contests", "dashboard")."</a><br><a href='leaderboard.php?id=".$row["id"]."'><span class='icon svg-ic_format_list_numbered_24px'></span></a> <a href='leaderboard.php?id=".$row["id"]."'>".i18n("contests", "leaderboard")."</a></p>";
						}
					}
					if (!count($output)) {
						echo "<p style='text-align:center;'>".i18n("contests", "noprivate")."</p>";
					} else {
						echo implode("", $output);
					}
				} else {
					echo "<p style='text-align:center;'>".i18n("contests", "noprivate")."</p>";
				}
			}
			?>
		</div>
	</article>
</div>
</body>
</html>