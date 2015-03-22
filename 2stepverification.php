<?php
require("core.php");
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "configured")
  $msg = '<p class="alert-success">La verificación en 2 pasos ha sido configurada satisfactoriamente</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "disabled")
  $msg = '<p class="alert-success">La verificación en 2 pasos se ha inhabilitado satisfactoriamente</p>';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require ("head.php"); ?>
        <title>Verificación en 2 pasos - <?php echo $appname; ?></title>
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
                <?php } else { ?>
                <div class="text">
                <?php } ?>
                    <?php
                    if (!loggedin())
                    {
                      die ("<div class='alert-danger'><p>¡No estás connectado! <a href='index.php'>Conéctate</a></p></div>");
                    }
                    ?>
                    <h1>Verificación en 2 pasos</h1>
                    <?php
                    echo $msg;
                    if (twostepverification()) {
                        echo "<p><span class='icon svg-ic_verified_user_24px'></span> La verificación en 2 pasos está activada.</p><p><a class='button-link' style='display:inline-block;' href='enable2stepverification.php'>Configúralo de nuevo</a> | <span class='icon svg-ic_vpn_key_24px'></span> <a href='securitykeys.php'>Administra las llaves de seguridad</a> | <span class='icon svg-ic_cancel_24px'></span> <a href='disable2stepverification.php'>Desactívalo</a></p>";
                    } else {
                        echo "<p><span class='icon svg-ic_warning_24px'></span> La verificación en 2 pasos no está activada. <a href='enable2stepverification.php'>Actívala</a></p>";
                    }
                    ?>
                </div>
            </article>
        </div>
    </body>
</html>