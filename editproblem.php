<?php
require_once("core.php");
if (getrole() > 1)
{
initi18n("editproblem");
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "empty")
  $msg = '<p class="alert-danger">Por favor, rellena todos los campos</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "nameunique")
  $msg = '<p class="alert-danger">Un problema con este nombre ya existe</p>';
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require ("head.php"); ?>
    <title><?=i18n("editproblem", "title")?> – <?=$appname?></title>
    <?php htmledit(); ?>
  </head>
  <body>
    <div class="content">
      <?php require("nav.php"); ?>
    	<article>
        <?php anuncio(); ?>
        <?php require("sidebar.php"); ?>
    		<div class="text right large">
    		  <h1><?=i18n("editproblem", "title")?></h1>
          <?=$msg?>
          <?php
          if (!isset($_GET["id"])) {
            if (isset($_POST["id"])) {
              $id = (INT)$_POST["id"];
            } else {
              die("<p class='alert-danger'>".i18n("editproblem", "problemdoesntexist")."</p>");
            }
          } else {
            $id = (INT)$_GET["id"];
          }
          $query = mysqli_query($con, "SELECT * FROM problems WHERE id = '".(INT)$id."'");
          if (!mysqli_num_rows($query)) {
            die("<p class='alert-danger'>".i18n("editproblem", "problemdoesntexist")."</p>");
          }
          $row = mysqli_fetch_assoc($query);
          $io = json_decode($row["io"], true);
          if (isset($_GET["sent"]) && $_GET["sent"] == 1) {
            $name = htmlspecialchars(mysqli_real_escape_string($con, $_POST['name']));
            $description = mysqli_real_escape_string($con, $_POST['description']);
            $pts_sinput = (INT)$_POST['pts_sinput'];
            $pts_linput = (INT)$_POST['pts_linput'];

            if (empty($name) || empty($description) || empty($pts_sinput) || empty($pts_linput)) {
              header("Location: editproblem.php?msg=empty");
              exit();
            }

            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM problems WHERE name = '".$name."' AND id != '".$id."'"))) {
              header("Location: editproblem.php?msg=nameunique");
              exit();
            }

            $io["pts"]["small"] = $pts_sinput;
            $io["pts"]["large"] = $pts_linput;

            $array_files = array("in1_sinput", "out1_sinput", "in2_sinput", "out2_sinput", "in3_sinput", "out3_sinput", "in_linput", "out_linput");

            foreach ($array_files as $file) {
              if (isset($_FILES[$file]) && $_FILES[$file]["error"] != 4) {
                if ($_FILES[$file]["error"] != 0) {
                  die("<p class='alert-danger'>".i18n("editproblem", "error_upload", array($_FILES[$file]["error"], htmlspecialchars($_FILES[$file]["name"])))."</p>");
                } else {
                  if (move_uploaded_file($_FILES[$file]['tmp_name'], "uploaded_img/".$io["files"][$file])) {
                    // Awesome!
                  } else {
                    die("<p class='alert-danger'>".i18n("editproblem", "error_move", array(htmlspecialchars($_FILES[$file]["name"])))."</p>");
                  }
                }
              }
            }

            $io = json_encode($io);

            if (mysqli_query($con, "UPDATE problems SET name='".$name."', description='".$description."', io='".$io."' WHERE id = ".$id)) {
              header("Location: admincontest.php?id=".$row["contest"]."&msg=editproblemsuccess");
            } else {
              die("<div class='alert alert-danger'>Error editando la competición :-/</div>");
            }
          } else {
          ?>
          <form action="editproblem.php?sent=1" enctype="multipart/form-data" method="POST" autocomplete="off">
              <input type="hidden" name="id" value="<?=(INT)$_GET["id"]?>">
              <p><label for="name"><?=i18n("editproblem", "name_field")?></label>: <input type="text" name="name" id="name" required="required" maxlength="50" value="<?=$row["name"]?>"></p>
              <p><label for="description"><?=i18n("editproblem", "description_field")?></label>:<br><textarea class="ckeditor" name="description" id="description" required="required"><?=htmlspecialchars($row["description"])?></textarea></p>
              <h3>Input sets:</h3>
              <h4 style="margin-bottom: 0;"><?=i18n("editproblem", "small")?></h4>
              <div class="padding10">
                <p style="margin-top: 5px;">
                  <label for="pts_sinput"><?=i18n("editproblem", "points")?></label>: <input type="number" name="pts_sinput" id="pts_sinput" required="required" min="1"><br><br>
                  <label for="in1_sinput"><?=i18n("editproblem", "input_field", array("1"))?></label>: <input type="file" name="in1_sinput" id="in1_sinput" accept=".in" required="required"><br>
                  <label for="out1_sinput"><?=i18n("editproblem", "output_field", array("1"))?></label>: <input type="file" name="out1_sinput" id="out1_sinput" accept=".out" required="required"><br><br>
                  <label for="in2_sinput"><?=i18n("editproblem", "input_field", array("2"))?></label>: <input type="file" name="in2_sinput" id="in2_sinput" accept=".in" required="required"><br>
                  <label for="out2_sinput"><?=i18n("editproblem", "output_field", array("2"))?></label>: <input type="file" name="out2_sinput" id="out2_sinput" accept=".out" required="required"><br><br>
                  <label for="in3_sinput"><?=i18n("editproblem", "input_field", array("3"))?></label>: <input type="file" name="in3_sinput" id="in3_sinput" accept=".in" required="required"><br>
                  <label for="out3_sinput"><?=i18n("editproblem", "output_field", array("3"))?></label>: <input type="file" name="out3_sinput" id="out3_sinput" accept=".out" required="required">
                </p>
              </div>
              <div id="largeinputs">
                <div id="largeinput1">
                  <h4 style="margin-bottom: 0;"><?=i18n("editproblem", "large")?></h4>
                  <div class="padding10">
                    <p style="margin-top: 5px;">
                      <label for="pts_linput"><?=i18n("editproblem", "points")?></label>: <input type="number" name="pts_linput" id="pts_linput" required="required" min="1"><br>
                      <label for="in_linput"><?=i18n("editproblem", "input_field", array(""))?>: <input type="file" name="in_linput" id="in_linput" accept=".in" required="required"><br>
                      <label for="out_linput"><?=i18n("editproblem", "output_field", array(""))?></label>: <input type="file" name="out_linput" id="out_linput" accept=".out" required="required">
                    </p>
                  </div>
                </div>
              </div>
  		        <p><input type="submit" value="<?=i18n("editproblem", "edit_btn")?>" class="button-link"></p>
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