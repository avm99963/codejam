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
<title>Administrar llaves de seguridad - <?php echo $appname; ?></title>
<style>
td, th
{
  padding:5px;
}
table
{
  border-collapse:collapse;
  margin-bottom: 10px;
}
table, th, td
{
  border: 1px solid black;
}
thead {
  font-weight: bold;
}
h1 span {
  vertical-align: middle;
}
</style>
</head>
<body>
<div class="content">
      <?php include "nav.php"; ?>
      <article>
            <?php anuncio(); ?>
            <?php if (getrole() > 0) {
              require("sidebar.php");
            ?>
            <div class="text right large">
            <?=$msg?>
            <?php } else { ?>
            <div class="text">
            <?php } ?>
            <h1><a href="2stepverification.php"><span class='icon svg-ic_chevron_left_24px'></span></a> <span>Administrar llaves de seguridad</span></h1>
            <?=$msg?>
            <?php
            if (!twostepverification()) {
              header("Location: 2stepverification.php");
              exit;
            }
            $query = mysqli_query($con, "SELECT * FROM securitykeys WHERE user_id = '".$_SESSION['id']."'") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
            if (mysqli_num_rows($query)) {
              ?>
              <table>
                <thead>
                  <tr><td colspan="2">Dispositivo donde se añadió</td><td colspan="2">Dispositivo donde se ha insertado la última vez</td></tr>
                  <tr><td>Dirección IP</td><td>Fecha</td><td>Dirección IP</td><td>Fecha</td></tr>
                </thead>
                <tbody>
              <?php
              while ($row = mysqli_fetch_assoc($query)) {
                if (empty($row["lastuseddate"])) {
                  $lastuseddevice = "-";
                  $lastuseddate = "-";
                } else {
                  $lastuseddevice = $row["lastuseddevice"];
                  $lastuseddate = date("d/m/Y H:i:s", $row["lastuseddate"]);
                }
                echo "<tr><td>".$row["deviceadded"]."</td><td>".date("d/m/Y H:i:s", $row["dateadded"])."</td><td>".$lastuseddevice."</td><td>".$lastuseddate."</td><td><a href='deletesecuritykey.php?id=".$row['id']."'><span class='icon svg-ic_delete_24px'></span></a></td></tr>";
              }
              ?>
                </tbody>
              </table>
              <?php
            } else {
              ?>
              <p style="color: gray; text-align: center;">Oh vaya... Todavía no tienes asociada ninguna llave de seguridad :-(</p>
              <?php
            }
            ?>
            <a href="addsecuritykey.php" class="button-link" style="display: inline-block;">Añadir</a>
            </div>
      </article>
</div>
</body>
</html>