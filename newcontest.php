<?php
require_once("core.php");
if (isadmin())
{
initi18n("newcontest");
$msg = "";
if (isset($_GET['msg']) && in_array($_GET['msg'], array("empty", "contestnameunique", "timesdontmatch"))) {
  $msg = "<p class='alert-danger'>".i18n("global", "msg_".$_GET['msg'])."</p>";
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require ("head.php"); ?>
    <title><?=i18n("newcontest", "title")?> – <?=$appname?></title>
  </head>
  <body>
    <div class="content">
      <?php require("nav.php"); ?>
    	<article>
        <?php anuncio(); ?>
        <?php require("sidebar.php"); ?>
    		<div class="text right large">
    		  <h1><?=i18n("newcontest", "title")?></h1>
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
              header("Location: newcontest.php?msg=contestnameunique");
              exit();
            }

            if (mysqli_query($con, "INSERT INTO contests (name, description, privacy, starttime, endtime) VALUES ('".$name."', '".$description."', '".$privacy."', '".$starttime."', '".$endtime."')")) {
              header("Location: contests.php?msg=newsuccess");
            } else {
              die("<div class='alert alert-danger'>".i18n("newcontest", "error")." :-/</div>");
            }
          } else {
          ?>
          <form action="newcontest.php?sent=1" method="POST" autocomplete="off">
              <p><label for="name"><?=i18n("newcontest", "name_field")?></label>: <input type="text" name="name" id="name" required="required" maxlength="50"></p>
              <p><label for="description"><?=i18n("newcontest", "description_field")?></label>:<br><textarea name="description" id="description" required="required" maxlength="1000" style="width: 300px; height: 100px;"></textarea></p>
              <p><label for="privacy"><?=i18n("newcontest", "privacy_field")?></label>: <select name="privacy" id="privacy"><option value="0"><?=i18n("newcontest", "private")?></option><option value="1"><?=i18n("newcontest", "semiprivate")?></option><option value="2"><?=i18n("newcontest", "public")?></option></select></p>
              <p><label for="starttime"><?=i18n("newcontest", "starttime_field")?></label>: <input type="datetime-local" name="starttime" id="starttime" required="required"></p>
              <p><label for="endtime"><?=i18n("newcontest", "endtime_field")?></label>: <input type="datetime-local" name="endtime" id="endtime" required="required"></p>
  		        <p><input type="submit" value="<?=i18n("newcontest", "submit_btn")?>" class="button-link"></p>
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