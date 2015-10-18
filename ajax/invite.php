<?php
require_once("../core.php");

initi18n("invite_errors", 1);

$return = array();

if (!getrole()) {
	$return["errorCode"] = 1;
	$return["errorText"] = i18n("invite_errors", "error_1");
	echo json_encode($return);
	exit;
}

if (!isset($_POST["people"]) || empty($_POST["people"]) || !isset($_POST["contest"]) || empty($_POST["contest"])) {
	$return["errorCode"] = 2;
	$return["errorText"] = i18n("invite_errors", "error_2");
	echo json_encode($return);
	exit;
}

$people = explode(",", mysqli_real_escape_string($con, $_POST["people"]));
$contest = (INT)$_POST["contest"];

if (isset($_POST["exclude"]) && !empty($_POST["exclude"])) {
	$exclude = json_decode($_POST["exclude"], true);
} else {
	$exclude = array();
}

$query = mysqli_query($con, "SELECT * FROM invitations WHERE contest = {$contest}");

$already_invited = array();

if (mysqli_num_rows($query)) {
	for ($i = 0; $i < mysqli_num_rows($query); $i++) {
		$row = mysqli_fetch_assoc($query);
		$already_invited[] = $row["user_id"];
	}
}

$rows = mysqli_num_rows($query);

foreach ($people as $person) {
	if (!in_array($person, $already_invited)) {
		if ($id == userdata("id", $person)) {
			if (!mysqli_query($con, "INSERT INTO invitations (user_id, contest) VALUES ({$id}, {$contest})")) {
				$return["errorCode"] = 3;
				$return["errorText"] = i18n("invite_errors", "error_3");
				echo json_encode($return);
				exit;
			}
		}
	}
}

$return["status"] = "invited";

echo json_encode($return);
?>