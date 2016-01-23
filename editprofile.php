<?php
require_once("core.php");
initi18n("editprofile");
$msg = "";
if (isset($_GET['msg']) && in_array($_GET['msg'], array("usereditsuccess", "emailincorrect", "usernametaken", "emailregistered", "password", "recaptcha", "empty"))) {
  if ($_GET['msg'] == "emaildomain") {
    $msg = '<p class="alert-warning">'.i18n("global", "msg_emaildomain", array($conf["email_domain"])).'</p>';
  } elseif ($_GET['msg'] == "usereditsuccess") {
    $msg = '<p class="alert-success">'.i18n("global", "msg_".$_GET['msg']).'</p>';
  } else {
    $msg = '<p class="alert-danger">'.i18n("global", "msg_".$_GET['msg']).'</p>';
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title><?=i18n("editprofile", "title")?> - <?php echo $appname; ?></title>
</head>
<body>
<div class="content">
      <?php include "nav.php"; ?>
      <article>
            <?php anuncio(); ?>
            <div class="text">
            <h1><?=i18n("editprofile", "title")?></h1>
            <?=$msg?>
            <?php
            if (isset($_GET['sent']) && $_GET['sent'] == "1") {
            $id = $_SESSION['id'];
            $name = htmlspecialchars(mysqli_real_escape_string($con, $_POST['name']));
            $surname = htmlspecialchars(mysqli_real_escape_string($con, $_POST['surname']));
            $email = mysqli_real_escape_string($con, $_POST['email']);
            if (!empty($_POST["password"]))
              $password = mysqli_real_escape_string($con, $_POST['password']);

            if (empty($name) || empty($surname) || empty($email)) {
              header("Location: editprofile.php?id=".$_POST['id']."&msg=empty");
              exit();
            }

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
              header("Location: editprofile.php?id=".$_POST['id']."&msg=emailincorrect");
              exit();
            }

            if (isset($conf["email_domain"]) && !empty($conf["email_domain"]) && explode("@", $_POST['email'])[1] != $conf["email_domain"]) {
              header("Location: editprofile.php?msg=emaildomain");
              exit();
            }

            if (isset($password) && strlen($password) < 6) {
              header("Location: editprofile.php?id=".$_POST['id']."&msg=password");
              exit();
            }
            if (isset($password)) {
              $password_query = ", password='".password_hash($_POST["password"], PASSWORD_DEFAULT)."'";
            } else {
              $password_query = "";
            }

            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE email = '".$email."' AND id != ".$id))) {
              header("Location: editprofile.php?id=".$_POST['id']."&msg=emailregistered");
              exit();
            }

            $sql6 = "UPDATE users set name='".$name."', surname='".$surname."', email='".$email."'".$password_query." WHERE ID = '".$id."' LIMIT 1";
            if (mysqli_query($con,$sql6))
              {
              header("Location: editprofile.php?msg=usereditsuccess");
              }
            else
              {
              die ("<p class='alert-danger'>Error editando el usuario: " . mysqli_error($con) . "</p>");
              }
            }
            else
            {
            $query = mysqli_query($con, "SELECT * FROM users WHERE ID = '".$_SESSION['id']."'") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
			       $row = mysqli_fetch_assoc($query);
            ?>
            <form action="editprofile.php?sent=1" method="POST" id="install-form" autocomplete="off">
              <p><label for="id"><?=i18n("global", "id")?></label>: <input type="number" name="id" id="id" required="required" value="<?php echo $row['id']; ?>" readonly="readonly"></p>
              <p><label for="username"><?=i18n("global", "username")?></label>: <input type="text" name="username" id="username" required="required" value="<?php echo $row['username']; ?>" readonly="readonly"></p>
              <p><label for="name"><?=i18n("global", "name")?></label>: <input type="text" name="name" id="name" required="required" value="<?php echo $row['name']; ?>" maxlength="50"></p>
              <p><label for="surname"><?=i18n("global", "surname")?></label>: <input type="text" name="surname" id="surname" required="required" value="<?php echo $row['surname']; ?>" maxlength="100"></p>
              <p><label for="email"><?=i18n("global", "email")?></label>: <input type="email" name="email" id="email" required="required" value="<?php echo $row['email']; ?>" maxlength="100"></p>
              <p><label for="password"><?=i18n("global", "password")?></label>: <input type="password" name="password" id="password" maxlength="50"> <span style="color:gray;"><?=i18n("editprofile", "passwordhelper")?></span></p>
              <p><span class="icon svg-ic_security_24px"></span> <a href="2stepverification.php"><?=i18n("editprofile", "2stepverification")?></a></p>
              <p><input type="submit" class="button-link" value="<?=i18n("editprofile", "editbtn")?>"></p>
            </form>
            <?php
		        }
            ?>
            </div>
      </article>
</div>
</body>
</html>