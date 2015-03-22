<?php
require_once("core.php");
require("lib/u2flib_server/loadU2F.php");

if (!twostepverification()) {
  header("Location: 2stepverification.php");
  exit;
}

$query = mysqli_query($con, "SELECT keyHandle FROM securitykeys WHERE user_id = '".$_SESSION['id']."'") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
$row = array();
if (mysqli_num_rows($query)) {
    while ($row[] = mysqli_fetch_assoc($query)) {}
    array_pop($row);
    foreach ($row as $key => $value) {
        $row[$key] = json_decode(json_encode($value));
    }
}

try {
    $data = $u2f->getRegisterData($row);
    list($req,$sigs) = $data;
} catch( Exception $e ) {
    echo "Error: ".$e->getMessage();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require ("head.php"); ?>
        <title>Administrar llaves de seguridad - <?php echo $appname; ?></title>
        <style>
        h1 span {
            vertical-align: middle;
        }

        .step {
            padding: 10px 0;
            border-bottom: 1px solid #ebebeb;
        }

        .step .number {
            display: inline-block;
            vertical-align: middle;
            font-family: "Arial", sans-serif;
            font-size: 36px;
            font-weight: bold;
            color: green;
            margin: 0;
            margin-right: 15px;
            padding: 0;
            line-height: normal;
        }

        .step .text {
            display: inline-block;
            vertical-align: middle;
            margin: 0;
            padding: 0;
        }

        .step .icon_container {
            float: right;
            height: 24px;
            padding-top: 9px;
            padding-right: 9px;
        }

        .small_view {
            max-width: 670px;
            margin-left: auto;
            margin-right: auto;
        }

        [flex] {
            -ms-flex: 1 1 0.000000001px;
            -webkit-flex: 1;
            flex: 1;
            -webkit-flex-basis: 0.000000001px;
            flex-basis: 0.000000001px;
        }
        
        #registrar_container div {
            margin-top: 8px;
        }
        
        #waiting_usb span {
            vertical-align: middle;
        }

        /* Material Design Spinner – http://codepen.io/mrrocks/pen/EiplA */
        .spinner {
          -webkit-animation: rotator 1.4s linear infinite;
                  animation: rotator 1.4s linear infinite;
          margin-right: 5px;
          vertical-align: middle;
        }

        @-webkit-keyframes rotator {
          0% {
            -webkit-transform: rotate(0deg);
                    transform: rotate(0deg);
          }
          100% {
            -webkit-transform: rotate(270deg);
                    transform: rotate(270deg);
          }
        }

        @keyframes rotator {
          0% {
            -webkit-transform: rotate(0deg);
                    transform: rotate(0deg);
          }
          100% {
            -webkit-transform: rotate(270deg);
                    transform: rotate(270deg);
          }
        }
        .path {
          stroke-dasharray: 187;
          stroke-dashoffset: 0;
          -webkit-transform-origin: center;
              -ms-transform-origin: center;
                  transform-origin: center;
          -webkit-animation: dash 1.4s ease-in-out infinite, colors 5.6s ease-in-out infinite;
                  animation: dash 1.4s ease-in-out infinite, colors 5.6s ease-in-out infinite;
        }

        @-webkit-keyframes colors {
          0% {
            stroke: #4285F4;
          }
          25% {
            stroke: #DE3E35;
          }
          50% {
            stroke: #F7C223;
          }
          75% {
            stroke: #1B9A59;
          }
          100% {
            stroke: #4285F4;
          }
        }

        @keyframes colors {
          0% {
            stroke: #4285F4;
          }
          25% {
            stroke: #DE3E35;
          }
          50% {
            stroke: #F7C223;
          }
          75% {
            stroke: #1B9A59;
          }
          100% {
            stroke: #4285F4;
          }
        }
        @-webkit-keyframes dash {
          0% {
            stroke-dashoffset: 187;
          }
          50% {
            stroke-dashoffset: 46.75;
            -webkit-transform: rotate(135deg);
                    transform: rotate(135deg);
          }
          100% {
            stroke-dashoffset: 187;
            -webkit-transform: rotate(450deg);
                    transform: rotate(450deg);
          }
        }
        @keyframes dash {
          0% {
            stroke-dashoffset: 187;
          }
          50% {
            stroke-dashoffset: 46.75;
            -webkit-transform: rotate(135deg);
                    transform: rotate(135deg);
          }
          100% {
            stroke-dashoffset: 187;
            -webkit-transform: rotate(450deg);
                    transform: rotate(450deg);
          }
        }
        </style>
        <script src="chrome-extension://pfboblefjcgdjicmnffhdgionmgcdmne/u2f-api.js"></script>
        <script>
        <?php
        echo "var req = " . json_encode($req) . ";";
        echo "var sigs = " . json_encode($sigs) . ";";
        ?>
        </script>
        <script src="js/addsecuritykey.js"></script>
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
                    <h1><a href="securitykeys.php"><span class='icon svg-ic_chevron_left_24px'></span></a> <span>Añadir llave de seguridad</span></h1>
                    <img src="img/security-key.png" style="float: right;">
                    <p style="margin-bottom: 20px;">Puedes añadir una llave de seguridad a tu cuenta para que el proceso de inicio de sesión sea más seguro.</p>
                    <p style="margin-bottom: 0;">Para ello, sigue los siguientes pasos:</p>
                    <div class="step">
                        <div class="number">1</div><div class="text"><b>Instala la extensión <a href="https://chrome.google.com/webstore/detail/fido-u2f-universal-2nd-fa/pfboblefjcgdjicmnffhdgionmgcdmne" target="_blank">FIDO U2F</a> de la Chrome Web Store.</b></div><div hidden flex class="icon_container" id="extension"><span class="icon svg-ic_done_24px"></span></div>
                    </div>
                    <div class="step">
                        <div class="number">2</div><div class="text"><b>Asegúrate de que tienes a mano una llave de seguridad</b><br>¿No tienes una llave de seguridad? <a href="https://support.google.com/accounts/answer/6103523?hl=es" target="_blank">Más información</a></div>
                    </div>
                    <div class="step">
                        <div class="number">3</div><div class="text"><b>Retira la llave de seguridad si ya la has insertado.</b></div>
                    </div>
                    <div class="step" style="margin-bottom: 5px">
                        <div class="number">4</div><div class="text"><b>Haz clic en Registrar y, a continuación, introduce tu llave de seguridad en un puerto USB.</b><br>Si tu llave de seguridad tiene un botón o un disco dorado, tócalo.</div>
                    </div>
                    <div id="registrar_container">
                        <button id="registrar" class="button-link">Registrar</button>
                        <div hidden id="waiting_usb">
                            <svg class="spinner" width="24px" height="24px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                                <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                            </svg>
                            <span>Ahora introduce (y toca) tu llave de seguridad.</span>
                        </div>
                        <div hidden id="done">
                            <span class="icon svg-ic_done_all_24px"></span>
                            <span>¡Listo!</span>
                        </div>
                        <div hidden id="usb_error_1">
                            <span class="icon svg-ic_error_24px"></span>
                            <span>Ha ocurrido un error inesperado. Por favor, intenta de nuevo. <span style="color: gray;font-size: 12px;">(OTHER_ERROR)</span></span>
                        </div>
                        <div hidden id="usb_error_2">
                            <span class="icon svg-ic_error_24px"></span>
                            <span>Oh, vaya. Ha ocurrido un error inesperado. <span style="color: gray;font-size: 12px;">(BAD_REQUEST)</span></span>
                        </div>
                        <div hidden id="usb_error_3">
                            <span class="icon svg-ic_error_24px"></span>
                            <span>Parece que la configuración no es está soportada. <span style="color: gray;font-size: 12px;">(CONFIGURATION_UNSUPPORTED)</span></span>
                        </div>
                        <div hidden id="usb_error_4">
                            <span class="icon svg-ic_warning_24px"></span>
                            <span>Esta llave ya está asociada a tu cuenta. <span style="color: gray;font-size: 12px;">(DEVICE_INELIGIBLE)</span></span>
                        </div>
                        <div hidden id="usb_error_5">
                            <span class="icon svg-ic_timer_24px"></span>
                            <span>Se ha agotado el tiempo de espera. Por favor, intenta de nuevo. <span style="color: gray;font-size: 12px;">(TIMEOUT)</span></span>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </body>
</html>