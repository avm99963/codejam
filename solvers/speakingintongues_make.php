<?php
// St. Paul's Code Jam – Speaking in Tongues problem maker
// @Author: avm99963
if (isset($_POST["text"]) && !empty($_POST["text"])) {
	header("Content-Type: text/plain");

	$input = $_POST["text"];

	$healthy = array("y", "e", "q", "j", "p", "m", "s", "l", "c", "k", "d", "x", "v", "n", "r", "i", "b", "t", "a", "h", "w", "f", "o", "u", "g", "z");
	$yummy   = array("A", "O", "Z", "U", "R", "L", "N", "G", "E", "I", "S", "M", "P", "B", "T", "D", "H", "W", "Y", "X", "F", "C", "K", "J", "V", "Q");

	$healthy2 = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
	$yummy2   = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

	$cases = explode("\n", $input);

	echo count($cases)."\n";

	foreach ($cases as $n => $case) {
		if (strlen($case) > 100) {
			die ("Case #".$n." has a phrase with more than 100 characters.");
		}
		echo str_replace($yummy, $healthy, str_replace($yummy2, $healthy2, $case))."\n";
	}
} else {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Speaking in Tongues – Problem Maker</title>
		<style>
		body {
			font-family: "Helvetica Neue", "Arial", sans-serif;
		}
		textarea {
			width: 600px;
			height: 350px;
		}
		</style>
	</head>
	<body>
		<h3>Speaking in Tongues – Problem Maker</h3>
		<form method="POST">
			<div><textarea name="text" placeholder="text"></textarea></div>
			<p><input type="submit" value="Create"></p>
		</form>
	</body>
</html>
<?php
}
?>