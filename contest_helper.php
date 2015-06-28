<?php
/**
 * Contest Scoreboard Helper Script
 * 

    /////  //////  //  // ///// /////   ///// /////
   //     //  //  /// //   //  //__    //__    //
  //     //  //  // ///   //  //´´´   ´´´//   //
 /////  //////  //  //   //  /////   /////   //

 */

require_once("core.php");

function leaderboard($contest, $top10 = false, $tellmethetruth = false) {
	global $con;

	$query2 = mysqli_query($con, "SELECT user_id FROM submissions WHERE contest = ".(INT)$contest." GROUP BY user_id");

	if (!mysqli_num_rows($query2)) {
		return false;
	}

	$scores = array();
	$participants = array();

	for ($i = 0; $i < mysqli_num_rows($query2); $i++) {
		$row2 = mysqli_fetch_assoc($query2);
		$participants[] = $row2["user_id"];
	}

	foreach ($participants as $participant) {
		$submissions = submissions((INT)$contest, (INT)$participant, $tellmethetruth);

		if ($submissions === false) {
			return false;
		}

		$totalpoints = 0;
		$time = 0;

		$penalty = 0;

		foreach ($submissions as $submission) {
			foreach ($submission as $type => $solution) {
				$totalpoints += $solution["pts"];
				if ($type == "small") {
					if ($solution["status"] == "correct") {
						$penalty += ($solution["count"] - 1) * 4 * 60;
					}
				}
				if ($time < $solution["penalty"]) {
					$time = $solution["penalty"];
				}
			}
		}

		$time += $penalty;

		/*if ($totalpoints == 0) {
			continue;
		}*/ // Causes bug when judging submissions of contestant who has only submitted wrong responses
		// TODO: Decide if support for this should be continued or not

		$scores[] = array(
			"score" => $totalpoints,
			"time" => $time,
			"user_id" => $participant,
			"submissions" => $submissions
		);
	}

	usort($scores, function($a, $b) {
		$order = $b["score"]-$a["score"];
		if ($order == 0) {
			$order = $a["time"]-$b["time"];
		}
	    return $order;
	});

	if (count($scores) == 0) {
		return false;
	}

	if ($top10 === true) {
		return array_slice($scores, 0, 10);
	}

	return $scores;
}

function judge_output($response, $problem, $type) {
	global $con;

	$query = mysqli_query($con, "SELECT * FROM problems WHERE id = ".(INT)$problem);

	if (!mysqli_num_rows($query)) {
		return -1;
	}

	$row = mysqli_fetch_assoc($query);

	$io = json_decode($row["io"], true);

	if (!isset($io["files"][$type])) {
		return -2;
	}

	$solution = trim(file_get_contents("../uploaded_img/".$io["files"][$type]));

	$response = trim($response);

	$solution_lines = explode("\n", $solution);
	$response_lines = explode("\n", $response);

	foreach ($response_lines as $i => $line) {
		$case = $i+1;
		if (substr($line, 0, (8+strlen($case))) != "Case #".$case.": ") {
			return -3;
		}
	}

	if (count($solution_lines) != count($response_lines)) {
		return -4;
	}

	foreach ($response_lines as $i => $line) {
		if ($line != $solution_lines[$i]) {
			return 0;
		}
	}	

	return 1;
}

function translateintosubmission($problem, $dataset, $try = null, $userid = 'currentuser') {
	global $con;

	if ($userid == 'currentuser') {
		$id = $_SESSION['id'];
	} else {
		$id = $userid;
	}

	if (in_array($dataset, array("large", "small"), true)) {
		$dataset = ($dataset == "large") ? 1 : 0;
	} elseif (!in_array($dataset, array(0,1), true)) {
		return false;
	}

	if ($dataset == 1) {
		$try = null;
		$trywhere = "";
	} elseif ($try <= 3 && $try > 0) {
		$trywhere = " AND try = ".(INT)$try;
	} else {
		return false;
	}

	$query = mysqli_query($con, "SELECT id FROM submissions WHERE problem = ".(INT)$problem." AND type = ".(INT)$dataset." AND user_id = ".(INT)$id.$trywhere." LIMIT 1");

	if (mysqli_num_rows($query)) {
		return mysqli_fetch_assoc($query)["id"];
	} else {
		return false;
	}
}

