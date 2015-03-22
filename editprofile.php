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
if (isset($_GET['msg']) && $_GET['msg'] == "editsuccess")
  $msg = '<p class="alert-success">Has editado tu perfil correctamente.</p>';
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title>Editar perfil - <?php echo $appname; ?></title>
</head>
<body>
<div class="content">
      <?php include "nav.php"; ?>
      <article>
            <?php anuncio(); ?>
            <div class="text">
            <h1>Editar perfil</h1>
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
              $password_query = ", password='".md5($password)."'";
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
              header("Location: editprofile.php?msg=editsuccess");
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
              <p><label for="id">ID</label>: <input type="number" name="id" id="id" required="required" value="<?php echo $row['id']; ?>" readonly="readonly"></p>
              <p><label for="username">Usuario</label>: <input type="text" name="username" id="username" required="required" value="<?php echo $row['username']; ?>" readonly="readonly"></p>
              <p><label for="name">Nombre</label>: <input type="text" name="name" id="name" required="required" value="<?php echo $row['name']; ?>" maxlength="50"></p>
              <p><label for="surname">Apellidos</label>: <input type="text" name="surname" id="surname" required="required" value="<?php echo $row['surname']; ?>" maxlength="100"></p>
              <p><label for="email">Email</label>: <input type="email" name="email" id="email" required="required" value="<?php echo $row['email']; ?>" maxlength="100"></p>
              <p><label for="password">Contraseña</label>: <input type="password" name="password" id="password" maxlength="50"> <span style="color:gray;">Dejar en blanco si no quieres cambiar la contraseña</span></p>
              <p><span class="icon svg-ic_security_24px"></span> <a href="2stepverification.php">Verificación en 2 pasos</a></p>
              <p><input type="submit" class="button-link" value="Editar usuario"></p>
            </form>
            <?php
		        }
            ?>
            </div>
      </article>
</div>
</body>
</html>