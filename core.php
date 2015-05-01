<?php
/**
 * Core
 * 

    /////  //////  //////  /////
   //     //  //  //  //  //___
  //     //  //  //////  //´´´´
 /////  //////  // //   /////

 */

// Timezone
date_default_timezone_set("Europe/Madrid");

// Aquí se recoge la configuración
require("config.php");

// Aquí se accede a la BD y a la sesión
$con = @mysqli_connect($host_db, $usuario_db, $clave_db, $nombre_db) or die("<center>Check Mysqli settings in config.php</center>"); // Conectamos y seleccionamos BD

session_start();

// Custom error handler

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        echo "<div class='alert alert-danger'><b>Error:</b> [$errno] $errstr<br>\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...</div>\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<div class='alert alert-warning'><b>Warning:</b> [$errno] $errstr on line $errline in file $errfile</div>\n";
        break;

    case E_WARNING:
        echo "<div class='alert alert-warning'><b>Warning:</b> [$errno] $errstr on line $errline in file $errfile</div>\n";
        break;

    case E_ERROR:
        echo "<div class='alert alert-danger'><b>Error:</b> [$errno] $errstr<br>\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...</div>\n";
        exit(1);
        break;

    case E_USER_NOTICE:
        echo "<div class='alert alert-warning'><b>Notice:</b> [$errno] $errstr on line $errline in file $errfile</div>\n";
        break;

    default:
        echo "<div class='alert alert-warning'>Unknown error type: [$errno] $errstr on line $errline in file $errfile</div>\n";
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}

$old_error_handler = set_error_handler("myErrorHandler");

// Aquí van todas las funciones
function anuncio()
{
	echo $GLOBALS['anuncio'];
}

// Function which tells if user is hyperadmin. Unsupported, should be removed from code where found and replaced with function getrole().
function isadmin() {
	if (getrole() == 3)
		return TRUE;
	else
		return FALSE;
}

function getrole() {
    if (!isset($_SESSION['id']))
        return FALSE;
	$id = $_SESSION['id'];
	$query = mysqli_query($GLOBALS['con'], "SELECT role FROM users WHERE ID = '".$id."'");
	$row = mysqli_fetch_assoc($query);
	return $row["role"];
}

function userdata($data2, $userid='currentuser') {
	if ($userid == 'currentuser') {
		$id = $_SESSION['id'];
	} else {
		$id = $userid;
	}
	$data = mysqli_real_escape_string($GLOBALS['con'], $data2);
	$query = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE ID = '".$id."'");
    if (!mysqli_num_rows($query)) {
        return false;
    }
	$row = mysqli_fetch_assoc($query);
	return $row[$data];
}

function loggedin() {
	if (isset($_SESSION['id']))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

function randomfilename($filename) {
	$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$length = $GLOBALS['filenamelength'];
	$name = '';
	for($i = 0; $i < $length; $i++) {
	    $name .= $chars[mt_rand(0, 35)];
    }
    $explode = explode(".", $filename);
    $extension = end($explode);
    $return = $name.".".$extension; // random_name.png
	return $return;
}

function tominutesandseconds($time) {
    settype($time, 'integer');
    if ($time < 1) {
        return;
    }
    $minutes = floor($time / 60);
    $seconds = str_pad(($time % 60), 2, '0', STR_PAD_LEFT);
    return $minutes.":".$seconds;
}

function format_time($t,$f=':') {
    $hours = floor($t/3600);
    $minutes = ($t/60)%60;
    $seconds = $t%60;
    if ($hours == 0) {
        return sprintf("%02d%s%02d", $minutes, $f, $seconds);
    } else {
        return sprintf("%02d%s%02d%s%02d", $hours, $f, $minutes, $f, $seconds);
    }
}

function htmledit() {
    echo '<script src="lib/ckeditor/ckeditor.js"></script>';
}

function get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}

function twostepverification($userid='currentuser') {
    global $con;
    if ($userid == 'currentuser') {
        $id = $_SESSION['id'];
    } else {
        $id = $userid;
    }
    $query = mysqli_query($con, "SELECT * FROM 2stepverification WHERE user_id = ".$id);
    if (mysqli_num_rows($query)) {
        $query2 = mysqli_query($con, "SELECT * FROM securitykeys WHERE user_id = ".$id);
        if (mysqli_num_rows($query2)) {
            return 2;
        } else {
            return 1;
        }
    } else {
        return FALSE;
    }
}

