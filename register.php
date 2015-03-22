<?php
require_once("core.php");
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "emailincorrect")
  $msg = '<p class="alert-danger">Por favor, introduce una dirección de correo electrónico correcta.</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "empty")
  $msg = '<p class="alert-danger">Por favor, rellena todos los campos</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "emaildomain")
  $msg = '<p class="alert-danger">La dirección de correo electrónico debe estar en el dominio '.$conf["email_domain"].'.</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "usernametaken")
  $msg = '<p class="alert-danger">El nombre de usuario escogido ya está siendo usado por otro participante. Por favor, escoge otro.</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "emailregistered")
  $msg = '<p class="alert-danger">Ya hay un concursante inscrito con esta dirección de correo electrónico. Por favor, introduce una diferente.</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "password")
  $msg = '<p class="alert-danger">La contraseña debe contener como mínimo 6 caracteres.</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "recaptcha")
  $msg = '<p class="alert-danger">Por favor, rellena otra vez el captcha.</p>';
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require ("head.php"); ?>
    <title>Formulario de registro – <?=$appname?></title>
    <script src='https://www.google.com/recaptcha/api.js'></script>
  </head>
  <body>
    <div class="content">
      <?php require("nav.php"); ?>
    	<article>
    		<div class="text" style='margin-top:10px;'>
    		  <h1 style='text-align:center;'>Formulario de registro</h1>
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
      		<p>Rellena el siguiente formulario para inscribirte como concursante en "<?=$appname?>":</p>
          <form action="register.php?sent=1" method="POST">
              <p><label for="username">Usuario</label>: <input type="text" name="username" id="username" required="required" maxlength="50"></p>
              <p><label for="name">Nombre</label>: <input type="text" name="name" id="name" required="required" maxlength="50"></p>
              <p><label for="surname">Apellidos</label>: <input type="text" name="surname" id="surname" required="required" maxlength="100"></p>
              <p><label for="email">Email</label>: <input type="email" name="email" id="email" required="required" maxlength="100"></p>
              <p><label for="password">Contraseña</label>: <input type="password" name="password" id="password" required="required" maxlength="50"></p>
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