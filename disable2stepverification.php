<?php
require_once("core.php");
initi18n("disable2stepverification");
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "empty")
  $msg = '<p class="alert-warning">'.i18n("global", "msg_empty2").'</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "passworddoesntmatch")
  $msg = '<p class="alert-danger">'.i18n("global", "msg_passworddoesntmatch").'</p>';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require ("head.php"); ?>
        <title><?=i18n("disable2stepverification", "title")?> - <?php echo $appname; ?></title>
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
                      die ("<div class='alert-danger'>".i18n("global", "notloggedin")."</div>");
                    }
                    ?>
                    <h1><?=i18n("disable2stepverification", "title")?></h1>
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
                        $query = mysqli_query($con, "SELECT * FROM users WHERE id=".$_SESSION['id']);
                        if (mysqli_num_rows($query)) {
                            $row = mysqli_fetch_assoc($query);
                            if (password_verify($_POST["password"], $row["password"])) {
                                $sql = "DELETE FROM securitykeys WHERE user_id = ".$_SESSION['id'];
                                $sql2 = "DELETE FROM 2stepverification WHERE user_id = ".$_SESSION['id']." LIMIT 1";
                                if (mysqli_query($con, $sql)) {
                                    if (mysqli_query($con, $sql2)) {
                                        header("Location: 2stepverification.php?msg=disabled");
                                    } else {
                                        die ("<p class='alert-danger'>".i18n("disable2stepverification", "error_disable")."</p>");
                                    }
                                } else {
                                    die ("<p class='alert-danger'>".i18n("disable2stepverification", "error_deletesecuritykeys")."</p>");
                                }
                            } else {
                                header("Location: disable2stepverification.php?msg=passworddoesntmatch");
                                exit;
                            }
                        }
                    } else {
                    ?>
                    <p><?=i18n("disable2stepverification", "paragraph1")?></p>
                    <p><?=i18n("disable2stepverification", "paragraph2")?></p>
                    <p><?=i18n("disable2stepverification", "paragraph3")?></p>
                    <form action="disable2stepverification.php" method="POST">
                        <p><label for="password"><?=i18n("global", "password")?>:</label> <input type="password" id="password" name="password"></p>
                        <p><input type="submit" href="disable2stepverification.php" class="button-link-red" value="<?=i18n("disable2stepverification", "yes")?>"> <a href="2stepverification.php" class="button-link"><?=i18n("disable2stepverification", "no")?></a></p>
                    </form>
                    <?php
                    }
                    ?>
                </div>
            </article>
        </div>
    </body>
</html>