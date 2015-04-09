<?php
require_once("core.php");
if (getrole()) {
	if (!isset($_GET["id"]) || !isset($_GET["judge"])) {
		die ("1: No se han enviado todos los argumentos");
	}

	if (!in_array($_GET["judge"], array(2, 1, 0))) {
		die("2: No es válido el argumento judge");
	}

	$id = (INT)$_GET["id"];
	$judge = (INT)$_GET["judge"];

	$query = mysqli_query($con, "SELECT * FROM submissions WHERE id = {$id}");

	if (!mysqli_num_rows($query)) {
		die ("3: No existe esta respuesta");
	}

	$row = mysqli_fetch_assoc($query);

	if ($judge == 2) {
		$judge = "null";
	}

	if (mysqli_query($con, "UPDATE submissions SET judged = {$judge} WHERE id = {$id}")) {
		header("Location: judgecontest.php?id=".$row["contest"]);
	} else {
		die("4: Ha ocurrido un error mientras se juzgaba la respuesta");
	}
} else {
  header('HTTP/1.0 404 Not Found');
}
?>