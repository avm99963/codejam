<?php
require_once("../core.php");
require_once("../contest_helper.php");
initi18n("submitsolution", 1);

$return = array();

if (!isset($_POST["contest"]) || !isset($_POST["problem"]) || !isset($_FILES["output"])) {
	$return["errorCode"] = 1;
	$return["errorText"] = i18n("submitsolution", "error1");
	echo json_encode($return);
	exit;
}

if($_FILES["output"]["size"] >= 200*1024) {
	$return["errorCode"] = 15;
	$return["errorText"] = i18n("submitsolution", "error15");
	echo json_encode($return);
	exit;
}

$contest = (INT)$_POST["contest"];
$problem = (INT)$_POST["problem"];
$type = mysqli_real_escape_string($con, $_POST["type"]);

$now = time();

$query = mysqli_query($con, "SELECT * FROM contests WHERE id = ".$contest." LIMIT 1");

if (!mysqli_num_rows($query)) {
	$return["errorCode"] = 2;
	$return["errorText"] = i18n("submitsolution", "error2");
	echo json_encode($return);
	exit;
}

if (!isinvited($contest)) {
	$return["errorCode"] = 18;
	$return["errorText"] = i18n("submitsolution", "error18");
	echo json_encode($return);
	exit;
}

$row = mysqli_fetch_assoc($query);

if ($now < $row["starttime"]) {
	$return["errorCode"] = 3;
	$return["errorText"] = i18n("submitsolution", "error3");
	echo json_encode($return);
	exit;
}

if ($_POST["competitionhasendedyey"] == 1 && $now > $row["endtime"]) {
	$competitionhasendedyey = true;
} else {
	$competitionhasendedyey = false;
}

if ($now > $row["endtime"] && $competitionhasendedyey === false) {
	$return["errorCode"] = 4;
	$return["errorText"] = i18n("submitsolution", "error4");
	echo json_encode($return);
	exit;
}

$query2 = mysqli_query($con, "SELECT * FROM problems WHERE contest = ".$contest." AND id = ".$problem);

if (!mysqli_num_rows($query2)) {
	$return["errorCode"] = 5;
	$return["errorText"] = i18n("submitsolution", "error5");
	echo json_encode($return);
	exit;
}

$row2 = mysqli_fetch_assoc($query2);

if (!isset($_FILES["sourcecode"]) && !$competitionhasendedyey) {
	$return["errorCode"] = 1;
	$return["errorText"] = i18n("submitsolution", "error1");
	echo json_encode($return);
	exit;
	
	if($_FILES["output"]["size"] >= 200*1024) {
		$return["errorCode"] = 16;
		$return["errorText"] = i18n("submitsolution", "error16");
		echo json_encode($return);
		exit;
	}
}

$response = file_get_contents($_FILES["output"]["tmp_name"]);

if ($competitionhasendedyey) {
	$weirdtype = ($type == "small") ? "out1_sinput" : "out_linput";
} else {
	$query3 = mysqli_query($con, "SELECT * FROM submissions WHERE contest = {$contest} AND problem = {$problem} AND user_id = {$_SESSION['id']} AND type = ".(($type == "large") ? 1 : 0)." ORDER BY try DESC");
	if (mysqli_num_rows($query3)) {
		$row3 = mysqli_fetch_assoc($query3);
		if ($type == "small") {
			$try = $row3["try"];
		} else {
			$try = 0;
		}
		$endtime = $row3["time"] + (($type == "small") ? 4 : 8) * 60;
		if ($now > $endtime) {
			$return["errorCode"] = 10;
			$return["errorText"] = i18n("submitsolution", "error10");
			echo json_encode($return);
			exit;
		}
		if (isset($row3["valid"])) {
			$return["errorCode"] = 14;
			$return["errorText"] = i18n("submitsolution", "error14");
			echo json_encode($return);
			exit;
		}
	} else {
		$return["errorCode"] = 9;
		$return["errorText"] = i18n("submitsolution", "error9");
		echo json_encode($return);
		exit;
	}
	$weirdtype = convertfileshorthand($type, "out", $try);
}

$judged = judge_output($response, $problem, $weirdtype);

if ($judged >= -2 && $judged < 0) {
	$return["errorCode"] = 6;
	$return["errorText"] = i18n("submitsolution", "error6");
	echo json_encode($return);
	exit;
}

if ($judged == -3) {
	$return["errorCode"] = 7;
	$return["errorText"] = i18n("submitsolution", "error7");
	echo json_encode($return);
	exit;
}

if ($judged == -4) {
	$return["errorCode"] = 8;
	$return["errorText"] = i18n("submitsolution", "error8");
	echo json_encode($return);
	exit;
}

if ($type == "small" || $competitionhasendedyey) {
	$return["judged"] = $judged;
} else {
	$return["judged"] = "submitted";
}

if (!$competitionhasendedyey) {
	$solution = array();
	$array_files = array("output", "sourcecode");
	foreach ($array_files as $file) {
		if ($_FILES[$file]["error"] != 0)  {
			$return["errorCode"] = 11;
			$return["errorText"] = i18n("submitsolution", "error11", array($_FILES[$file]["error"], htmlspecialchars($_FILES[$file]["name"])));
			echo json_encode($return);
			exit;
		} else {
			$newfilename = randomfilename($_FILES[$file]["name"]);
			while (file_exists("../uploaded_solutions/" . $newfilename)) {
				$newfilename = randomfilename($_FILES[$file]["name"]);
			}
			if (move_uploaded_file($_FILES[$file]['tmp_name'], "../uploaded_solutions/".$newfilename)) {
				$solution[$file] = $newfilename;
			} else {
				$return["errorCode"] = 12;
				$return["errorText"] = i18n("submitsolution", "error11", array(htmlspecialchars($_FILES[$file]["name"])));
				echo json_encode($return);
				exit;
			}
    	}
    }
    $solution_json = mysqli_real_escape_string($con, json_encode($solution));
	if (mysqli_query($con, "UPDATE submissions SET solution = '{$solution_json}', valid = {$judged}, timesent = {$now} WHERE id = ".$row3["id"])) {
		// Awesome!
		$return["saved"] = 1;
	} else {
		$return["errorCode"] = 17;
		$return["errorText"] = i18n("submitsolution", "error1");
		echo json_encode($return);
		exit;
	}
}

echo json_encode($return);
?>