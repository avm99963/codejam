<?php
require_once("core.php");
if (getrole() > 1)
{
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
    <title>Añadir problema – <?=$appname?></title>
    <!--<script src="js/problems.js"></script>-->
    <?php htmledit(); ?>
  </head>
  <body>
    <div class="content">
      <?php require("nav.php"); ?>
    	<article>
        <?php anuncio(); ?>
        <?php require("sidebar.php"); ?>
    		<div class="text right large">
    		  <h1>Añadir problema</h1>
          <?=$msg?>
          <?php
          if (isset($_GET["sent"]) && $_GET["sent"] == 1) {
            $name = htmlspecialchars(mysqli_real_escape_string($con, $_POST['name']));
            $description = mysqli_real_escape_string($con, $_POST['description']);
            $pts_sinput = (INT)$_POST['pts_sinput'];
            $pts_linput = (INT)$_POST['pts_linput'];
            $contest = (INT)$_POST['contest'];

            if ($pts_sinput == 0 || $pts_linput == 0) {
              header("Location: addproblem.php?msg=pts&id=".$contest);
            }

            if (empty($name) || empty($description) || empty($pts_sinput) || empty($pts_linput) || empty($contest)) {
              header("Location: addproblem.php?msg=empty&id=".$contest);
              exit();
            }

            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM problems WHERE name = '".$name."'"))) {
              header("Location: addproblem.php?msg=nameunique&id=".$contest);
              exit();
            }

            if (!mysqli_num_rows(mysqli_query($con, "SELECT * FROM contests WHERE id = '".$contest."'"))) {
              die("<p class='alert-danger'>Contest doesn't exist :-/</p>");
              exit();
            }

            $array_files = array("in1_sinput", "out1_sinput", "in2_sinput", "out2_sinput", "in3_sinput", "out3_sinput", "in_linput", "out_linput");

            $io = array();
            $io["pts"] = array();
            $io["pts"]["small"] = $pts_sinput;
            $io["pts"]["large"] = $pts_linput;
            $io["files"] = array();

            foreach ($array_files as $file) {
              if (!isset($_FILES[$file])) {
                header("Location: addproblem.php?msg=empty&id=".$contest);
                exit();
              }
              if ($_FILES[$file]["error"] != 0)  {
                die("<p class='alert-danger'>Error ".$_FILES[$file]["error"]." happened while uploading file ".htmlspecialchars($_FILES[$file]["name"])."</p>");
              } else {
                $newfilename = randomfilename($_FILES[$file]["name"]);
                while (file_exists("uploaded_img/" . $newfilename)) {
                  $newfilename = randomfilename($_FILES[$file]["name"]);
                }
                if (move_uploaded_file($_FILES[$file]['tmp_name'], "uploaded_img/".$newfilename)) {
                  $io["files"][$file] = $newfilename;
                } else {
                  die("<p class='alert-danger'>Couldn't upload file ".htmlspecialchars($_FILES[$file]["name"])." – can't move it from tmp directory to the problems folder</p>");
                }
              }
            }

            $io = json_encode($io);

            $sql12 = "SELECT * FROM problems WHERE contest = '".$contest."'";
            $query2 = mysqli_query($con, $sql12);
            if (mysqli_num_rows($query2)) {
              $num = mysqli_num_rows($query2)+1;
            } else {
              $num = 1;
            }

            if (mysqli_query($con, "INSERT INTO problems (name, description, contest, num, io) VALUES ('".$name."', '".$description."', '".$contest."', '".$num."', '".$io."')")) {
              header("Location: admincontest.php?id=".$contest."&msg=addproblemsuccess");
            } else {
              die("<div class='alert alert-danger'>Error añadiendo el problema :-/</div>");
            }
          } else {
          ?>
          <form action="addproblem.php?sent=1" enctype="multipart/form-data" method="POST" autocomplete="off">
              <input type="hidden" name="contest" value="<?=$_GET["id"]?>">
              <p><label for="name">Título del problema</label>: <input type="text" name="name" id="name" required="required" maxlength="50"></p>
              <p><label for="description">Descripción</label>:<br><textarea class="ckeditor" name="description" id="description" required="required"></textarea></p>
              <h3>Input sets:</h3>
              <h4 style="margin-bottom: 0;">Small input:</h4>
              <div class="padding10">
                <p style="margin-top: 5px;">
                  <label for="pts_sinput">Puntos</label>: <input type="number" name="pts_sinput" id="pts_sinput" required="required" min="1"><br><br>
                  <label for="in1_sinput">Input 1 (<i>.in</i>)</label>: <input type="file" name="in1_sinput" id="in1_sinput" accept=".in" required="required"><br>
                  <label for="out1_sinput">Output 1 (<i>.out</i>)</label>: <input type="file" name="out1_sinput" id="out1_sinput" accept=".out" required="required"><br><br>
                  <label for="in2_sinput">Input 2 (<i>.in</i>)</label>: <input type="file" name="in2_sinput" id="in2_sinput" accept=".in" required="required"><br>
                  <label for="out2_sinput">Output 2 (<i>.out</i>)</label>: <input type="file" name="out2_sinput" id="out2_sinput" accept=".out" required="required"><br><br>
                  <label for="in3_sinput">Input 3 (<i>.in</i>)</label>: <input type="file" name="in3_sinput" id="in3_sinput" accept=".in" required="required"><br>
                  <label for="out3_sinput">Output 3 (<i>.out</i>)</label>: <input type="file" name="out3_sinput" id="out3_sinput" accept=".out" required="required">
                </p>
              </div>
              <div id="largeinputs">
                <div id="largeinput1">
                  <h4 style="margin-bottom: 0;">Large input:</h4>
                  <div class="padding10">
                    <p style="margin-top: 5px;">
                      <label for="pts_linput">Puntos</label>: <input type="number" name="pts_linput" id="pts_linput" required="required" min="1"><br>
                      <label for="in_linput">Input (<i>.in</i>)</label>: <input type="file" name="in_linput" id="in_linput" accept=".in" required="required"><br>
                      <label for="out_linput">Output (<i>.out</i>)</label>: <input type="file" name="out_linput" id="out_linput" accept=".out" required="required">
                    </p>
                  </div>
                </div>
              </div>
              <!--<p><a href="#" id="addinputset"><span class="icon svg-ic_add_24px" style="height: 20px;width: 20px;background-position: -4px -4px;"></span>Add input set</a></p>-->
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