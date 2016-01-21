<?php
require_once("core.php");
initi18n("register");
$msg = "";
if (isset($_GET['msg']) && in_array($_GET['msg'], array("emailincorrect", "usernametaken", "emailregistered", "password", "recaptcha", "empty"))) {
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
    <title><?=i18n("register", "title")?> – <?=$appname?></title>
    <script src='https://www.google.com/recaptcha/api.js?hl=<?=getlanguagei18n()?>'></script>
  </head>
  <body>
    <div class="content">
      <?php require("nav.php"); ?>
    	<article>
    		<div class="text" style='margin-top:10px;'>
    		  <h1 style='text-align:center;'><?=i18n("register", "title")?></h1>
          <?=$msg?>
          <?php
          if (isset($_GET["sent"]) && $_GET["sent"] == 1) {
            $username = htmlspecialchars(mysqli_real_escape_string($con, $_POST['username']));
            $name = htmlspecialchars(mysqli_real_escape_string($con, $_POST['name']));
            $surname = htmlspecialchars(mysqli_real_escape_string($con, $_POST['surname']));
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $password = mysqli_real_escape_string($con, $_POST['password']);
            $recaptcha = $_POST['g-recaptcha-response'];

            $response_recaptcha = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$conf["recaptcha"]["secretkey"]."&response=".$recaptcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);

            if ($response_recaptcha["success"] === false) {
              header("Location: register.php?msg=recaptcha");
              exit();
            }

            if (empty($username) || empty($name) || empty($surname) || empty($email) || empty($password)) {
              header("Location: register.php?msg=empty");
              exit();
            }

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
              header("Location: register.php?msg=emailincorrect");
              exit();
            }

            if (isset($conf["email_domain"]) && !empty($conf["email_domain"]) && explode("@", $_POST['email'])[1] != $conf["email_domain"]) {
              header("Location: register.php?msg=emaildomain");
              exit();
            }

            if (strlen($password) < 6) {
              header("Location: register.php?msg=password");
              exit();
            }

            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE username = '".$username."'"))) {
              header("Location: register.php?msg=usernametaken");
              exit();
            }

            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE email = '".$email."'"))) {
              header("Location: register.php?msg=emailregistered");
              exit();
            }

            if (mysqli_query($con, "INSERT INTO users (username, name, surname, email, role, password) VALUES ('".$username."', '".$name."', '".$surname."', '".$email."', 0, '".md5($password)."')")) {
              header("Location: index.php?msg=registersuccess");
            } else {
              die("<div class='alert alert-danger'>Error registrando al concursante :-/</div>");
            }
          } else {
          ?>
      		<p><?=i18n("register", "introduction", array($appname))?></p>
          <form action="register.php?sent=1" method="POST">
              <p><label for="username"><?=i18n("register", "username_field")?></label>: <input type="text" name="username" id="username" required="required" maxlength="50"></p>
              <p><label for="name"><?=i18n("register", "name_field")?></label>: <input type="text" name="name" id="name" required="required" maxlength="50"></p>
              <p><label for="surname"><?=i18n("register", "surname_field")?></label>: <input type="text" name="surname" id="surname" required="required" maxlength="100"></p>
              <p><label for="email"><?=i18n("register", "email_field")?></label>: <input type="email" name="email" id="email" required="required" maxlength="100"></p>
              <p><label for="password"><?=i18n("register", "password_field")?></label>: <input type="password" name="password" id="password" required="required" maxlength="50"></p>
              <div class="g-recaptcha" data-sitekey="<?=$conf["recaptcha"]["sitekey"]?>"></div>
  		        <p><input type="submit" value="Regístrate" class="button-link"></p>
          </form>
          <?php
          }
          ?>
    		</div>
    	</article>
    </div>
  </body>
</html>