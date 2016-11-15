<?php 
    session_start();
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
     <h2>This website is for user to book flights between Canada and the US.</br> Wanna get the best price for your flight? Register here and take a bid!</h2>

     <div class="form-group panel panel-default">
         <label for="sel1">Select Departure Airport:</label>
        <select class="form-control" id="sel1">
            <?php
        
        ?>
          <option>Tenneesee</option>
          <option>Washington</option>
          <option>New York</option>
          <option>Ottawa</option>
        </select>
     <label for="sel1">Select Arrival Airport:</label>
        <select class="form-control" id="sel1">
            <?php
        
        
        ?>
          <option>Calgary</option>
          <option>Vancouver</option>
          <option>New York</option>
          <option>Ottawa</option>
        </select>
     </div>
     <div class='panel-default panel'>
        <h1>San Diego Airport – Concourse F – Terminal 2 Expansion. N.d. San Diego Airport – Concourse F – Terminal 2 Expansion-Mcmahon Steel. Web. 11 Nov. 2016. <http://mcmahonsteel.com/project_item/san-diego-airport-concourse-f-terminal-2-expansion/>.</h1>
        <label>name</label>
        <h3><input type="text" placeholder="Enter Full Name"></h3>
        <h4>passport number</h4>
        <h5><input type="text" placeholder="e.g abcdef1234567"></h5>
        <a href="http://causairlinebooking.000webhostapp.com/loggedin.html"><button class='btn btn-default' style=" color:blue;border-radius:10px;position: absolute;right:0;left:10%;top:25%;">login</button></a>
        <a href="http://causairlinebooking.000webhostapp.com/register.html"><button class='btn btn-default' style="color:blue;border-radius:10px;position: absolute;right:0;left:10%;top:30%;">register</button></a>
     </div>
   
</div>
</body>
<style>
#bg{
background-image: url('http://mcmahonsteel.com/wp/wp-content/uploads/2013/01/IMG_0110.jpg');
width: 100%;
height: 100vh;
}
#bg h1{
position: absolute;
right:0;
left:60%;
top:90%;
text:italic;
text-align:center;
color: white;
font-family: arial;
font-size: 20;
}
#bg h2{
position: absolute;
right:0;
left:10%;
top:7%;
color:yellow;
}
#bg h3{
position: absolute;
right:0;
left:10%;
top:10%;
}
#bg h4{
position: absolute;
right:0;
left:10%;
top:15%;
color:yellow;
font-size: 24;
}
#bg h5{
position: absolute;
right:0;
left:10%;
top:19%;
}
#bg d{
position: absolute;
right:0;
left:60%;
top:10%;
text:italic;
text-align:right;
color: yellow;
font-family: arial;
font-size: 20;
}

#bg textarea{
color: background-color;
}




input[type="text"] {
height: 25px;
width: 200px;
}
</style>
</html>
