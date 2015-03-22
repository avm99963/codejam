<?php
require_once("../core.php");
require_once("../contest_helper.php");

$return = array();

if (!isset($_POST["problem"]) || !isset($_POST["type"])) {
	$return["errorCode"] = 1;
	$return["errorText"] = "No se ha indicado el problema o el tipo";
	echo json_encode($return);
	exit;
}

$problem = (INT)$_POST["problem"];
$type = mysqli_real_escape_string($con, $_POST["type"]);

if (!in_array($type, array("small", "large"))) {
	$return["errorCode"] = 7;
	$return["errorText"] = "Se ha especificado un tipo incorrecto";
	echo json_encode($return);
	exit;
}

$now = time();

$query2 = mysqli_query($con, "SELECT * FROM problems WHERE id = ".$problem);

if (!mysqli_num_rows($query2)) {
	$return["errorCode"] = 5;
	$return["errorText"] = "Este problema no existe";
	echo json_encode($return);
	exit;
}

$row2 = mysqli_fetch_assoc($query2);

$query = mysqli_query($con, "SELECT * FROM contests WHERE id = ".$row2["contest"]." LIMIT 1");

if (!mysqli_num_rows($query)) {
	$return["errorCode"] = 2;
	$return["errorText"] = "Esta competición no existe";
	echo json_encode($return);
	exit;
}

if (!isinvited($row2["contest"])) {
	$return["errorCode"] = 11;
	$return["errorText"] = "No estás invitado a esta competición";
	echo json_encode($return);
	exit;
}

$row = mysqli_fetch_assoc($query);

if ($now < $row["starttime"]) {
	$return["errorCode"] = 3;
	$return["errorText"] = "Todavía no ha empezado la competición";
	echo json_encode($return);
	exit;
}

if ($now > $row["endtime"]) {
	$return["errorCode"] = 4;
	$return["errorText"] = "Ya ha terminado la competición";
	echo json_encode($return);
	exit;
}

$query3 = mysqli_query($con, "SELECT * FROM submissions WHERE problem = ".$problem." AND user_id = ".userdata("id")." AND type = ".(($type == "small") ? 0 : 1)." ORDER BY try DESC");

if ($type == "small") {
	$try = 1;
} else {
	$try = "NULL";
}

if (mysqli_num_rows($query3)) {
	if ($type == "large") {
		$return["errorCode"] = 6;
		$return["errorText"] = "Ya tienes o has tenido un contador activo para el tipo large de este problema";
		echo json_encode($return);
		exit;
	} elseif (mysqli_num_rows($query3) >= 3) {
		$return["errorCode"] = 8;
		$return["errorText"] = "Ya has hecho 3 intentos para el tipo small de este problema";
		echo json_encode($return);
		exit;
	} else {
		$try = mysqli_num_rows($query3) + 1;
		$row3 = mysqli_fetch_assoc($query3);
		if ($row3["valid"] == 1) {
			$return["errorCode"] = 10;
			$return["errorText"] = "Ya has solucionado correctamente este problema";
			echo json_encode($return);
			exit;
		}
	}
}

$fileshorthand = convertfileshorthand($type, "in", $try);

if (mysqli_query($con, "INSERT INTO submissions (user_id, contest, problem, type, try, time) VALUES (".(INT)$_SESSION["id"].", ".$row2["contest"].", ".$problem.", ".(($type == "small") ? 0 : 1).", ".$try.", ".$now.")")) {
	$return["starttime"] = $now;
	$return["endtime"] = $now + (($type == "small") ? 4 : 8) * 60;
	$return["problem"] = $problem;
	$return["type"] = $type;
	$return["try"] = $try;
	$return["inputfilename"] = getfilename($problem, $fileshorthand);
	$return["inputurl"] = "download.php?problem=".$problem."&type=".$fileshorthand;
} else {
	$return["errorCode"] = 9;
	$return["errorText"] = "No se ha podido contactar con la base de datos";
	echo json_encode($return);
	exit;
}

echo json_encode($return);
?>