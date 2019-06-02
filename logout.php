<?php

session_start();
session_destroy();
unset($_SESSION['email']);
unset($_SESSION['name']);
unset($_SESSION['surname']);
header('Location: login.php');
exit;

?>