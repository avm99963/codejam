<?php
require_once("core.php");
if (isadmin())
{
initi18n("editcontest");
$msg = "";
if (isset($_GET['msg']) && in_array($_GET['msg'], array("empty", "contestnameunique", "timesdontmatch"))) {
  $msg = "<p class='alert-danger'>".i18n("global", "msg_".$_GET['msg'])."</p>";
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require ("head.php"); ?>
    <title><?=i18n("editcontest", "title")?> – <?=$appname?></title>
  </head>
  <body>
    <div class="content">
      <?php require("nav.php"); ?>
    	<article>
        <?php anuncio(); ?>
        <?php require("sidebar.php"); ?>
    		<div class="text right large">
    		  <h1><?=i18n("editcontest", "title")?></h1>
          <?=$msg?>
          <?php
          if (isset($_GET["sent"]) && $_GET["sent"] == 1) {
            $id = (INT)$_POST['id'];
            $name = htmlspecialchars(mysqli_real_escape_string($con, $_POST['name']));
            $description = htmlspecialchars(mysqli_real_escape_string($con, $_POST['description']));
            $privacy = (INT)$_POST['privacy'];
            $starttime = strtotime($_POST['starttime']);
            $endtime = strtotime($_POST['endtime']);
            
            if (empty($name) || empty($description) || (empty($privacy) && $privacy != 0) || empty($starttime) || empty($endtime)) {
              header("Location: editcontest.php?msg=empty");
              exit();
            }

            if ($starttime >= $endtime) {
              header("Location: editcontest.php?msg=timesdontmatch");
              exit();
            }

            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM contests WHERE name = '".$name."' AND id != '".$id."'"))) {
              header("Location: editcontest.php?msg=contestnameunique");
              exit();
            }

            if (mysqli_query($con, "UPDATE contests SET name = '".$name."', description = '".$description."', privacy = '".$privacy."', starttime = '".$starttime."', endtime = '".$endtime."' WHERE id = '".$id."' LIMIT 1")) {
              header("Location: admincontest.php?id={$id}&msg=editsuccess");
            } else {
              die("<div class='alert alert-danger'>".i18n("editcontest", "error")." :-/ ".mysqli_error($con)."</div>");
            }
          } else {
            if (!isset($_GET["id"])) {
              die("<div class='alert-danger'>Can't edit</div>");
            }
            $id = (INT)$_GET["id"];
            $query = mysqli_query($con, "SELECT * FROM contests WHERE id = '".$id."'");
            if (!mysqli_num_rows($query)) {
              die("<div class='alert-danger'>Can't edit</div>");
            }
            $row = mysqli_fetch_assoc($query);
          ?>
          <form action="editcontest.php?sent=1" method="POST" autocomplete="off">
              <p><label for="id"><?=i18n("editcontest", "id_field")?></label>: <input type="number" name="id" id="id" required="required" value="<?php echo $_GET['id']; ?>" readonly="readonly"></p>
              <p><label for="name"><?=i18n("editcontest", "name_field")?></label>: <input type="text" name="name" id="name" required="required" maxlength="50" value="<?=$row["name"]?>"></p>
              <p><label for="description"><?=i18n("editcontest", "description_field")?></label>:<br><textarea name="description" id="description" required="required" maxlength="1000" style="width: 300px; height: 100px;"><?=$row["description"]?></textarea></p>
              <p><label for="privacy"><?=i18n("editcontest", "privacy_field")?></label>: <select name="privacy" id="privacy"><option value="0"<?php if ($row["privacy"] == 0) echo " selected='selected'"; ?>><?=i18n("editcontest", "private")?></option><option value="1"<?php if ($row["privacy"] == 1) echo " selected='selected'"; ?>><?=i18n("editcontest", "semiprivate")?></option><option value="2"<?php if ($row["privacy"] == 2) echo " selected='selected'"; ?>><?=i18n("editcontest", "public")?></option></select></p>
              <p><label for="starttime"><?=i18n("editcontest", "starttime_field")?></label>: <input type="datetime-local" name="starttime" id="starttime" required="required" value="<?=date("Y-m-d\TH:i:s", $row["starttime"])?>"></p>
              <p><label for="endtime"><?=i18n("editcontest", "endtime_field")?></label>: <input type="datetime-local" name="endtime" id="endtime" required="required" value="<?=date("Y-m-d\TH:i:s", $row["endtime"])?>"></p>
  		        <p><input type="submit" value="<?=i18n("editcontest", "submit_btn")?>" class="button-link"></p>
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