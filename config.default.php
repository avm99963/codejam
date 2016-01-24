<?php
/**
 * Configuration
 * 

    /////  //////  ////  //  //////  //  //////
   //     //  //  // // //  //          //
  //     //  //  //  ////  //////  //  //  //
 /////  //////  //   ///  //      //  //////

 */

// Welcome to the configuration file! In comments you have the explanation of what each variable does.
// Make a copy of this file as config.php, and fill in the information below :-)

// Define the website name:
$appname = "Code Jam";

// Define the language: (currently only "en" and "es" are supported)
$language = "en";

// Define the MySQL DataBase settings:
$host_db = ''; // DB Host (default: localhost)
$usuario_db = ''; // DB User
$clave_db = ''; // DB Password
$nombre_db = ''; // DB name

// Length of the name of the images uploaded (default is 8):
$filenamelength = 8;

// If you want an alert at the beggining of each page, define it here:
$anuncio = '';

// Configuration array:
$conf = array();

// Domains permitted for registration:
$conf["email_domain"] = "";

// Configuration array for Recaptcha:
// (You can get your API keys at https://www.google.com/recaptcha/admin)
$conf["recaptcha"] = array();
$conf["recaptcha"]["secretkey"] = "";
$conf["recaptcha"]["sitekey"] = "";
?>