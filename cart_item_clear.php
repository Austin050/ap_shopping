<?php 
session_start();
$id = $_GET['pid'];
unset($_SESSION['cart']['id'.$id]);

header ("Location: cart.php")
?>