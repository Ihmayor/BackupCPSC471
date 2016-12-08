<?php 
    session_start();
    //Echo all error messages received from other pages
    if (isset($_GET['message']) && isset($_SESSION[$_GET['message']])) {
		echo $_SESSION[$_GET['message']];
		unset($_SESSION[$_GET['message']]);
     }
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
	<head>
		<meta charset="utf-8">
		<meta name="description" content="A website of booking for flights connecting Canada and ths US."> 
		<meta name="keywords" content="flight booking,Canadian airline,American airline">
		<meta name="author" content="Aaron Tong, Irene Mayor, Kevin Tran">
		<meta property="og:title" content="Canadian and American airline booking system">
		<meta property="og:url" content="http://causairlinebooking.000webhostapp.com">
		<meta property="og:description" content="A website of booking for flights connecting Canada and the US. Customer can simply book a flight by creating an account  
		and providing their personal information such as passport number, name etc. After they search for their preferred flight, they can obtain the current bit price for that.">
		<title>Flight booking system</title>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
                <!-- Latest compiled and minified CSS -->
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>

                <!-- Latest compiled and minified JavaScript -->
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

</head>
<body>
   
<div id="bg">
    <!--Set up log in panel-->
       <div class="col-md-6 col-md-offset-3  form-group panel panel-default">
        <h1>Book and Bid for Flights throughout North America!</h1>
         <div class='panel-heading'>
            Sign up today and start bidding!
        </div>
        <form class='panel body' method='post' action='verify.php'>
            <div class='col-md-12'>
                <label class='col-md-4'>First Name</label>
                <input class='col-md-5' type='text' name='firstname' pattern="[A-Za-z]{,50}"  value='' placeholder='ie: Aaron' title="within 50 alphaletters">
            </div>
			 <div class='col-md-12'>
                <label class='col-md-4'>Last Name</label>
                <input class='col-md-5' type='text' name='lastname' pattern="[A-Za-z]{,50}" value='' placeholder='ie: Tong' title="within 50 alphaletters">
            </div>
             <div class='col-md-12'>
                <label class='col-md-4'>9 digit Passport Number</label>
                <input class='col-md-5' type='digit' pattern=".{9,9}"  name='passportnum' value=''   placeholder='ie:91281238' required title="9 digit">
            </div>
            <div class='col-md-12'></div>
            <span class='col-md-offset-5'>
                <input type='submit' value='Login'class='btn btn-default'></input>
            </span>
        </form>
		
           <!--Set up register panel-->
     </div>
	 <div class="col-md-6 col-md-offset-3  form-group panel panel-default">
		<div class='panel-heading'>
            Not a member? Register here first!
        </div>
		<form class='panel body' method='post' action='register.php'>
		<span class='col-md-offset-5'>
                <a href=''><button class='btn btn-default'>Sign Up </button></a>
            </span>
		</form>
</div>
</body>
<style>
#bg{
background-image: url('http://mcmahonsteel.com/wp/wp-content/uploads/2013/01/IMG_0110.jpg');
width: 100%;
height: 100vh;
}

</style>
</html>