<?php
require("../core.php");
require("../lib/GoogleAuthenticator/GoogleAuthenticator.php");

$authenticator = new GoogleAuthenticator();

$return = array();

if (!isset($_POST["code"]) || strlen($_POST["code"]) != 6) {
	$return["errorCode"] = 1;
    die(json_encode($return));
}

$user_id = $_SESSION["prov_id"];

$secret = mysqli_real_escape_string($con, getsecret());
$code = mysqli_real_escape_string($con, $_POST["code"]);

if ($authenticator->checkCode($secret, $code)) {
	$_SESSION["id"] = $_SESSION["prov_id"];
	unset($_SESSION["prov_id"]);
	$return["status"] = "correct";
} else {
	unset($_SESSION["prov_id"]);
	$return["errorCode"] = 2;
}

echo json_encode($return);
?>