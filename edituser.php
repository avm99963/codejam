<?php
require_once("core.php");
if (isadmin())
{
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
if (isset($_GET['msg']) && $_GET['msg'] == "uniquehyperadmin")
  $msg = '<p class="alert-danger">¡No puedes quitar el rol de hyperadmin al único hyperadmin!</p>';
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title>Editar usuario - <?php echo $appname; ?></title>
</head>
<body>
<div class="content">
      <?php include "nav.php"; ?>
      <article>
            <?php anuncio(); ?>
            <?php require("sidebar.php"); ?>
            <div class="text right large">
            <h1>Editar usuario</h1>
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
              $password_query = ", password='".md5($password)."'";
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
              die ("<p class='alert-danger'>Error editando el usuario: " . mysqli_error($con) . "</p>");
              }
            }
            else
            {
            $query = mysqli_query($con, "SELECT * FROM users WHERE ID = '".mysqli_real_escape_string($con, $_GET['id'])."'") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
			       $row = mysqli_fetch_assoc($query);
            ?>
            <form action="edituser.php?sent=1" method="POST" id="install-form" autocomplete="off">
              <p><label for="id">ID</label>: <input type="number" name="id" id="id" required="required" value="<?php echo $row['id']; ?>" readonly="readonly"></p>
              <p><label for="username">Usuario</label>: <input type="text" name="username" id="username" required="required" value="<?php echo $row['username']; ?>" readonly="readonly"></p>
              <p><label for="name">Nombre</label>: <input type="text" name="name" id="name" required="required" value="<?php echo $row['name']; ?>" maxlength="50"></p>
              <p><label for="surname">Apellidos</label>: <input type="text" name="surname" id="surname" required="required" value="<?php echo $row['surname']; ?>" maxlength="100"></p>
              <p><label for="email">Email</label>: <input type="email" name="email" id="email" required="required" value="<?php echo $row['email']; ?>" maxlength="100"></p>
              <p><label for="password">Contraseña</label>: <input type="password" name="password" id="password" maxlength="50"> <span style="color:gray;">Dejar en blanco si no quieres cambiar la contraseña</span></p>
              <p><label for="role">Rol</label>: <select name="role"><option value="0"<?php if ($row["role"] == 0) echo " selected='selected'"; ?>>Contestant</option><option value="1"<?php if ($row["role"] == 1) echo " selected='selected'"; ?>>Judge</option><option value="2"<?php if ($row["role"] == 2) echo " selected='selected'"; ?>>Problem writer</option><option value="3"<?php if ($row["role"] == 3) echo " selected='selected'"; ?>>Hyperadmin</option></select></p>
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
<?php
}
else
{
      header('HTTP/1.0 404 Not Found');
}
?>