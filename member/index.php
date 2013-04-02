<?php
session_start();
$_SESSION['right_enter'] = 1;
header("Location: ../index.php?s=Member/Login");