// "Broken" function for now... (It works, but it is not very useful)
function submission($submission, $tellmethetruth = false) {
	global $con;

	$now = time();

	$query3 = mysqli_query($con, "SELECT * FROM submissions WHERE id = ".(INT)$submission);

	if (!mysqli_num_rows($query3)) {
		return false;
	}

	$row3 = mysqli_fetch_assoc($query3);
	$dataset = ($row3["type"] == 1) ? "large" : "small";

	$query = mysqli_query($con, "SELECT * FROM contests WHERE id = ".(INT)$row3["contest"]);

	if (!mysqli_num_rows($query)) {
		return false;
	}

	$row = mysqli_fetch_assoc($query);

	/* Check if the submission is complete, or else if it is not
	 *
	 * It checks if:
	 * - Time expired
	 * - It was sent/judged
	 * - It was judged manually
	 * - Contest has finished, and so time has expired
	 *
	 */
	if ((($row3["time"] + (($row3["type"] == 0) ? 4 : 8) * 60) < $now) || isset($row3["valid"]) || isset($row3["judged"]) || $now > $row["endtime"]) {
		$query2 = mysqli_query($con, "SELECT * FROM problems WHERE id = ".(INT)$row3["problem"]);

		if (!mysqli_num_rows($query2)) {
			return false;
		}

		$row2 = mysqli_fetch_assoc($query2);
		$io = json_decode($row2["io"], true);

		$valid = 0;
		if (isset($row3["judged"]) && $row3["judged"] != "") {
			$valid = $row3["judged"];
		} else {
			$valid = $row3["valid"];
		}
		if ($row3["type"] == "large") {
			$return = array(
				"status" => "notattempted",
				"pts" => 0,
				"penalty" => 0
			);
			if (!isset($valid)) {
				$return["status"] = "timeexpired";
			} elseif ($now > $row["endtime"] || $tellmethetruth === true) {
				if ($valid == 1) {
					$return["status"] = "correct";
					$return["penalty"] = $row3["timesent"] - $row["starttime"];
					$return["pts"] = $io["pts"][$dataset];
				} else {
					$return["status"] = "incorrect";
				}
			} else {
				$return["status"] = "submitted";
				$return["penalty"] = $row3["timesent"] - $row["starttime"];
				$return["pts"] = $io["pts"][$dataset];
			}
		} else {
			$return = array(
				"status" => "notattempted",
				"pts" => 0,
				"penalty" => 0,
				"count" => 0
			);
			if ($valid == 1) {
				$return["status"] = "correct";
				$return["penalty"] = $row3["timesent"] - $row["starttime"];
				$return["pts"] = $io["pts"][$dataset];
			} elseif ($valid == 0) {
				$return["status"] = "incorrect";
			} else {
				$return["status"] = "timeexpired";
			}
			$query4 = mysqli_query($con, "SELECT id FROM submissions WHERE problem = ".$row3["problem"]." AND type = ".$row3["type"]." AND user_id = ".$row3["user_id"]." LIMIT 1");
			$return["count"] = mysqli_num_rows($query4);
		}
	} else {
		return false;
	}
	return $return;
}

