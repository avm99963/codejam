<?php
require_once("core.php");
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "wrongcode")
  $msg = '<p class="alert-warning">El código que has introducido no es correcto</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "passworddoesntmatch")
  $msg = '<p class="alert-danger">La contraseña que has introducido no es correcta</p>';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require ("head.php"); ?>
        <title>Segundo factor - <?php echo $appname; ?></title>
        <style>
        .small_view {
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
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
    </head>
    <body>
        <div class="content">
            <?php include "nav.php"; ?>
            <article>
                <?php anuncio(); ?>
                <div class="text small_view">
                    <?php
                    if (!isset($_SESSION["prov_id"])) {
                      header("Location: index.php");
                      exit;
                    }
                    ?>
                    <h1>Verificación en 2 pasos</h1>
                    <?php
                    echo $msg;
                    $secondfactor = twostepverification($_SESSION["prov_id"]);
                    if (!$secondfactor) {
                        header("Location: index.php");
                        exit;
                    }
                    if (isset($_POST["verificationcode"])) {
                        
                    } else {
                        if ($secondfactor == 1 || (isset($_GET["forcecode"]) && $_GET["forcecode"] == "true")) {
                    ?>
                    <script src="js/secondfactor_code.js"></script>
                    <style>
                    #code_container {
                        margin: 20px;
                    }
                    .deliverymethodcontainer {
                        margin-bottom: 20px;
                        background-image: url(img/authenticator-ios-phone-icon_1X.png);
                        background-size: 72px 72px;
                        background-repeat: no-repeat;
                        background-position: left center;
                        min-height: 72px;
                        padding-left: 82px;
                        text-align: left;
                    }
                    #verificationcode {
                        width: 100%;
                        font-size: 14px;
                    }
                    #verify {
                        font-size: 13px!important;
                        width: 100%;
                        height: 34px;
                        outline: 0;
                    }
                    #verify_container div {
                        margin-top: 8px;
                    }
                    </style>
                    <div id="code_container">
                        <div class="deliverymethodcontainer">
                            <p><b>Introduce el código de verificación generado por tu aplicación para móviles.</b></p>
                        </div>
                        <div id="verify_container">
                            <div id="input">
                                <div style="margin-bottom: 10px;"><input type="text" id="verificationcode" name="verificationcode" placeholder="Introduce el código"></div>
                                <div><button id="verify" class="g-button g-button-submit">Verificar</button></div>
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
                    <?php
                        } elseif ($secondfactor == 2) {
                            require("lib/u2flib_server/loadU2F.php");
                            $query = mysqli_query($con, "SELECT keyHandle FROM securitykeys WHERE user_id = '".$_SESSION['prov_id']."'") or die("<div class='alert-danger'>".mysqli_error($con)."</div>");
                            $row = array();
                            if (mysqli_num_rows($query)) {
                                while ($row[] = mysqli_fetch_assoc($query)) {}
                                array_pop($row);
                                foreach ($row as $key => $value) {
                                    /*$row[$key] = new stdClass();
                                    $row[$key]->keyHandle = $value["keyHandle"];*/
                                    $row[$key] = json_decode(json_encode($value));
                                }
                            } else {
                                die('<iframe width="400" height="270" src="//www.youtube-nocookie.com/embed/rp8hvyjZWHs?rel=0" frameborder="0" allowfullscreen></iframe>');
                            }

                            try {
                                $reqs = json_encode($u2f->getAuthenticateData($row));
                                echo "<script>var req = $reqs;</script>";
                            } catch( Exception $e ) {
                                echo "Error: ".$e->getMessage();
                            }
                    ?>
                    <script src="chrome-extension://pfboblefjcgdjicmnffhdgionmgcdmne/u2f-api.js"></script>
                    <script src="js/secondfactor_gnubby.js"></script>
                    <style>
                    #waiting_usb span {
                        vertical-align: middle;
                    }

                    #auth_container div {
                        margin-top: 20px;
                    }
                    </style>
                    <div id="challenge">
                        <img src="img/Challenge_2SV-Gnubby_graphic.png" style="height: 162px;">
                    </div>
                    <div id="install_extension" hidden>
                        <p>Antes deberás instalar la extensión <a href="https://chrome.google.com/webstore/detail/fido-u2f-universal-2nd-fa/pfboblefjcgdjicmnffhdgionmgcdmne" target="_blank">FIDO U2F</a> de la Chrome Web Store.</p>
                    </div>
                    <div id="auth_container">
                        <div hidden id="waiting_usb">
                            <svg class="spinner" width="24px" height="24px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                                <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                            </svg>
                            <span>Introduce (y pulsa) tu llave de seguridad.</span>
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
                            <span>Se ha agotado el tiempo de espera. Por favor, <a href="secondfactor.php">intenta de nuevo</a>. <span style="color: gray;font-size: 12px;">(TIMEOUT)</span></span>
                        </div>
                    </div>
                    <p><a style="font-size: 12px;" href="secondfactor.php?forcecode=true">También puedes introducir un código</a></p>
                    <?php
                        } else {
                    ?>
                    <iframe width="400" height="270" src="//www.youtube-nocookie.com/embed/rp8hvyjZWHs?rel=0" frameborder="0" allowfullscreen></iframe>
                    <?php
                        }
                    }
                    ?>
                </div>
            </article>
        </div>
    </body>
</html>