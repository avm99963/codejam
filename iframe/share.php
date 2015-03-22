<?php
require_once("../core.php");
if (getrole()) {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/share_iframe.css">
		<script src="../lib/jquery/jquery.min.js"></script>
		<script src="../js/share_iframe.js"></script>
		<script src="../lib/selectize/selectize.js"></script>
		<link rel="stylesheet" type="text/css" href="../lib/selectize/selectize.default.css">
	</head>
	<body>
		<?php
		if (!isset($_GET["contest"]))
            die("Esta competición no existe");

        $id = (INT)$_GET["contest"];
		$query = mysqli_query($con, "SELECT * FROM contests WHERE id = {$id}");

		if (!mysqli_num_rows($query))
			die("Esta competición no existe");

		$row = mysqli_fetch_assoc($query);
        ?>
        <script>
        var contest = <?=$id?>;
        </script>
		<h3>Invitar nuevos concursantes</h3>
		<div id="sharing-box">
			<h4 id="people">Quién es concursante</h4>
			<?php
			$query2 = mysqli_query($con, "SELECT * FROM invitations WHERE contest = {$id}");
			if (mysqli_num_rows($query2)) {
				?>
				<p id="nocontestants" hidden>No hay concursantes</p>
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
				<p id="nocontestants">No hay concursantes</p>
				<table id="contestants" hidden></table>
				<?php
			}
			?>
			<h4 id="invitepeople">Invitar concursantes</h4>
			<input type="text" id="invite_textbox"></select>
			<p><button id="done" class="g-button g-button-submit">Listo</button></p>
		</div>
	</body>
</html>
<?php
} else {
  header('HTTP/1.0 404 Not Found');
}
?>