<?php
require("../core.php");
require("../contest_helper.php");
initi18n("conteststats", 1);

$return = array();

if (!isset($_POST["contest"])) {
	$return["errorCode"] = 1;
	$return["errorText"] = i18n("conteststats", "error1");
	echo json_encode($return);
	exit;
}

$contest = (INT)$_POST["contest"];

$query = mysqli_query($con, "SELECT * FROM contests WHERE id = ".$contest." LIMIT 1");

if (!mysqli_num_rows($query)) {
	$return["errorCode"] = 2;
	$return["errorText"] = i18n("conteststats", "error2");
	echo json_encode($return);
	exit;
}

$row = mysqli_fetch_assoc($query);

if (!isinvited($contest)) {
	$return["errorCode"] = 5;
	$return["errorText"] = i18n("conteststats", "error5");
	echo json_encode($return);
	exit;
}

$now = time();

if ($now < $row["starttime"]) {
	$return["errorCode"] = 3;
	$return["errorText"] = i18n("conteststats", "error3");
	echo json_encode($return);
	exit;
}

$query2 = mysqli_query($con, "SELECT * FROM problems WHERE contest = ".$contest);

if (!mysqli_num_rows($query2)) {
	$return["errorCode"] = 4;
	$return["errorText"] = i18n("conteststats", "error4");
	echo json_encode($return);
	exit;
}

$problems = array();

for ($i = 0; $i < mysqli_num_rows($query2); $i++) {
	$problems[$i] = mysqli_fetch_assoc($query2);
}

$leaderboard = leaderboard($contest, true);

$return["topscores"] = array();

$i = 0;
if ($leaderboard !== false) {
	foreach ($leaderboard as $leader) {
		$return["topscores"][$i] = array(
			"contestant" => userdata("username", $leader["user_id"]),
			"score" => $leader["score"]
		);
		$i++;
	}
}

$return["rank"] = rank($contest, $_SESSION["id"]);
$return["score"] = score($contest);

$return["submissions"] = submissions($contest);

echo json_encode($return);
?>