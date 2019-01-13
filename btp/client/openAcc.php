<!DOCTYPE html>
<html>
<head>
    <style>
    </style>
    <title>Open Account</title>
    <link rel="stylesheet" href="page.css">
</head>
<body>
    <header>
        <h1>Open Account</h1>
        <p style="text-align:center;">RECORD- Open Account</p>
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


    <?php
    	include_once('db.php');
    	session_start();

    	if(isset($_POST['name']) && isset($_POST['aadhar']) && isset($_POST['pan']) && isset($_POST['sign']) && isset($_POST['bio']) && isset($_POST['address']) && isset($_POST['mobile']))
    	{
    	  $name = $_POST['name'];
          $aadhar = $_POST['aadhar'];
          $pan = $_POST['pan'];
	 	  $sign = $_POST['sign'];
	 	  $bio = $_POST['bio'];
	 	  $add = $_POST['address'];
	 	  $mob = $_POST['mobile'];
          $email = $_POST['email'];

          if(is_numeric($aadhar) && is_numeric($pan) && is_numeric($mob))
          {

	      $msg=sprintf('02%u%s%u%s%u%s%u%s%u%s%u%s%u%s%u%s',strlen($name),$name, strlen($sign),$sign, strlen($bio),$bio, strlen($add),$add, strlen($mob),$mob, strlen($email),$email, strlen($aadhar),$aadhar, strlen($pan),$pan);
	      
	      $host=$_SESSION['serverAddr'];
	      $port=$_SESSION['serverPort'];
	     // $host='localhost';
	     // $port=7778;
	      $sock=socket_create(AF_INET,SOCK_STREAM,0);
	      if(($ret=socket_connect($sock,$host,$port))<=0)echo "error connecting central server\n";
	      else
	      {
		      socket_write($sock,$msg,strlen($msg));
		      $reply=socket_read($sock,1024);
		      if($reply)echo " you got reply ";
		      if(intval($reply[0])==0)
		      {
			      $id=intval(substr($reply,1));


			      $sql = "INSERT INTO customer VALUES('$id','$name','$sign','$bio','$add','$mob','$email',0)";
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

          else {
            echo "invalid data : Try again\n";
            }
        }
    ?>


            <form method="POST">
                <h3>Fill the Customer Profile Details</h3><br>
                Customer's Name      	: <input type="text" name="name"/><br/><br/>
		KYC Details<br/>
                Aadhar Number  		: <input type="text" name="aadhar"/>
                PAN	 		: <input type="text" name="pan"/><br/><br/>
		Customer Unique Identity<br/>
		Signature 		: <input type="password" name="sign"/>
		Biometric	 	: <input type="password" name="bio"/><br/><br/>
		Customer Contact Details<br/>
		Address 		: <input type="text" name="address"/>
		Mobile/Phone 		: <input type="text" name="mobile"/>
		E-Mail (optional) 	: <input type="text" name="email"/><br/><br/>
                <input type="submit" value=" Enter "/>
            </form>





</body>
</html>
