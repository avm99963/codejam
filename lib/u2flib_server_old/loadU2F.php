<?php
require("src/u2flib_server/U2F.php");
require("vendor/autoload.php");
$scheme = isset($_SERVER['HTTPS']) ? "https://" : "http://";
$u2f = new u2flib_server\U2F($scheme . $_SERVER['HTTP_HOST']);
?>