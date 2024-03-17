<?php
include_once "php/utils.php";
unset($_COOKIE["username"]);
redirect("./login.php");