<?php
require("../core.php");
require("../lib/u2flib_server/loadU2F.php");

$return = array();

if (!isset($_POST["data"]) || !isset($_POST["req"]) || empty($_POST["data"]) || empty($_POST["req"])) {
	die("{ 'errorCode': 3, 'errorDetail': 'No se ha pasado toda la información necesaria.' }");
}

try {
	$reg = $u2f->doRegister(json_decode($_POST['req']), json_decode($_POST['data']));
	$user_id = $_SESSION["id"];
	$keyHandle = mysqli_real_escape_string($con, $reg->keyHandle);
	$publicKey = mysqli_real_escape_string($con, $reg->publicKey);
	$certificate = mysqli_real_escape_string($con, $reg->certificate);
	$counter = mysqli_real_escape_string($con, $reg->counter);
	$dateadded = mysqli_real_escape_string($con, time());
	$deviceadded = mysqli_real_escape_string($con, get_ip_address());
	$sql = "INSERT INTO securitykeys (user_id, keyHandle, publicKey, certificate, counter, dateadded, deviceadded) values ({$user_id}, '{$keyHandle}', '{$publicKey}', '{$certificate}', '{$counter}', '{$dateadded}', '{$deviceadded}')";
	if (mysqli_query($con, $sql)) {
		$return["status"] = "correct";
	} else {
		$return["errorCode"] = 2;
		$return["errorDetail"] = "Ha habido un error añadiendo tu llave de seguridad en la base de datos: ".mysqli_error($con);
	}
} catch( Exception $e ) {
    $return["errorCode"] = 1;
    $return["errorDetail"] = $e;
}

echo json_encode($return);
?>