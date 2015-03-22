<?php
require_once("../core.php");

$return = array();

if (!getrole()) {
	$return["errorCode"] = 1;
	$return["errorText"] = "No tienes los suficientes privilegios para realizar esta acción";
	echo json_encode($return);
	exit;
}

if (!isset($_POST["contest"]) || empty($_POST["contest"]) || !isset($_POST["contestant"]) || empty($_POST["contestant"])) {
	$return["errorCode"] = 2;
	$return["errorText"] = "No se han enviado todos los argumentos";
	echo json_encode($return);
	exit;
}

$contest = (INT)$_POST["contest"];
$contestant = (INT)$_POST["contestant"];

$query = mysqli_query($con, "SELECT * FROM invitations WHERE contest = {$contest} AND user_id = {$contestant}");

if (!mysqli_num_rows($query)) {
	$return["errorCode"] = 2;
	$return["errorText"] = "Este concursante no está invitado a este concurso";
	echo json_encode($return);
	exit;
}

$rows = mysqli_num_rows($query);

if (mysqli_query($con, "DELETE FROM invitations WHERE user_id = {$contestant} AND contest = {$contest} LIMIT 1")) {
	$return["status"] = "removed";
	$return["id"] = $contestant;
} else {
	$return["errorCode"] = 3;
	$return["errorText"] = "No se pudo eliminar al concursante";
	echo json_encode($return);
	exit;
}

echo json_encode($return);
?>