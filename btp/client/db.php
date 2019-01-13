<?php
session_start();
$db_host = "localhost";
$db_username = "mysql_username";
$db_pass = "mysql_passwd";
$db_name = "BranchDB1";

$conn = new mysqli($db_host,$db_username,$db_pass, $db_name) or die ("Couldn't connect to MySQL");
mysqli_select_db($conn,$db_name) or die ("No database"); // no need as already included in previous function

$_SESSION['serverAddr'] = '10.5.16.239';
$_SESSION['serverPort'] = 7778;

$_SESSION['myAddr'] = '10.5.16.191';
$_SESSION['myPort'] = 7779;

//echo"Successful connection";
?>
