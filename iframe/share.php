<?php
require_once("../core.php");
initi18n("share", 1);
if (getrole()) {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/share_iframe.css">
		<script src="../lib/jquery/jquery.min.js"></script>
		<?php initi18n_js("share_iframe_js", 1); ?>
		<script src="../js/share_iframe.js"></script>
		<script src="../lib/selectize/selectize.js"></script>
		<link rel="stylesheet" type="text/css" href="../lib/selectize/selectize.default.css">
	</head>
	<body>
		<?php
		if (!isset($_GET["contest"]))
            die(i18n("share", "contestdoesntexist"));

        $id = (INT)$_GET["contest"];
		$query = mysqli_query($con, "SELECT * FROM contests WHERE id = {$id}");

		if (!mysqli_num_rows($query))
			die(i18n("share", "contestdoesntexist"));

		$row = mysqli_fetch_assoc($query);
        ?>
        <script>
        var contest = <?=$id?>;
        </script>
		<h3><?=i18n("share", "title")?></h3>
		<div id="sharing-box">
			<h4 id="people"><?=i18n("share", "people")?></h4>
			<?php
			$query2 = mysqli_query($con, "SELECT * FROM invitations WHERE contest = {$id}");
			if (mysqli_num_rows($query2)) {
				?>
				<p id="nocontestants" hidden><?=i18n("share", "nocontestants")?></p>
				<table id="contestants">
				<?php
				while ($row2 = mysqli_fetch_assoc($query2)) {
					?>
					<tr data-id="<?=$row2["user_id"]?>">
						<td><?=userdata("username", $row2["user_id"])?></td>
						<td><span class='icon svg-ic_remove_circle_24px'></span></td>
					</tr>
					<?php
				}
				?>
				</table>
				<?php
			} else {
				?>
				<p id="nocontestants"><?=i18n("share", "nocontestants")?></p>
				<table id="contestants" hidden></table>
				<?php
			}
			?>
			<h4 id="invitepeople"><?=i18n("share", "invitepeople")?></h4>
			<input type="text" id="invite_textbox"></select>
			<p><button id="done" class="g-button g-button-submit"><?=i18n("share", "ok")?></button></p>
		</div>
	</body>
</html>
<?php
} else {
  header('HTTP/1.0 404 Not Found');
}
?>