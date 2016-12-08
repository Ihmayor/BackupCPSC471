<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
                session_start();
		//Fetch all necessary information from login input on past page
                $first_name=$_POST["firstname"];
		$last_name=$_POST["lastname"];
		$passport=$_POST["passportnum"];
		$servername = "localhost";
		$username = "id181715_cpsc471";
		$password = "cpsc471";
		$dbname = "id181715_dbcpsc471";
		//Append names together as the there's only one column for a user's name
                $name=$first_name." ".$last_name;
                
                //Start connection the database
		$con=mysqli_connect($servername,$username,$password,$dbname);
		// Check connection
		if (mysqli_connect_errno($con))
		{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
                //Query and check the database for any matching passengers
		$result = $con->query("SELECT * FROM PASSENGER AS p WHERE p.name='$name' AND p.passport_num='$passport'");
		if($result->num_rows == 0) {
                        //Redirect user back to front page with error message.
			redirect('name or password number invalid, try again', '/index.php');
		} else {
                        //Store fetched user data into session variables 
                        $row = $result->fetch_object();
                        $_SESSION["userName"] = $_POST["firstname"]." ".$_POST["lastname"];
	                $_SESSION["userID"] = $_POST["passportnum"];
					$_SESSION["birthdate"]=$row->dateOfBirth;
				$_SESSION["citizenship"]=$row->citizenship;
                }
                //Fetch information of user's credit card
		 $result = $con->query("SELECT * FROM CREDIT_CARD AS c WHERE c.passport_number='$passport'");
		 if($result->num_rows == 0) {
                        //Redirect user back to front page with error message
			redirect('cannot find credit card info', '/index.php');
		} else {
                                //Store Fetched creditcard information into session variables
                                $row = $result->fetch_object();
                                $_SESSION["creditcardnumber"]=$row->creditcard_number;
				$_SESSION["cardholdername"]=$row->cardholdername ;
				$_SESSION["billingaddress"]=$row->billing_address;
				$_SESSION["securitynumber"]=$row->security_num;
				$_SESSION["expirydate"]=$row->expiry_date;
				header("Location: user.php");
         }
		 
		//Redirect Helper Function for handling the redirection of pages and error messages to be sent back 
		function redirect($message, $page=FALSE) {
                    $my_get = array();
                    $_GET['message'] = set_session_message($message);
                    foreach ($_GET as $n=>$v) {
			$my_get[] = "{$n}={$v}";
                    }
                    if (count($my_get) > 0) {
			$my_get = '?'.implode('&',$my_get);
                    } else {
			$my_get = '';
                    }
	 
                    if (is_string($page)) {
                            $location = $page;
                    } else {
                            $location = $_SERVER['SCRIPT_NAME'];
                    }

                    $http = (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS'])!='on')?'http':'https';

                    header("Location: {$http}://{$_SERVER['HTTP_HOST']}{$location}{$my_get}");
                    exit;
		}
                
                //Helper set session function
		function set_session_message($message) {
			$message_id = sha1(microtime(true));
			$_SESSION[$message_id] = $message;
		 
			return $message_id;
		}
                $con->close();
        ?>
    </body>
</html>