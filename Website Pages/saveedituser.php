<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php 
	session_start();
	$expirydate=$_POST["expirydate"];
	$cardholdername=$_POST["cardholdername"];
	$billingaddress=$_POST["billingaddress"];
	$creditcardnumber=$_POST["creditcardnumber"];
	$securitynumber=$_POST["securitynumber"];
	$first_name=$_POST["firstname"];
	$last_name=$_POST["lastname"];
	$passport=$_POST["passportnum"];
	$ex_passport=$_POST["expassportnum"];
	$birthdate=$_POST["bd"];
	$name=$first_name." ".$last_name;
	$homecountry=$_POST["citizenship"];
	$servername = "localhost";
    $username = "id181715_cpsc471";
    $password = "cpsc471";
	$wheelchair="0";
	$minor="0";
    $dbname = "id181715_dbcpsc471";
	$con=mysqli_connect($servername,$username,$password,$dbname);
	// Check connection
	if (mysqli_connect_errno($con))
	{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	if(isset($_POST["IsMinor"]) && 
		$_POST["IsMinor"] == 'Yes') 
	{
		echo("minor set");
		$minor="1";
	}
	if(isset($_POST["wheelchair"]) && 
		$_POST["wheelchair"] == 'Yes') 
	{
		$wheelchair="1";
	}
	$result = $con->query("SELECT * FROM PASSENGER AS P WHERE P.passport_num='$ex_passport'");
	if($result->num_rows == 0) {
		redirect('Account with this ex-passport number do not exist', '/edituserinfo.php');
	}else{
		$result1 = $con->query("SELECT * FROM PASSENGER AS P WHERE P.passport_num='$passport'");
		if($result1->num_rows == 0) {
			$sql="INSERT INTO PASSENGER VALUES ('". $passport."', '". $name."', '". $birthdate."','". $homecountry."','". $minor."','". $wheelchair."')";
			if (!mysqli_query($con,$sql)){
				die('Error: ' . mysqli_error($con));
			}else{$_SESSION['userName'] = $_POST["firstname"]." ".$_POST["lastname"];
				$_SESSION["userID"] = $_POST["passportnum"];
				$_SESSION["birthdate"]= $_POST["bd"];
				$_SESSION["citizenship"]=$_POST["citizenship"];
				
				$sql="UPDATE PASSENGER_HAS_BID SET passport_num='".$passport. "' WHERE passport_num='".$ex_passport. "'";
				if (!mysqli_query($con,$sql)){
					die('Error: ' . mysqli_error($con));
				}
				else{
				$sql="UPDATE CREDIT_CARD SET passport_number='".$passport. "' WHERE passport_number='".$ex_passport. "'";
				
				if (!mysqli_query($con,$sql)){
					die('Error: ' . mysqli_error($con));
				}else{
					$_SESSION["creditcardnumber"] = $_POST["creditcardnumber"];
					$_SESSION["cardholdername"] = $_POST["cardholdername"];
					$_SESSION["billingaddress"] = $_POST["billingaddress"];
					$_SESSION["securitynumber"] = $_POST["securitynumber"];
					$_SESSION["expirydate"] = $_POST["expirydate"];
					header("Location: user.php");
				}
				}
			
			} 
		}else {
			redirect('Account with the same passport number already exists.', '/edituserinfo.php');
		}
		
		
		$sql="DELETE FROM PASSENGER WHERE passport_num='$ex_passport'";
		if (!mysqli_query($con,$sql)){
			die('Error: ' . mysqli_error($con));
		}
		
	 
	}
	
	
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
	function set_session_message($message) {
		$message_id = sha1(microtime(true));
		$_SESSION[$message_id] = $message;
	 
		return $message_id;
	}
	

?>
<html>
    	<head>
		<meta charset="utf-8">
		<meta name="description" content="A website of booking for flights connecting Canada and ths US."> 
		<meta name="keywords" content="flight booking,Canadian airline,American airline">
		<meta name="author" content="Aaron Tong, Irene Mayor, Kevin Tran">
		<meta property="og:title" content="Canadian and American airline booking system-user registration">
		<meta property="og:url" content="http://causairlinebooking.000webhostapp.com/flight_result.html">
		<meta property="og:description" content="A website of booking for flights connecting Canada and the US. Customer can simply book a flight by creating an account  
		and providing their personal information such as passport number, name etc. After they search for their preferred flight, they can obtain the current bit price for that.">
		<title>Flight booking system</title>
                    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <!-- Latest compiled and minified CSS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>
<body>
Welcome <?php echo $name; ?><br>
Your passport number is: <?php echo $passport; ?><br>
Need Wheelchair: <?php echo $wheelchair; ?><br>
Birthdate:<?php echo $birthdate; ?><br>
is minor:<?php echo $minor; ?><br>
Citizenship:<?php echo $homecountry; ?><br>
</body>