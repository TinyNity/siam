<?php

//! Error handling 

// ini_set('display_errors', 0);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// ini_set('log_errors', 1);
ini_set('error_log', './error.log');

// *********************************
// *                               *
// *   Utilitarian functions used  *
// *   throughout the project      *
// *                               *
// *********************************



//? Constant value for the cookies expiry time
define('EXPIRYDATE', time() + (3600 * 24 * 60));
define('PARENTPATH',dirname(__DIR__));

//? Use a header to redirect the client to a url given in parameter with an optional status code
function redirect(string $url, bool $die = true, int $statusCode = 303) { // Redirect HTTP code
    header('Location: ' . $url, true, $statusCode);
    if ($die) die();
}

//? Used to write a message to the javascript console
function js_debug($data) {
    $output = $data;
    if (is_array($output)) $output = implode(',', $output);
    echo "<script>console.log('[!] Debug : " . $output . "' );</script>";
}

//? Generate a simple alert with the message given in parameters
function alert($message) {
    echo '<script type="application/javascript">';
    echo 'alert("' . $message . '");';
    echo '</script>';
}

//? Append javascript to the page

function js($code) {
    echo '<script type="application/javascript">';
    echo $code;
    echo '</script>';
}