function getsecret() {
    global $con;
    $id = (isset($_SESSION['id'])) ? $_SESSION['id'] : $_SESSION['prov_id'];
    $query = mysqli_query($con, "SELECT secret FROM 2stepverification WHERE user_id = ".$id);
    if (mysqli_num_rows($query)) {
        $row = mysqli_fetch_assoc($query);
        return $row["secret"];
    } else {
        return FALSE;
    }
}

function getfilename($problem, $type) {
    global $con;

    $array_files = array("in1_sinput", "out1_sinput", "in2_sinput", "out2_sinput", "in3_sinput", "out3_sinput", "in_linput", "out_linput");
    if (!in_array($type, $array_files)) {
        return -1;
    }

    $query = mysqli_query($con, "SELECT * FROM problems WHERE id = ".(INT)$problem);

    if (!mysqli_num_rows($query)) {
        return -2;
    }

    $row = mysqli_fetch_assoc($query);
    $io = json_decode($row["io"], true);

    $file = $io["files"][$type];

    $difficulty = (explode("_", $type)[1] == "linput") ? "large" : "small";
    if ($difficulty == "small") {
        $difficulty .= "-".(str_replace(array("in", "out"), "", explode("_", $type)[0]));
    }
    $problemname = str_replace(" ", "", strtolower($row["name"]));

    return $problemname."-".$difficulty;
}

function translateweirdnesstoawesome($weird) {
    $array_files = array("in1_sinput", "out1_sinput", "in2_sinput", "out2_sinput", "in3_sinput", "out3_sinput", "in_linput", "out_linput");
    if (!in_array($weird, $array_files)) {
        return -1;
    }

    $return = array();

    $return["difficulty"] = (explode("_", $weird)[1] == "linput") ? "large" : "small";
    $return["io"] = (substr($weird, 0, 1) == "i") ? "in" : "out";

    if ($return["difficulty"] == "small") {
        $return["try"] = str_replace(array("in", "out"), "", explode("_", $weird)[0]);
    }

    return $return;
}

function convertfileshorthand($type, $file, $try=NULL) {
    if (!in_array($file, array("out", "in"))) {
        return false;
    }

    if ($type == "small" && ($try < 1 || $try > 3)) {
        return false;
    }

    if ($type == "large") {
        return $file."_linput";
    } else {
        return $file.$try."_sinput";
    }
}

function array_search_multidimensional($haystack, $field, $needle) {
   foreach($haystack as $key => $hayinstack) {
      if ($hayinstack[$field] === $needle)
         return $key;
   }
   return false;
}

function getlanguagei18n() {
    global $language;
    return $language;
}

function initi18n($include) {
    global $i18n_strings;
    $i18n_strings = array();
    $language = getlanguagei18n();

    $i18n_strings["global"] = json_decode(file_get_contents("locales/".$language."/global.json"), true);

    if (gettype($include) == "array") {
        foreach ($include as $includer) {
            $file = "locales/".$language."/".$includer.".json";
            if (file_exists($file)) {
                $i18n_strings[$includer] = json_decode(file_get_contents($file), true);
            } else {
                die("<div class='alert alert-danger'>File ".$file." doesn't exist</div>");
                return false;
            }
        }
    } elseif (gettype($include) == "string") {
        $file = "locales/".$language."/".$include.".json";
        if (file_exists($file)) {
            $i18n_strings[$include] = json_decode(file_get_contents($file), true);
        } else {
            die("<div class='alert alert-danger'>File ".$file." doesn't exist</div>");
            return false;
        }
    } else {
        return false;
    }

    return true;
}

function i18n($include, $message, $strings = null) {
    global $i18n_strings;

    if (!isset($i18n_strings[$include][$message])) {
        return false;
    }

    $string = $i18n_strings[$include][$message];

    if ($strings != null) {
        foreach ($strings as $i => $subst) {
            $string = str_replace("%".$i, $subst, $string);
        }
    }

    return $string;
}
?>