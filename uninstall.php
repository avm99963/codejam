<?php
// --------------------------
// IMPORTANT!
// Delete file when website is in production
// --------------------------
require_once("core.php");
$result = mysqli_query($con, "SHOW TABLES FROM ".mysqli_real_escape_string($con, $nombre_db));
while ($row = mysqli_fetch_assoc($result))
{
	mysqli_query($con, "DROP TABLE ".$row[0]) or die("Error");
}
if (!rmdir("uploaded_img")) {
	die("Shit, the uploaded images directory was not erased.");
}
// comprobamos que se haya iniciado la sesión 
    if(isset($_SESSION['id'])) { 
        session_destroy();
    }else { 
        echo "No se ha hecho logout. "; 
    }
echo "All deleted!";
?>