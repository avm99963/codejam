<?php
require_once("core.php");
require_once("contest_helper.php");
if (getrole())
{
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "editsuccess")
  $msg = '<p class="alert-success">Competición editada satisfactoriamente</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "addproblemsuccess")
  $msg = '<p class="alert-success">Problema añadido satisfactoriamente</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "editproblemsuccess")
  $msg = '<p class="alert-success">Problema editado satisfactoriamente</p>';
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require ("head.php"); ?>
    <title>Pasar de ronda – <?=$appname?></title>
    <style>
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        display: none;
        /*-webkit-appearance: none;*/
        margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
    }
    </style>
  </head>
  <body>
    <div class="content">
      <?php require("nav.php"); ?>
    	<article>
        <?php anuncio(); ?>
        <?php if (getrole() > 0) {
          require("sidebar.php");
        ?>
        <div class="text right large">
        <?php } else { ?>
        <div class="text">
        <?php
        }
        ?>
          <?=$msg?>
          <h1>Pasar de ronda</h1>
          <?php
          if (isset($_GET["sent"]) && $_GET["sent"] == 1) {
            if (!isset($_POST["contestants"]) || !isset($_POST["origin"]) || !isset($_POST["objective"])) {
              die("<div class='alert-warning'>Por favor, rellena todo el formulario.</div>");
            }

            $contestants = (INT)$_POST["contestants"];
            $origin = (INT)$_POST["origin"];
            $objective = (INT)$_POST["objective"];

            if ($contestants < 1) {
              die("<div class='alert-warning'>Selecciona un mínimo de 1 participante.</div>");
            }

            $originquery = mysqli_query($con, "SELECT * FROM contests WHERE id = ".$origin);

            if (!mysqli_num_rows($originquery)) {
              die("<div class='alert-warning'>La competición de origen no existe.</div>");
            }

            $originrow = mysqli_fetch_assoc($originquery);

            $objectivequery = mysqli_query($con, "SELECT * FROM contests WHERE id = ".$objective);

            if (!mysqli_num_rows($objectivequery)) {
              die("<div class='alert-warning'>La competición de destino no existe.</div>");
            }

            $objectiverow = mysqli_fetch_assoc($objectivequery);

            $leaderboard = leaderboard($origin);

            if ($leaderboard === false) {
              die("<div class='alert-warning'>No ha participado nadie en la competición de origen.</div>");
            }

            if (count($leaderboard) < $contestants) {
              die("<div class='alert-warning'>Solo han participado ".count($leaderboard)." participantes en la competición de origen. Has seleccionado ".$contestants.".</div>");
            }

            $leaderboard = array_slice($leaderboard, 0, $contestants);

            foreach ($leaderboard as $leader) {
              $query = mysqli_query($con, "SELECT * FROM invitations WHERE user_id = ".$leader["user_id"]." AND contest = ".$objective);
              if (mysqli_num_rows($query)) {
                echo "<div class='alert-warning' style='margin-bottom: 5px;'>No se ha invitado al usuario ".userdata("username", $leader["user_id"])." porque ya estaba invitado.</div>";
              } else {
                if (!mysqli_query($con, "INSERT INTO invitations (user_id, contest) VALUES (".$leader["user_id"].", ".$objective.")")) {
                  die("<div class='alert-danger'>No se ha podido invitar el usuario ".userdata("username", $leader["user_id"]).".</div>");
                }
              }
            }

            echo "<div class='alert-success'>Se ha invitado satisfactoriamente los líderes a la siguiente ronda.</div>";
          } else {
          ?>
          <form action="promote.php?sent=1" method="POST" autocomplete="off">
            <?php
            $now = time();
            $query = mysqli_query($con, "SELECT * FROM contests");
            if (mysqli_num_rows($query)) {
              ?>
              <p>Invitar los <input type="number" name="contestants" style="width: 50px;" required="required" min="1"> primeros concursantes de <select name="origin" required="required">
              <?php
              $select = "";
              while ($row = mysqli_fetch_assoc($query)) {
                if ($row["endtime"] <= $now) {
                  echo '<option value="'.$row["id"].'">'.$row["name"].'</option>';
                } else {
                  $select .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
                }
              }
              ?>
              </select> a <select name="objective" required="required"><?=$select?></select></p>
              <p><input type="submit" value="Pasar de ronda" class="button-link"></p>
              <?php
            } else {
              echo "<p style='text-align: center;'>No hay ningún concurso</p>";
            }
            ?>
          </form>
          <?php
          }
          ?>
    		</div>
    	</article>
    </div>
    <div id="modal-dialog-bg" hidden></div>
    <div id="modal-dialog" hidden></div>
  </body>
</html>
<?php
}
else
{
  header('HTTP/1.0 404 Not Found');
}
?>