<?php
require_once("core.php");
require_once("contest_helper.php");
initi18n("leaderboard");
?>
<!DOCTYPE html>
<html>
<head>
	<?php require ("head.php"); ?>
	<title><?=i18n("leaderboard", "title")?> - <?php echo $appname; ?></title>
	<style>
	table {
		width: 100%;
		font-family: 'Arial', sans-serif;
		font-size: 13px;
		border-collapse: collapse;
		border-top: 0.1em solid #c3d9ff;
		border-bottom: 0.1em solid #c3d9ff;
	}
	thead {
		border-bottom: 1px solid #cccccc;
	}
	thead tr {
		text-align: left;
	}
	thead tr:first-child {
		text-align: center!important;
	}
	thead th {
		font-weight: normal!important;
		padding-left: 0.5em;
	}
	tbody tr:nth-child(odd) {
		background-color: #efefef;
	}
	tbody td {
		vertical-align: top;
		padding: 0.5em 0 0.3em 0.5em;
	}
	tbody td:nth-child(n+5) {
		line-height: 15px;
	}
	tbody td img, tbody td span {
		vertical-align: middle;
	}
	</style>
</head>
<body>
<div class="content">
	<?php include "nav.php"; ?>
	<article>
		<?php
		if (!loggedin()) {
			die(i18n("global", "notloggedin"));
		}
		?>
		<?php anuncio(); ?>
		<?php if (getrole() > 0) {
			require("sidebar.php");
		?>
		<div class="text right large">
		<?php } else { ?>
		<div class="text">
		<?php }
		if (!isset($_GET["id"])) {
			die ("<div class='alert-danger'>No contest... What?!</div>");
		}
		$id = (INT)$_GET["id"];
		$query = mysqli_query($con, "SELECT * FROM contests WHERE id = '".$id."'");
		if (!mysqli_num_rows($query)) {
			die ("<div class='alert-danger'>Hey, this contest doesn't exist!</div>");
		}
		$row = mysqli_fetch_assoc($query);

		if (!getrole() && $row["privacy"] == 0) { // TODO: Know if person is invited to contest and let them see leaderboard or not
			if (!isinvited($id)) {
				die("<div class='alert-danger'>".i18n("leaderboard", "notinvited")."</div>");
			}
		}

		$now = time();

		$query2 = mysqli_query($con, "SELECT id, name, io FROM problems WHERE contest = ".$id." ORDER BY num");

		if (!mysqli_num_rows($query2)) {
			die ("<div class='alert-danger'>".i18n("leaderboard", "noproblems")."</div>");
		}

		if ($now < $row["starttime"] && getrole() == 0) {
			die ("<div class='alert-danger'>".i18n("leaderboard", "notstarted")."</div>");
		}

		$problems = array();

		for ($i = 0; $i < mysqli_num_rows($query2); $i++) {
			$problem = mysqli_fetch_assoc($query2);
			$problems[$problem["id"]] = $problem;
		}

		if (getrole() && isset($_GET["tellmethetruth"]) && $_GET["tellmethetruth"] == 1) {
			$leaderboard = leaderboard($row["id"], false, true);
		} else {
			$leaderboard = leaderboard($row["id"]);
		}
		?>
			<h2><?=i18n("leaderboard", "title")?> – <?=$row["name"]?></h2>
			<table>
				<thead>
					<tr>
						<th colspan="4"></th>
						<?php
						foreach ($problems as $problem) {
							echo '<th colspan="2">'.$problem["name"].'</th>';
						}
						?>
					</tr>
					<tr>
						<th><?=i18n("leaderboard", "rank")?></th>
						<th><?=i18n("leaderboard", "contestant")?></th>
						<th><?=i18n("leaderboard", "score")?></th>
						<th><?=i18n("leaderboard", "time")?></th>
						<?php
						foreach ($problems as $problem) {
							$io = json_decode($problem["io"], true);
							echo '<th>'.$io["pts"]["small"].i18n("leaderboard", "points_abbr").'</th><th>'.$io["pts"]["large"].i18n("leaderboard", "points_abbr").'</th>';
						}
						?>
					</tr>
				</thead>
				<tbody>
				<?php
				if ($leaderboard === false) {
					?>
				</tbody>
			</table>
			<p style="text-align: center; font-size: 13px;"><?=i18n("leaderboard", "noresults")?></p>
					<?php
					exit;
				}
				if (isset($_GET["imgeeky"]) && $_GET["imgeeky"] == 1) {
					var_dump($leaderboard);
					exit;
				}
				$i = 0;
				foreach ($leaderboard as $leader) {
					$i++;
					?>
					<tr>
						<td><?=$i?></td>
						<td><?=userdata("username", $leader["user_id"])?></td>
						<td><?=$leader["score"]?></td>
						<td><?=format_time($leader["time"])?></td>
						<?php
						foreach ($leader["submissions"] as $submission) {
							foreach ($submission as $type => $solution) {
								if ($solution["status"] == "correct") {
									if ($type == "small" && $solution["count"] > 1) {
				        				$add = '<br><span style="color: black;">'.($solution["count"] - 1)." ".(($solution["count"] - 1 == 1) ? i18n("leaderboard", "wrongtry") : i18n("leaderboard", "wrongtries")).'</span>';
				        			} else {
				        				$add = '';
				        			}
				        			echo '<td style="color: #006600;"><img src="img/checkmark.png"> <span>'.format_time($solution["penalty"]).'</span>'.$add.'</td>';
								} elseif ($solution["status"] == "incorrect") {
									if ($type == "large") {
										echo '<td>--<br>1 '.i18n("leaderboard", "wrongtry").'</td>';
									} else {
										echo '<td>--<br>'.$solution["count"]." ".(($solution["count"] == 1) ? i18n("leaderboard", "wrongtry") : i18n("leaderboard", "wrongtries")).'</td>';
									}
								} elseif ($solution["status"] == "notattempted") {
									echo '<td>--</td>';
								} elseif ($solution["status"] == "submitted") {
									echo '<td style="color: #006600;"><img src="img/question.png"> <span>'.format_time($solution["penalty"]).'</span></td>';
								} elseif ($solution["status"] == "timeexpired") {
									echo '<td>'.i18n("leaderboard", "timeexpired").'</td>';
								}
							}
						}
						?>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			<p style="font-size: 12px; text-align: right;"><?=i18n("leaderboard", "updated")?>: <?=date("d/m/Y H:i:s", time())?><?php if (getrole() && $now < $row["endtime"]) { ?> | <?php if (isset($_GET["tellmethetruth"]) && $_GET["tellmethetruth"] == 1) { ?><a href="leaderboard.php?id=<?=$_GET["id"]?>"><?=i18n("leaderboard", "donttellmethetruth")?></a><?php } else { ?><a href="leaderboard.php?id=<?=$_GET["id"]?>&tellmethetruth=1"><?=i18n("leaderboard", "tellmethetruth")?></a><?php } } ?></p>
		</div>
	</article>
</div>
</body>
</html>