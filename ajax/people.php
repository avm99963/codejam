<?php
require_once("../core.php");

$return = array();

if (!getrole()) {
	$return["errorCode"] = 1;
	$return["errorText"] = "No tienes los suficientes privilegios para realizar esta acción";
	echo json_encode($return);
	exit;
}

if (!isset($_GET["q"]) || empty($_GET["q"])) {
	$return["errorCode"] = 2;
	$return["errorText"] = "Por favor, especifica un término de búsqueda";
	echo json_encode($return);
	exit;
}

$q = mysqli_real_escape_string($con, $_GET["q"]);

if (isset($_GET["exclude"]) && !empty($_GET["exclude"])) {
	$exclude = json_decode($_GET["exclude"], true);
} else {
	$exclude = array();
}

$query = mysqli_query($con, "SELECT id, username, name, surname, email FROM users WHERE username LIKE '%{$q}%' OR name LIKE '%{$q}%' OR surname LIKE '%{$q}%' OR email LIKE '%{$q}%'");

$rows = mysqli_num_rows($query);

if ($rows) {
	$counter = 0;
	for ($i = 0; $i < $rows; $i++) {
		$row = mysqli_fetch_assoc($query);
		if (!in_array($row["id"], $exclude)) {
			$return[$counter] = $row;
			$counter++;
		}
	}
}

echo json_encode($return);
?>