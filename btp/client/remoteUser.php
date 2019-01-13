<!DOCTYPE html>
<html>
<head>
    <style>
    </style>
    <title>Remote User</title>
    <link rel="stylesheet" href="page.css">
</head>
<body>
    <header>
        <h1>Remote User</h1>
        <p style="text-align:center;">RECORD- Remote User</p>
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
                <h3>Fill Details and Transact Remotely</h3>
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
			else $money = 0;

			if($mode!="balance" && ((empty($_POST["w_money"]) && empty($_POST["d_money"])) || !is_numeric($money)))
			{
				echo "Fill Proper Amount";
			}
			else
			{

					echo "<h4 align='center' >Hello Remote User</h4>";
					$msg=sprintf('11%u%s%u%s%u%s%u%s%u%s',strlen($id),$id, strlen($sign),$sign, strlen($bio),$bio, strlen($mode),$mode, strlen($money),$money);

					$host=$_SESSION['serverAddr'];
	   				$port=$_SESSION['serverPort'];
	    			$sock=socket_create(AF_INET,SOCK_STREAM,0);
	    			if(($ret=socket_connect($sock,$host,$port))<=0)echo "error connecting central server\n";
	    			else
	    			{
		    			socket_write($sock,$msg,strlen($msg));
		    			$reply=socket_read($sock,1024);
		      			if($reply)echo "<h4 align='center'>You got reply</h4>";

		    			
			    		if(intval($reply[0])==1 and intval($reply[1])==0)
			    		{
			    			echo "Invalid biometric / signature\n";
			    		}
			    		else if(intval($reply[0])==0 and intval($reply[1])==1)
			    		{
			    			echo "Insufficient Balance\n";
			    		}
			    		else if(intval($reply[0])==0 and intval($reply[1])==0)
			    		{
			    			echo "Account Doesn't exist";
			    		}
			    		else if(intval($reply[0])==1 and intval($reply[1])==1)
		    			{
			    			$balance=intval(substr($reply,2));
			    			if($mode!='balance')echo "<h4 align='center'>Transaction Successfully completed</h4>";
			    			echo "<h4 align='center'> Account Balance : ".$balance."</h4>";			    			
			    		}
			    	}



			    /*  $sql = "INSERT INTO customer VALUES('$id','$name','$sign','$bio','$add','$mob','$email',0)";
			      $result = $conn->query($sql);
			      echo "Hey you have id = '$id' allocated centrally. Please note it down for any transactions\n";
				  if($result)
				 	echo "Account Successfully Created!\n";
				  else
					echo "OOPS : Problem in Branch Database.\n";

		    		}
		    		else
						echo "Central Server error\n";
	      }
          }


					if($mode=="withdraw")
					{
							$sql = "INSERT INTO transaction values ('$id','$money',1,2,'$sign','$bio',0)";
							$result = $conn->query($sql);
			      				if($result)
							{
								echo "<h4 align='center'>Withdraw Transaction Initiated...</h4>";
								//
								//
							}
							else echo "<h4 align='center'>OOPS :[w] Problem in Branch Database.</h4>";
					}
					else if($mode=="deposit")
					{
							$sql = "INSERT INTO transaction values ('$id','$money',1,1,'$sign','$bio',0)";
							$result = $conn->query($sql);
			      				if($result)
							{
								echo "<h4 align='center'>Deposit Transaction Initiated...</h4>";
								//
								//
							}
							else echo "<h4 align='center'>OOPS :[d] Problem in Branch Database.</h4>";
					}
					else
					{
							$sql = "INSERT INTO transaction values ('$id',0,1,0,'$sign','$bio',0)";
							$result = $conn->query($sql);
			      				if($result)
							{
								echo "<h4 align='center'>Requesting Balance...</h4>";
								//
								//
							}
							else echo "<h4 align='center'>OOPS :[b] Problem in Branch Database.</h4>";
					}*/


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

