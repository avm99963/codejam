<?php
require_once("core.php");
if (isadmin())
{
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "empty")
  $msg = '<p class="alert-danger">Por favor, rellena todos los campos</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "timesdontmatch")
  $msg = '<p class="alert-danger">Ilógica aplastante: El tiempo de comienzo es mayor o igual al tiempo de fin</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "nameunique")
  $msg = '<p class="alert-danger">Una competición con este nombre ya existe</p>';
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require ("head.php"); ?>
    <title>Crear competición – <?=$appname?></title>
  </head>
  <body>
    <div class="content">
      <?php require("nav.php"); ?>
    	<article>
        <?php anuncio(); ?>
        <?php require("sidebar.php"); ?>
    		<div class="text right large">
    		  <h1>Crear competición</h1>
          <?=$msg?>
          <?php
          if (isset($_GET["sent"]) && $_GET["sent"] == 1) {
            $name = htmlspecialchars(mysqli_real_escape_string($con, $_POST['name']));
            $description = htmlspecialchars(mysqli_real_escape_string($con, $_POST['description']));
            $privacy = (INT)$_POST['privacy'];
            $starttime = strtotime($_POST['starttime']);
            $endtime = strtotime($_POST['endtime']);
            
            if (empty($name) || empty($description) || (empty($privacy) && $privacy != 0) || empty($starttime) || empty($endtime)) {
              header("Location: newcontest.php?msg=empty");
              exit();
            }

            if ($starttime >= $endtime) {
              header("Location: newcontest.php?msg=timesdontmatch");
              exit();
            }

            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM contests WHERE name = '".$name."'"))) {
              header("Location: newcontest.php?msg=nameunique");
              exit();
            }

            if (mysqli_query($con, "INSERT INTO contests (name, description, privacy, starttime, endtime) VALUES ('".$name."', '".$description."', '".$privacy."', '".$starttime."', '".$endtime."')")) {
              header("Location: contests.php?msg=newsuccess");
            } else {
              die("<div class='alert alert-danger'>Error creando la competición :-/</div>");
            }
          } else {
          ?>
          <form action="newcontest.php?sent=1" method="POST" autocomplete="off">
              <p><label for="name">Nombre</label>: <input type="text" name="name" id="name" required="required" maxlength="50"></p>
              <p><label for="description">Descripción</label>:<br><textarea name="description" id="description" required="required" maxlength="1000" style="width: 300px; height: 100px;"></textarea></p>
              <p><label for="privacy">Privacidad</label>: <select name="privacy" id="privacy"><option value="0">Private</option><option value="1">Semiprivate</option><option value="2">Public</option></select></p>
              <p><label for="starttime">Comienzo</label>: <input type="datetime-local" name="starttime" id="starttime" required="required"></p>
              <p><label for="endtime">Final</label>: <input type="datetime-local" name="endtime" id="endtime" required="required"></p>
  		        <p><input type="submit" value="Crear" class="button-link"></p>
          </form>
          <?php
          }
          ?>
    		</div>
    	</article>
    </div>
  </body>
</html>
<?php
}
else
{
  header('HTTP/1.0 404 Not Found');
}
?>