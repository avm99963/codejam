<?php
require_once("core.php");
require("lib/GoogleAuthenticator/GoogleAuthenticator.php");

$authenticator = new GoogleAuthenticator();

$secret = $authenticator->generateSecret();

$url = "otpauth://totp/".str_replace("+", "%20", urlencode($appname)).":".userdata('username')."?secret=".urlencode($secret)."&issuer=".str_replace("+", "%20", urlencode($appname));

$qrcode = "http://chart.apis.google.com/chart?cht=qr&chs=200x200&chl=".urlencode($url)."&chld=H|0";
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require ("head.php"); ?>
        <title>Activar verificación en 2 pasos - <?php echo $appname; ?></title>
        <style>
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
            width: Calc(100% - 40px);
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

        #verificationcode {
            width: 58px;
        }

        #verify_container div {
            margin-top: 8px;
        }

        #verify {
            outline: 0;
        }

        #cancel {
            font-family: "Arial";
            font-size: 13px;
            text-decoration: none;
            margin-left: 10px;
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
        <script>
        var secret = "<?=$secret?>";
        </script>
        <script src="js/enable2stepverification.js"></script>
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
                    <h1>Activar verificación en 2 pasos</h1>
                    <p>Para activar la verificación en 2 pasos, sigue los siguientes pasos:</p>
                    <div class="step">
                        <div class="number">1</div><div class="text"><b>Instala la aplicación <a href="https://m.google.com/authenticator" target="_blank">Google Authenticator</a> en tu iPhone, Android o Blackberry.</b><br>También puedes usar otra aplicación si lo prefieres.</div>
                    </div>
                    <div class="step">
                        <div class="number">2</div><div class="text"><b>Configura tu cuenta de <?=$appname?> escaneando el siguiente código QR:</b></div>
                    </div>
                    <img src="<?=$qrcode?>" style="display: block; margin-left: auto; margin-right: auto;">
                    <div class="step" style="border-top: 1px solid #ebebeb;">
                        <div class="number">3</div><div class="text"><b>¿No puedes escanear el código QR? Introduce manualmente la siguiente llave secreta:</b><br><?=chunk_split($secret, 4, ' ');?></div>
                    </div>
                    <div class="step" style="margin-bottom: 5px;">
                        <div class="number">4</div><div class="text"><b>Introduce el código de verificación de seis dígitos:</b></div>
                    </div>
                    <div id="verify_container">
                        <div id="input">
                            <input type="text" id="verificationcode" maxlength="6"> <button id="verify" class="g-button g-button-submit">Verificar y guardar</button> <a id="cancel" href="2stepverification.php">Cancelar</a>
                        </div>
                        <div hidden id="waiting">
                            <svg class="spinner" width="24px" height="24px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                                <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                            </svg>
                            <span>Verificando...</span>
                        </div>
                        <div hidden id="error_1">
                            <span class="icon svg-ic_warning_24px"></span>
                            <span>El código de verificación debe tener 6 cifras</span>
                        </div>
                        <div hidden id="error_2">
                            <span class="icon svg-ic_error_24px"></span>
                            <span>El código de verificación no es correcto</span>
                        </div>
                        <div hidden id="error_3">
                            <span class="icon svg-ic_error_24px"></span>
                            <span id="mysqli_error"></span>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </body>
</html>