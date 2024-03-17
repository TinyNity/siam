<?php
include_once "php/utils.php";

error_log("Ouais yo c moi le lougout");
unset($_COOKIE["username"]);
alert("You are now logged out.");
redirect("./login.php");