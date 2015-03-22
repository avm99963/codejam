<?php
require_once("core.php");

ignore_user_abort(true);
set_time_limit(30); // disable the time limit for this script

if (!isset($_GET["problem"]) || !isset($_GET["type"])) {
    die("Error downloading file: not all arguments were passed");
}

$problem = (INT)$_GET["problem"];
$type = mysqli_real_escape_string($con, $_GET["type"]);

$filename = getfilename($problem, $type);

if ($filename < 0) {
    switch ($filename) {
        case -1:
        die("Error downloading file: unknown type");
        break;

        case -2:
        die("Error downloading file: problem doesn't exist");
        break;

        default:
        die("Unknown error");
        break;
    }
}

$query = mysqli_query($con, "SELECT * FROM problems WHERE id = ".$problem);

$row = mysqli_fetch_assoc($query);

$query2 = mysqli_query($con, "SELECT * FROM contests WHERE id = ".$row["contest"]);

$row2 = mysqli_fetch_assoc($query2);

$now = time();

$translated = translateweirdnesstoawesome($type);

if (getrole() == 0 && $now <= $row2["endtime"]) {
    if ($translated["io"] == "out") {
        die("Can't touch this. Naaaa na na na naa, naaaaaa na. Can't touch this!");
    }
    $query3 = mysqli_query($con, "SELECT * FROM submissions WHERE contest = {$row['contest']} AND user_id = {$_SESSION['id']} AND problem = {$row['id']} AND type = ".(($translated["difficulty"] == "small") ? "0 AND try = ".$translated["try"] : 1));
    if (!mysqli_num_rows($query3)) {
        die("You don't have permission to download this file");
    }
}

$io = json_decode($row["io"], true);

$file = $io["files"][$type];

$path = "uploaded_img/"; // change the path to fit your websites document structure
$dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\].]|[\.]{2,})", '', $file); // simple file name validation
$fullPath = $path.$dl_file;

if ($fd = fopen($fullPath, "r")) {
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    header("Content-type: application/octet-stream");
    header("Content-Disposition: filename=\"".$filename.".".$ext."\"");
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
    }
}
fclose ($fd);
exit;
?>