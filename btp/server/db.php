<?php

session_start();
$db_host = "localhost";
$db_username = "mysql_username";
$db_pass = "mysql_passwd";
$db_name = "CentralServer";

$conn = new mysqli($db_host,$db_username,$db_pass, $db_name) or die ("Couldn't connect to MySQL");
mysqli_select_db($conn,$db_name) or die ("No database"); // no need as already included in previous function

$_SESSION['serverAddr'] = 'localhost';
$_SESSION['serverPort'] = 7778;

$_SESSION['b1_addr'] = 'localhost';
$_SESSION['b1_port'] = 7777;
$_SESSION['b1_ifsc'] =  1000;

$_SESSION['b2_addr'] = 'localhost';
$_SESSION['b2_port'] = 7779;
$_SESSION['b2_ifsc'] =  2000;

$_SESSION['b3_addr'] = 'localhost';
$_SESSION['b3_port'] = 7780;
$_SESSION['b3_ifsc'] =  3000;

?>
