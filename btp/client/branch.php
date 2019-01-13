<?php

include_once('db.php');
session_start();

$host=$_SESSION['myAddr'];
$port=$_SESSION['myPort'];
set_time_limit(0);

$sock=socket_create(AF_INET,SOCK_STREAM,0);
socket_bind($sock,$host,$port);
socket_listen($sock,2);
echo("Listening for connections\n");

do{
	$accept=socket_accept($sock);
	echo "Accepted a connection\n";
	$msg=socket_read($accept,1024);

	$idx=2;
	$len=intval($msg[$idx]);
	$id=substr($msg,++$idx,$len);
	$idx+=$len;
	$len=intval($msg[$idx]);
	$balance=substr($msg,++$idx,$len);

	$sql="UPDATE customer SET balance = '$balance' WHERE customer.accn = $id";
	$result = mysqli_query($conn,$sql);


}while(true);

socket_close($accept);
socket_close($sock);
?>
