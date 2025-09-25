<?php

session_start();
session_destroy();

// navigate to 
header("Location:login.php");
?>