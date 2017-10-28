<?php
require("../core.php");
require("../lib/u2flib_server/U2F.php");

$u2f = new u2flib_server\U2F((isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['HTTP_HOST']);

$return = array();

if (!isset($_POST["data"]) || !isset($_POST["req"]) || empty($_POST["data"]) || empty($_POST["req"])) {
	die("{ 'errorCode': 3, 'errorDetail': 'No se ha pasado toda la información necesaria.' }");
}

try {
	$query = mysqli_query($con, "SELECT * FROM securitykeys WHERE user_id = '".$_SESSION['prov_id']."'") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
    $row = array();
    if (mysqli_num_rows($query)) {
        while ($row[] = mysqli_fetch_assoc($query)) {}
        array_pop($row);
        foreach ($row as $key => $value) {
            $row[$key] = json_decode(json_encode($value));
        }
    } else {
        die("{ 'errorCode': 4, 'errorDetail': 'No existen llaves de seguridad vinculadas a esta cuenta.' }");
    }
	$reg = $u2f->doAuthenticate(json_decode($_POST['req']), $row, json_decode($_POST['data']));
	$user_id = $_SESSION["prov_id"];
	$keyHandle = mysqli_real_escape_string($con, $reg->keyHandle);
	$counter = mysqli_real_escape_string($con, $reg->counter);
	$lastuseddate = mysqli_real_escape_string($con, time());
	$lastuseddevice = mysqli_real_escape_string($con, get_ip_address());
	$sql = "UPDATE securitykeys SET counter = '{$counter}', lastuseddate = '{$lastuseddate}', lastuseddevice = '{$lastuseddevice}' WHERE keyHandle = '{$keyHandle}'";
	if (mysqli_query($con, $sql)) {
		$_SESSION["id"] = $_SESSION["prov_id"];
		unset($_SESSION["prov_id"]);
		$return["status"] = "correct";
	} else {
		$return["errorCode"] = 2;
		$return["errorDetail"] = "Ha habido un error actualizando el contador de la base de datos: ".mysqli_error($con);
	}
} catch( Exception $e ) {
    $return["errorCode"] = 1;
    $return["errorDetail"] = $e;
}

echo json_encode($return);
?>
