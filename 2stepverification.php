<?php
require("core.php");
initi18n("2stepverification");
$msg = "";
if (isset($_GET['msg']) && in_array($_GET['msg'], array("configured", "disabled"))) {
  $msg = "<p class='alert-success'>".i18n("global", "msg_".$_GET['msg'])."</p>";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require ("head.php"); ?>
        <title><?=i18n("2stepverification", "title")?> - <?php echo $appname; ?></title>
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
                      die ("<div class='alert-danger'><p>".i18n("global", "notloggedin")."</p></div>");
                    }
                    ?>
                    <h1><?=i18n("2stepverification", "title")?></h1>
                    <?php
                    echo $msg;
                    if (twostepverification()) {
                        echo "<p><span class='icon svg-ic_verified_user_24px'></span> ".i18n("2stepverification", "enabled")."</p><p><a class='button-link' style='display:inline-block;' href='enable2stepverification.php'>".i18n("2stepverification", "setupagain")."</a> | <span class='icon svg-ic_vpn_key_24px'></span> <a href='securitykeys.php'>".i18n("2stepverification", "securitykeys")."</a> | <span class='icon svg-ic_cancel_24px'></span> <a href='disable2stepverification.php'>".i18n("2stepverification", "disable")."</a></p>";
                    } else {
                        echo "<p><span class='icon svg-ic_warning_24px'></span> ".i18n("2stepverification", "disabled")." <a href='enable2stepverification.php'>".i18n("2stepverification", "enable")."</a></p>";
                    }
                    ?>
                </div>
            </article>
        </div>
    </body>
</html>