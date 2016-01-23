<?php
require_once("core.php");
initi18n("edituser");
if (isadmin()) {
$msg = "";
if (isset($_GET['msg']) && in_array($_GET['msg'], array("emailincorrect", "empty", "emaildomain", "usernametaken", "emailregistered", "password", "uniquehyperadmin"))) {
  if (isset($_GET['msg']) && $_GET['msg'] == "emaildomain") {
    $msg = '<p class="alert-warning">'.i18n("global", "msg_emaildomain", array($conf["email_domain"])).'</p>';
  } else {
    $msg = '<p class="alert-danger">'.i18n("global", "msg_".$_GET['msg']).'</p>';
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title><?=i18n("edituser", "title")?> - <?php echo $appname; ?></title>
</head>
<body>
<div class="content">
      <?php include "nav.php"; ?>
      <article>
            <?php anuncio(); ?>
            <?php require("sidebar.php"); ?>
            <div class="text right large">
            <h1><?=i18n("edituser", "title")?></h1>
            <?=$msg?>
            <?php
            if (isset($_GET['sent']) && $_GET['sent'] == "1") {
            $id = (int)$_POST['id'];
            $name = htmlspecialchars(mysqli_real_escape_string($con, $_POST['name']));
            $surname = htmlspecialchars(mysqli_real_escape_string($con, $_POST['surname']));
            $email = mysqli_real_escape_string($con, $_POST['email']);
            if (!empty($_POST["password"]))
              $password = mysqli_real_escape_string($con, $_POST['password']);
            $role = (int)$_POST['role'];

            if (empty($name) || empty($surname) || empty($email) || (empty($role) && $role != 0)) {
              header("Location: edituser.php?id=".$_POST['id']."&msg=empty");
              exit();
            }

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
              header("Location: edituser.php?id=".$_POST['id']."&msg=emailincorrect");
              exit();
            }

            if (isset($password) && strlen($password) < 6) {
              header("Location: edituser.php?id=".$_POST['id']."&msg=password");
              exit();
            }
            if (isset($password)) {
              $password_query = ", password='".password_hash($_POST["password"], PASSWORD_DEFAULT)."'";
            } else {
              $password_query = "";
            }

            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE email = '".$email."' AND id != ".$id))) {
              header("Location: edituser.php?id=".$_POST['id']."&msg=emailregistered");
              exit();
            }

            if ($role > 3 || $role < 0) {
              header("Location: edituser.php?id=".$_POST['id']."&msg=rolewrong");
              exit();
            }

            if ($id == $_SESSION["id"] && $role != 3) {
              if (mysqli_num_rows(mysqli_query($con, "SELECT id FROM users WHERE role = 3")) < 2) {
                header("Location: edituser.php?id=".$_POST['id']."&msg=uniquehyperadmin");
              exit();
              }
            }

            $sql6 = "UPDATE users set name='".$name."', surname='".$surname."', email='".$email."', role='".$role."'".$password_query." WHERE ID = '".$id."' LIMIT 1";
            if (mysqli_query($con,$sql6))
              {
              header("Location: users.php?msg=newsuccessful");
              }
            else
              {
              die ("<p class='alert-danger'>".i18n("edituser", "mysql_error")." " . mysqli_error($con) . "</p>");
              }
            }
            else
            {
            $query = mysqli_query($con, "SELECT * FROM users WHERE ID = '".mysqli_real_escape_string($con, $_GET['id'])."'") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
			       $row = mysqli_fetch_assoc($query);
            ?>
            <form action="edituser.php?sent=1" method="POST" id="install-form" autocomplete="off">
              <p><label for="id"><?=i18n("global", "id")?></label>: <input type="number" name="id" id="id" required="required" value="<?php echo $row['id']; ?>" readonly="readonly"></p>
              <p><label for="username"><?=i18n("global", "username")?></label>: <input type="text" name="username" id="username" required="required" value="<?php echo $row['username']; ?>" readonly="readonly"></p>
              <p><label for="name"><?=i18n("global", "name")?></label>: <input type="text" name="name" id="name" required="required" value="<?php echo $row['name']; ?>" maxlength="50"></p>
              <p><label for="surname"><?=i18n("global", "surname")?></label>: <input type="text" name="surname" id="surname" required="required" value="<?php echo $row['surname']; ?>" maxlength="100"></p>
              <p><label for="email"><?=i18n("global", "email")?></label>: <input type="email" name="email" id="email" required="required" value="<?php echo $row['email']; ?>" maxlength="100"></p>
              <p><label for="password"><?=i18n("global", "password")?></label>: <input type="password" name="password" id="password" maxlength="50"> <span style="color:gray;"><?=i18n("edituser", "password_helper")?></span></p>
              <p><label for="role"><?=i18n("global", "role")?></label>: <select name="role"><?php for ($i = 0; $i < 4; $i++) { ?><option value="<?=$i?>"<?php if ($row["role"] == $i) echo " selected='selected'"; ?>><?=i18n("global", "role_".$i)?></option><?php } ?></select></p>
              <p><input type="submit" class="button-link" value="<?=i18n("edituser", "submit_btn")?>"></p>
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
} else {
      header('HTTP/1.0 404 Not Found');
}
?>