<?php
require_once("core.php");
if (getrole() > 0)
{
initi18n("problem");
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
    <title><?=i18n("problem", "title")?> – <?=$appname?></title>
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
          <h3><?=i18n("problem", "inputoutput")?></h3>
          <h4><?=i18n("problem", "smallinputset")?></h4>
          <p>
            <?php
            $echo = array();
            for ($i = 1; $i < 4; $i++) {
              $echo[] = '<a href="download.php?problem='.$row["id"].'&type=in'.$i.'_sinput"><span class="icon svg-ic_file_download_24px"></span></a> <a href="download.php?problem='.$row["id"].'&type=in'.$i.'_sinput">'.i18n("problem", "inputlabel", array($i)).'</a>';
              $echo[] = '<a href="download.php?problem='.$row["id"].'&type=out'.$i.'_sinput"><span class="icon svg-ic_file_upload_24px"></span></a> <a href="download.php?problem='.$row["id"].'&type=out'.$i.'_sinput">'.i18n("problem", "outputlabel", array($i)).'</a>';
            }
            echo implode($echo, "<br>");
            ?>
          </p>
          <h4><?=i18n("problem", "largeinputset")?></h4>
          <p><a href="download.php?problem=<?=$row["id"]?>&type=in_linput"><span class="icon svg-ic_file_download_24px"></span></a> <a href="download.php?problem=<?=$row["id"]?>&type=in_linput"><?=i18n("global", "input")?></a><br>
          <a href="download.php?problem=<?=$row["id"]?>&type=out_linput"><span class="icon svg-ic_file_upload_24px"></span></a> <a href="download.php?problem=<?=$row["id"]?>&type=out_linput"><?=i18n("global", "output")?></a></p>
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