
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php 
    session_start();
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
<div id="bg">
    <div>
    <input type="text" placeholder="Enter New Departure" >
    <input type="text" placeholder="Enter New Arrival">
    <button>Search</button>  
    </div>
    <table class="table" ng-show="event.liveCount > 0">
          <thead style="color:white">
                            <tr>
                                
                                <th class="col-md-2">Flight ID</th>
                                <th class="col-md-2">Departure City</th>
                                <th class="col-md-2">Arrival City</th>
                                <th class="col-md-2">Departure Time</th>
                                <th class="col-md-2">Arrival Time</th>
                                <th class="col-md-1"></th>
                            </tr>
                        </thead>
                        <tbody>
	  <?php
          $_SESSION["flightidforbid"]='r11';
    for ($x = 0; $x <= 10; $x++) {
      
    echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
      ."<td>r11</td>"
      ."<td>Tennesee</td>"
      ."<td>Los Angelas</td>"
      ."<td>Dec 10, 2016 01:00:11</td>"
      ."<td>Dec 10, 2016 05:00:11</td>"
      . "<td><a href='bid_for_flight.php'><button> Bid</button></a></td>"
            . "</tr>";
} 
  
//  $myServer = "136.159.7.84";
//$myUser = "CPSC471_Fall2016_G7";
//$myPass = 'a"-na9o$^`I&"nw';
//$myDB = "CPSC471_Fall2016_G7"; 
//$connectionInfo = array("Database"=>$myDB, "UID" => $myUser, "PWD" => $myPass);
//connection to the database
//$dbhandle = sqlsrv_connect($myServer,$connectionInfo)
//  or die("Couldn't connect to SQL Server on $myServer"); 

//select a database to work with
//$selected = sqlsrv_select_db($myDB, $dbhandle)
//  or die("Couldn't open database $myDB"); 
        ?>
                  </tbody>
       </table>
<!--    <a href="http://causairlinebooking.000webhostapp.com/bid_for_flight.html"><button style="color:blue;border-radius:10px;position: absolute;right:0;left:10%;top:25%;">Find Bid</button></a>
-->
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


#bg textarea{
color: background-color;
}



input[type="text"] {
height: 25px;
width: 200px;
}
</style>
</html>
