<?php
require_once("core.php");
if (getrole() > 0)
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
    <title>Problema – <?=$appname?></title>
    <style>
    #description {
      /* Font */
      font-family: sans-serif, Arial, Verdana, "Trebuchet MS";
      font-size: 13px;

      /* Text color */
      color: #333;
    }
    </style>
  </head>
  <body>
    <div class="content">
      <?php require("nav.php"); ?>
    	<article>
        <?php anuncio(); ?>
        <?php require("sidebar.php"); ?>
    		<div class="text right large">
    		  <?php
          $query = mysqli_query($con, "SELECT * FROM problems WHERE id = '".(INT)$_GET["id"]."'");
          if (mysqli_num_rows($query)) {
            $row = mysqli_fetch_assoc($query);
            $io = json_decode($row["io"], true);
          ?>
          <h1><?=$row["name"]?></h1>
          <div id="description"><?=$row["description"]?></div>
          <h3>Input/output files</h3>
          <h4>Small Input Set</h4>
          <p><a href="download.php?problem=<?=$row["id"]?>&type=in1_sinput"><span class="icon svg-ic_file_download_24px"></span></a> <a href="download.php?problem=<?=$row["id"]?>&type=in1_sinput">Input 1</a><br>
          <a href="download.php?problem=<?=$row["id"]?>&type=out1_sinput"><span class="icon svg-ic_file_upload_24px"></span></a> <a href="download.php?problem=<?=$row["id"]?>&type=out1_sinput">Output 1</a><br>
          <a href="download.php?problem=<?=$row["id"]?>&type=in2_sinput"><span class="icon svg-ic_file_download_24px"></span></a> <a href="download.php?problem=<?=$row["id"]?>&type=in2_sinput">Input 2</a><br>
          <a href="download.php?problem=<?=$row["id"]?>&type=out2_sinput"><span class="icon svg-ic_file_upload_24px"></span></a> <a href="download.php?problem=<?=$row["id"]?>&type=out2_sinput">Output 2</a><br>
          <a href="download.php?problem=<?=$row["id"]?>&type=in3_sinput"><span class="icon svg-ic_file_download_24px"></span></a> <a href="download.php?problem=<?=$row["id"]?>&type=in3_sinput">Input 3</a><br>
          <a href="download.php?problem=<?=$row["id"]?>&type=out3_sinput"><span class="icon svg-ic_file_upload_24px"></span></a> <a href="download.php?problem=<?=$row["id"]?>&type=out3_sinput">Output 3</a></p>
          <h4>Large Input Set</h4>
          <p><a href="download.php?problem=<?=$row["id"]?>&type=in_linput"><span class="icon svg-ic_file_download_24px"></span></a> <a href="download.php?problem=<?=$row["id"]?>&type=in_linput">Input</a><br>
          <a href="download.php?problem=<?=$row["id"]?>&type=out_linput"><span class="icon svg-ic_file_upload_24px"></span></a> <a href="download.php?problem=<?=$row["id"]?>&type=out_linput">Output</a></p>
          <?php
          } else {
            echo "<div class='alert-danger'>Este problema no existe :-/</div>";
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