<!DOCTYPE html>
<html>
<head>
    <style>
    </style>
    <title>Local User</title>
    <link rel="stylesheet" href="page.css">
</head>
<body>
    <header>
        <h1>Local User</h1>
        <p style="text-align:center;">RECORD- Local User</p>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="openAcc.php">Open Account</a></li>
		<li><a href="localUser.php">Local User</a></li>
		<li><a href="remoteUser.php">Remote User</a></li><br>
            </ul>
        </nav>
    </header>
    <hr>



            <form action="" method="POST">
                <h3>Fill Details and Transact Locally</h3>
                Customer's ID  		: <input type="text" name="id"/><br/><br/>
		Customer Unique Identity [Fill atleast one of Signature and Biometric]<br/>
		Signature 		: <input type="password" name="sign"/>
		Biometric	 	: <input type="password" name="bio"/><br/><br/><br/>
               

Select Mode:<br/>
  <input type="radio" name="mode" value="balance">See Balance<br/><br/>
  <input type="radio" name="mode" value="deposit">Deposit Money<br/>
  Deposit Amount :<input type="text" name="d_money"/><br/><br/>
  <input type="radio" name="mode" value="withdraw">Withdraw Money<br/>
  Withdraw Amount :<input type="text" name="w_money"/><br/>
  <br>
  <input type="submit" name="submit" value="Submit"> 

            </form>

<?php
	include_once('db.php');
	include('addr.php');
	session_start();

	if($_POST)
	{
		
		if( empty($_POST["id"]) || ( empty($_POST["sign"]) && empty($_POST["bio"]) ) ){
			echo "Fill id and sign/bio";
		}
		else if (empty($_POST["mode"])) {
    			echo "Select Mode";
  		}
		else {
			$id=$_POST["id"];
			$sign=$_POST["sign"];
			$bio=$_POST["bio"];

   			$mode = test_input($_POST["mode"]);

			if($mode=="withdraw")$money=$_POST["w_money"];
			else if($mode=="deposit")$money=$_POST["d_money"];

			if($mode!="balance" && ((empty($_POST["w_money"]) && empty($_POST["d_money"])) || !is_numeric($money)))
			{
				echo "Fill Proper Amount";
			}
			else
			{
				$proceed=0;

				if($sign && $bio)
				  {
					$sql = "SELECT name, balance FROM customer where accn='$id' and sign='$sign' and bio='$bio'";
					$result = $conn->query($sql);
					$proceed=1;
				  }
				  else if($sign)
				  {
					$sql = "SELECT name, balance FROM customer where accn='$id' and sign='$sign'";
					$result = $conn->query($sql);
					$proceed=1;
				  }
				  else if($bio)
				  {
					$sql = "SELECT name, balance FROM customer where accn='$id' and bio='$bio'";
					$result = $conn->query($sql);
					$proceed=1;
				  }
				  else
				  {
					echo "Invalid User Details\n";
				  }

				if($proceed)
				{
					$row = mysqli_fetch_array($result);
					$name=$row["name"];
					echo "<h4 align='center' >Hello $name</h4>";

					if($mode=="withdraw")
					{
						if(intval($row['balance'])>=intval($money))
						{
							$money=$row['balance']-$money;
							$sql = "UPDATE customer SET balance='$money' where accn='$id' ";
			      				$result1 = $conn->query($sql);
							$sql = "INSERT INTO transaction(accn,amount,type,mode,sign,bio,status,sent) values ('$id','$money',0,2,'$sign','$bio',1,0)";
							$result2 = $conn->query($sql);

							if($result2)$tid = mysqli_insert_id($conn);

			    			if($result1 && $result2)echo "<h4 align='center'>Withdraw Transaction Successful. Your Balance: '$money'</h4>";
							else echo "<h4 align='center'>OOPS : Problem in Branch Database.</h4>";
						}	
						else echo "<h4 align='center'>Transaction Failed. Insufficient Balance: $row[balance]</h4>";
					}
					else if($mode=="deposit")
					{
							$money=$row['balance']+$money;
							$sql = "UPDATE customer SET balance='$money' where accn='$id' ";
			      				$result1 = $conn->query($sql);
							$sql = "INSERT INTO transaction(accn,amount,type,mode,sign,bio,status,sent) values ('$id','$money',0,1,'$sign','$bio',1,0)";
							$result2 = $conn->query($sql);

							if($result2)$tid = mysqli_insert_id($conn);

			    			if($result1 && $result2)echo "<h4 align='center'>Deposit Transaction Successful. Your Balance: '$money'</h4>";
							else echo "<h4 align='center'>OOPS : Problem in Branch Database.</h4>";
					}
					else
					{
						$money=$row["balance"];
						echo "<h4 align='center'>Your Balance: $money</h4>";
					}

					if($mode=="withdraw" or $mode="deposit")
					{
						$msg=sprintf('10%u%s%u%s',strlen($id),$id, strlen($money),$money);

						$host=$_SESSION['serverAddr'];
	    				$port=$_SESSION['serverPort'];

	    				$sock=socket_create(AF_INET,SOCK_STREAM,0);
	    				if(($ret=socket_connect($sock,$host,$port))<=0)echo "error connecting central server\n";
	    				else
	    				{
		    				socket_write($sock,$msg,strlen($msg));
		    				$reply=socket_read($sock,1024);

		    				if($msg[0]=='1')
		    				{
		    					$sql = "UPDATE transaction SET sent=1 where id='$tid' ";
			      				$result1 = $conn->query($sql);
		    				}
						}
					}


				}
			}
  		}
	}


function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>


</body>
</html>

