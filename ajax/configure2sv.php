<?php
require("../core.php");
require("../lib/GoogleAuthenticator/GoogleAuthenticator.php");

$authenticator = new GoogleAuthenticator();

$return = array();

if (!isset($_POST["secret"]) || !isset($_POST["code"]) || strlen($_POST["secret"]) != 32 || strlen($_POST["code"]) != 6) {
	$return["errorCode"] = 1;
    die(json_encode($return));
}

$user_id = $_SESSION["id"];

$secret = mysqli_real_escape_string($con, $_POST["secret"]);
$code = mysqli_real_escape_string($con, $_POST["code"]);

if ($authenticator->checkCode($secret, $code)) {
	$query = mysqli_query($con, "SELECT * FROM 2stepverification WHERE user_id = ".$_SESSION['id']) or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
	if (mysqli_num_rows($query)) {
		$sql = "UPDATE 2stepverification SET secret = '{$secret}' WHERE user_id = {$user_id} LIMIT 1";
	} else {
		$sql = "INSERT INTO 2stepverification (user_id, enabled, secret) values ({$user_id}, 1, '{$secret}')";
	}
	if (mysqli_query($con, $sql)) {
		$return["status"] = "correct";
	} else {
		$return["errorCode"] = 3;
		$return["errorDetail"] = "Ha habido un error añadiendo tu llave de seguridad en la base de datos: ".mysqli_error($con);
	}
} else {
	$return["errorCode"] = 2;
}

echo json_encode($return);
?>