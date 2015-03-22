<?php
require_once("core.php");
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "empty")
  $msg = '<p class="alert-warning">Por favor, introduce tu contraseña</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "passworddoesntmatch")
  $msg = '<p class="alert-danger">La contraseña que has introducido no es correcta</p>';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require ("head.php"); ?>
        <title>Inhabilitar verificación en 2 pasos - <?php echo $appname; ?></title>
        <style>
        .small_view {
            max-width: 670px;
            margin-left: auto;
            margin-right: auto;
        }
        </style>
    </head>
    <body>
        <div class="content">
            <?php include "nav.php"; ?>
            <article>
                <?php anuncio(); ?>
                <div class="text small_view">
                    <?php
                    if (!loggedin())
                    {
                      die ("<div class='alert-danger'><p>¡No estás connectado! <a href='index.php'>Conéctate</a></p></div>");
                    }
                    ?>
                    <h1>Inhabilitar verificación en 2 pasos</h1>
                    <?php
                    echo $msg;
                    if (!twostepverification()) {
                        header("Location: 2stepverification.php");
                        exit;
                    }
                    if (isset($_POST["password"])) {
                        if (empty($_POST["password"])) {
                            header("Location: disable2stepverification.php?msg=empty");
                            exit;
                        }
                        $password = mysqli_real_escape_string($con, $_POST["password"]);
                        $query = mysqli_query($con, "SELECT * FROM users WHERE id=".$_SESSION['id']." and password='".md5($password)."'");
                        if (mysqli_num_rows($query)) {
                            $sql = "DELETE FROM securitykeys WHERE user_id = ".$_SESSION['id'];
                            $sql2 = "DELETE FROM 2stepverification WHERE user_id = ".$_SESSION['id']." LIMIT 1";
                            if (mysqli_query($con, $sql)) {
                                if (mysqli_query($con, $sql2)) {
                                    header("Location: 2stepverification.php?msg=disabled");
                                } else {
                                    die ("<p class='alert-danger'>Error inhabilitando la verificación en 2 pasos (las llaves de seguridad sí se han eliminado): " . mysqli_error($con) . "</p>");
                                }
                            } else {
                                die ("<p class='alert-danger'>Error eliminando las llaves de seguridad: " . mysqli_error($con) . "</p>");
                            }
                        } else {
                            header("Location: disable2stepverification.php?msg=passworddoesntmatch");
                            exit;
                        }
                    } else {
                    ?>
                    <p>¿Estás seguro de que quieres desactivar la verificación en 2 pasos?</p>
                    <p>La verificación en 2 pasos ofrece seguridad extra a tu cuenta. Si inhabilitas la verificación en 2 pasos todas tus llaves de seguridad se desvincularán de esta cuenta y no te pediremos ningún código de verificación al iniciar sesión.</p>
                    <p>Si después de pensártelo dos veces sigues estando seguro introduce tu contraseña y haz clic en el botón rojo.</p>
                    <form action="disable2stepverification.php" method="POST">
                        <p><label for="password">Contraseña:</label> <input type="password" id="password" name="password"></p>
                        <p><input type="submit" href="disable2stepverification.php" class="button-link-red" value="Sí, estoy segurísimo"> <a href="2stepverification.php" class="button-link">¡De ninguna manera!</a></p>
                    </form>
                    <?php
                    }
                    ?>
                </div>
            </article>
        </div>
    </body>
</html>