function submissions($contest, $userid='currentuser', $tellmethetruth = false) {
	global $con;

	if ($userid == 'currentuser') {
		$id = $_SESSION['id'];
	} else {
		$id = $userid;
	}

	$query = mysqli_query($con, "SELECT * FROM contests WHERE id = ".$contest." LIMIT 1");

	if (!mysqli_num_rows($query)) {
		return false;
	}

	$row = mysqli_fetch_assoc($query);

	$now = time();

	$query2 = mysqli_query($con, "SELECT * FROM problems WHERE contest = ".$contest." ORDER BY num");

	if (!mysqli_num_rows($query2)) {
		return false;
	}

	$problems = array();

	for ($i = 0; $i < mysqli_num_rows($query2); $i++) {
		$problem = mysqli_fetch_assoc($query2);
		$problems[$problem["id"]] = $problem;
	}

	$query3 = mysqli_query($con, "SELECT * FROM submissions WHERE contest = {$contest} AND user_id = {$id}");

	$return = array();

	$submissions = array();

	foreach ($problems as $problem) {
		$submissions[$problem["id"]] = array(
			"small" => array(),
			"large" => array()
		);

		$return[$problem["id"]] = array(
			"small" => array(
				"status" => "notattempted",
				"pts" => 0,
				"penalty" => 0,
				"count" => 0,
				"manuallyjudged" => false
			),
			"large" => array(
				"status" => "notattempted",
				"pts" => 0,
				"penalty" => 0,
				"manuallyjudged" => false
			)
		);
	}

	if (mysqli_num_rows($query3)) {
		for ($i = 0; $i < mysqli_num_rows($query3); $i++) {
			$submission = mysqli_fetch_assoc($query3);
			if ((($submission["time"] + (($submission["type"] == 0) ? 4 : 8) * 60) < $now) || isset($submission["valid"]) || isset($submission["judged"]) || $now > $row["endtime"]) {
				$submissions[$submission["problem"]][(($submission["type"] == 0) ? "small" : "large")][] = $submission;
			}
		}

		foreach ($return as $problemid => $submission) {
			$io = json_decode($problems[$problemid]["io"], true);
			foreach ($submission as $dataset => $status) {
				if (count($submissions[$problemid][$dataset])) {
					$last = array_pop($submissions[$problemid][$dataset]);
					$valid = 0;
					if (isset($last["judged"]) && $last["judged"] != "") {
						$valid = $last["judged"];
						if ($last["judged"] != $last["valid"]) {
							$return[$problemid][$dataset]["manuallyjudged"] = true;
						}
					} else {
						$valid = $last["valid"];
					}
					if ($dataset == "large") {
						if (!isset($valid)) {
							$return[$problemid][$dataset]["status"] = "timeexpired";
						} elseif ($now > $row["endtime"] || $tellmethetruth === true) {
							if ($valid == 1) {
								$return[$problemid][$dataset]["status"] = "correct";
								$return[$problemid][$dataset]["penalty"] = $last["timesent"] - $row["starttime"];
								$return[$problemid][$dataset]["pts"] = $io["pts"][$dataset];
							} else {
								$return[$problemid][$dataset]["status"] = "incorrect";
							}
						} else {
							$return[$problemid][$dataset]["status"] = "submitted";
							$return[$problemid][$dataset]["penalty"] = $last["timesent"] - $row["starttime"];
							$return[$problemid][$dataset]["pts"] = $io["pts"][$dataset];
						}
					} else {
						if ($valid == 1) {
							$return[$problemid][$dataset]["status"] = "correct";
							$return[$problemid][$dataset]["penalty"] = $last["timesent"] - $row["starttime"];
							$return[$problemid][$dataset]["pts"] = $io["pts"][$dataset];
						} elseif ($valid == 0) {
							$return[$problemid][$dataset]["status"] = "incorrect";
						} else {
							$return[$problemid][$dataset]["status"] = "timeexpired";
						}
						$return[$problemid][$dataset]["count"] = count($submissions[$problemid][$dataset]) + 1;
					}
					if ($return[$problemid][$dataset]["manuallyjudged"] === true && (!isset($last["timesent"]) || $last["timesent"] == "")) {
						$return[$problemid][$dataset]["penalty"] = $last["time"] - $row["starttime"] + (($last["type"] == 0) ? 4 : 8) * 60;
						if ($return[$problemid][$dataset]["penalty"] > $row["endtime"] - $row["starttime"]) {
							$return[$problemid][$dataset]["penalty"] = $row["endtime"] - $row["starttime"];
						}
					}
				}
			}
		}
	}

	return $return;
}

function score($contest, $userid='currentuser') {
	global $con;

	if ($userid == 'currentuser') {
		$id = $_SESSION['id'];
	} else {
		$id = $userid;
	}

	$submissions = submissions($contest, $userid);

	if ($submissions === false) {
		return false;
	}

	$totalpoints = 0;

	foreach ($submissions as $submission) {
		foreach ($submission as $solution) {
			$totalpoints += $solution["pts"];
		}
	}

	return $totalpoints;
}

function rank($contest, $userid='currentuser') {
	if ($userid == 'currentuser') {
		$id = $_SESSION['id'];
	} else {
		$id = (INT)$userid;
	}

	$leaderboard = leaderboard((INT)$contest);

	if ($leaderboard === false) {
		return "--";
	}

	$i = 0;
	foreach ($leaderboard as $leader) {
		$i++;
		if ($leader["user_id"] == $id) {
			return $i;
		}
	}
	return "--";
}

function isinvited($contest, $userid='currentuser') {
	global $con;

	if ($userid == 'currentuser') {
		$id = $_SESSION['id'];
	} else {
		$id = (INT)$userid;
	}

	$query2 = mysqli_query($con, "SELECT * FROM contests WHERE id = ".(INT)$contest);

	if (!mysqli_num_rows($query2)) {
		return -1;
	}

	$row2 = mysqli_fetch_assoc($query2);

	if ($row2["privacy"] == 2) {
		return true;
	}

	$query = mysqli_query($con, "SELECT * FROM invitations WHERE contest = ".(INT)$contest." AND user_id = {$id}");

	if (mysqli_num_rows($query)) {
		return true;
	} else {
		return false;
	}
}
?>