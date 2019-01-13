<?php

include_once('db.php');
session_start();

$host=$_SESSION['serverAddr'];
$port=$_SESSION['serverPort'];
set_time_limit(0);

$sock=socket_create(AF_INET,SOCK_STREAM,0);
socket_bind($sock,$host,$port);
socket_listen($sock,3);
echo("Listening for connections\n");

do{
	$accept=socket_accept($sock);
	echo "Accepted a connection\n";
	$msg=socket_read($accept,1024);

	//echo 'helllo'.$msg[0]."bye".$msg[1];
	if($msg[0]=='0')
	{
		$client=intval($msg[1]);
		$ifsc=1000*$client;
		$idx=2;
		$len=intval($msg[$idx]);
		$name=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$sign=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$bio=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$add=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$mob=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$email=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$aadhar=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$pan=substr($msg,++$idx,$len);

		$sql = "INSERT INTO customer(name,sign,bio,addr,mob,email,balance,aadhar,pan,ifsc) VALUES('$name','$sign','$bio','$add',$mob,'$email',0,$aadhar,$pan,$ifsc)";
		$result = mysqli_query($conn,$sql);
		if($result)$id = mysqli_insert_id($conn);
		else {
			$id=-1;
			echo "Couldn't insert\n";
		}

		$msg=sprintf('0%u',$id);
		socket_write($accept,$msg,strlen($msg));
	}
	else if($msg[0]=='1' and $msg[1]=='0')
	{
		$client=intval($msg[1]);
		$ifsc=1000*$client;
		
		$idx=2;
		$len=intval($msg[$idx]);
		$id=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$amount=substr($msg,++$idx,$len);

		$sql="UPDATE customer SET balance ='$amount' WHERE customer.accn = $id";
		$result = mysqli_query($conn,$sql);
		if($result){$status = 1;}

		$msg=sprintf('11');
		socket_write($accept,$msg,strlen($msg));


	}
	else if($msg[0]=='1' and $msg[1]=='1')
	{

		$client=intval($msg[1]);
		$ifsc=1000*$client;
		
		$idx=2;
		$len=intval($msg[$idx]);
		$id=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$sign=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$bio=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$mode=substr($msg,++$idx,$len);
		$idx+=$len;
		$len=intval($msg[$idx]);
		$amount=substr($msg,++$idx,$len);

		$proceed=0;
		$present = 0;
		$balance =0;

		if($id)
		{
			$sql="SELECT ifsc from customer where accn=$id";
				$result = mysqli_query($conn,$sql);

				$row = mysqli_fetch_object($result);
				$count = mysqli_num_rows($result);
				if($count ==1 ){$present=1;}

				$ifsc=$row->ifsc;

		}
		if($present ==1 )
		{
			if($sign && $bio)
			  {
				$sql="SELECT ifsc from customer where accn='$id' and sign='$sign' and bio='$bio' ";
				$result = mysqli_query($conn,$sql);
				$count = mysqli_num_rows($result);
				if($count ==1 ){$proceed=1;}
			  }
			  else if($sign)
			  {
				$sql="SELECT ifsc from customer where accn='$id' and sign='$sign' ";
				$result = mysqli_query($conn,$sql);
				$count = mysqli_num_rows($result);
				if($count ==1 ){$proceed=1;}
			 }
			  else if($bio)
			  {
				$sql="SELECT ifsc from customer where accn='$id' and bio='$bio' ";
				$result = mysqli_query($conn,$sql);
				$count = mysqli_num_rows($result);
				if($count ==1 ){$proceed=1;}
			  }
			  else
			  {
				echo "Invalid User Details\n";
			  }
		}
		else
		{
			$msg=sprintf('00%u',$balance);
		    socket_write($accept,$msg,strlen($msg));
		}


		if($proceed)
		{
				if($mode == "deposit")
		      	{
		      		$sql="UPDATE customer SET balance = balance + '$amount' WHERE customer.accn = $id";
		      		$result = mysqli_query($conn,$sql);
		      		if($result){$status = 1;}

		      		$sql="SELECT balance from customer WHERE customer.accn = $id";
		      		$result = mysqli_query($conn,$sql);
		      		$obj = mysqli_fetch_object($result);
		      		$balance = $obj->balance;

		      	}
		      	else if($mode == "withdraw")
		      	{
		      		$sql="UPDATE customer SET balance = balance - '$amount' WHERE customer.accn = $id";
		      		$result = mysqli_query($conn,$sql);
		      		if($result){$status = 1;}

		      		$sql="SELECT balance from customer WHERE customer.accn = $id";
		      		$result = mysqli_query($conn,$sql);
		      		$obj = mysqli_fetch_object($result);
		      		$balance = $obj->balance;
		      	}
		      	else
		      	{
		      		$sql="SELECT balance from customer WHERE customer.accn = $id";
		      		$result = mysqli_query($conn,$sql);

		      		if($result){$status = 1;}
		      		$obj = mysqli_fetch_object($result);
		      		$balance = $obj->balance;
		      		//echo "balance is:".$balance;
		      	}
		      	$msg=sprintf('11%u',$balance);
		      	socket_write($accept,$msg,strlen($msg));

			if($ifsc and ($mode == "withdraw" or $mode == "deposit"))
			{
		      $new_sock=socket_create(AF_INET,SOCK_STREAM,0);
		      if($ifsc==$_SESSION['b1_ifsc'] && ($ret=socket_connect($new_sock,$_SESSION['b1_addr'],$_SESSION['b1_port']))<0)echo "error connecting branch 1 server\n";
		      else if($ifsc==$_SESSION['b2_ifsc'] && ($ret=socket_connect($new_sock,$_SESSION['b2_addr'],$_SESSION['b2_port']))<0)echo "error connecting branch 2 server\n";
		      else if($ifsc==$_SESSION['b3_ifsc'] && ($ret=socket_connect($new_sock,$_SESSION['b3_addr'],$_SESSION['b3_port']))<0)echo "error connecting branch 3 server\n";
		      else
		      {	      	
		      	$msg=sprintf('11%u%s%u%s',strlen($id),$id,  strlen($balance),$balance);
		      	socket_write($new_sock,$msg,strlen($msg));
		      	//$reply=socket_read($new_sock,1024);
		      }
			 }
		}
		else if ($present==1 and $proceed==0)
		{
			$msg=sprintf('10%u',$balance);
		    socket_write($accept,$msg,strlen($msg));
		}
	}
}while(true);

socket_close($accept);
socket_close($sock);
?>
