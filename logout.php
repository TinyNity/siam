<?php
include_once "utils.php";

unset($_COOKIE["user"]);
alert("You are now logged out.");
redirect("./login.php");