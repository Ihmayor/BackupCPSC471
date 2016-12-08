<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    session_start();
    if (isset($_GET['message']) && isset($_SESSION[$_GET['message']])) {
		echo $_SESSION[$_GET['message']];
		unset($_SESSION[$_GET['message']]);
     }
    //Store User Information into Variables for display
    $name= $_SESSION['userName'];
    $passport= $_SESSION["userID"];
    $birthdate=$_SESSION["birthdate"];
    $citizenship=$_SESSION["citizenship"];
    
    //Fetch Credit Card Information
    $creditcardnumber = $_SESSION["creditcardnumber"];
    $cardholdername = $_SESSION["cardholdername"];
    $billingaddress = $_SESSION["billingaddress"];
    $securitynumber = $_SESSION["securitynumber"];
    $expirydate = $_SESSION["expirydate"];

				
    //Check, under the case of a return to previous page button is pressed
    //of any past airports previously used in the flight search
    $arr_airport =  $_POST['ArrAirport'];
    $dep_airport =  $_POST['DepAirport'];
    $dep_date = $_POST['DepDate'];
    
    //Connecto the local mysql database on the 000webhost.com website
    $servername = "localhost";
    $username = "id181715_cpsc471";
    $password = "cpsc471";
    $dbname = "id181715_dbcpsc471";
    // Create connection
    $fail = true;
    $conn = new mysqli($servername, $username, $password,$dbname);
    $_SESSION["db"] = $conn;
    
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
                $airportResults = null;
                $flightResults = null;
                $fail = true;
                echo("Connection Failed");
    }
    else
    {
        //Check the blacklist 
        $checkBan = "SELECT * FROM BLACKLIST as b WHERE b.Pssgr_Num = '".$passport."'";
        $banResult = $conn->query($checkBan);
        if ($banResult->error)
        {
            echo $banResult->error;
        }
        else
        {
            if ($banResult->num_rows > 0)
            {
                //Create Query by adding all the banned countries and excluding them from
                //the list available to the user to book flights
                $countryRes = $banResult->fetch_assoc();
                $sql = "SELECT * FROM MAJOR_CITY as c WHERE NOT c.country_name = '".$countryRes['Country']."'";
                while($countryRes = $banResult->fetch_assoc()) {
                      $sql = $sql."AND NOT c.country_name ='".$countryRes['Country']."' ";   
                }
                $sql = $sql." ORDER BY name";
            }
            else{
                $sql = "SELECT * FROM MAJOR_CITY ORDER BY name ";
            }
            
        }
       
                
        $airportResults = $conn->query($sql);
        $airportResults2 = $conn->query($sql);
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>user</title>
		<meta name="description" content="A website of booking for flights connecting Canada and ths US."> 
		<meta name="keywords" content="flight booking,Canadian airline,American airline">
		<meta name="author" content="Aaron Tong, Irene Mayor, Kevin Tran">
		<meta property="og:title" content="Canadian and American airline booking system">
		<meta property="og:url" content="http://causairlinebooking.000webhostapp.com">
		<meta property="og:description" content="A website of booking for flights connecting Canada and the US. Customer can simply book a flight by creating an account  
		and providing their personal information such as passport number, name etc. After they search for their preferred flight, they can obtain the current bit price for that.">
           <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <!-- Latest compiled and minified CSS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class='panel panel-default'>
        <?php
                        echo "<h2 style='padding-left:30px;'>WELCOME!</h2>";
			echo "<br />\n";
                        echo "<div class='col-md-12'>";
                        echo "<img class='col-md-2' style='max-height:200px;' src='https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcTmaaNMmvIFizgPIN6EAiejHDv_rZqyznBLLu7IeGK-34F6VGDg6fN8xQ'>";
                        echo "<div class='col-md-10'><h4>Your Info</h4><ul class='list-group'>";
			echo "<li>";
			echo "Name: $name";
			echo "</li>";
			echo "<li>Passport: $passport</li>";
			echo "<li>Birthdate:".date('d-M-Y', strtotime($birthdate))."</li>";
			echo "<li>Citizenship: $citizenship</li>";
			echo "<li>Cardholder Name: $cardholdername</li>";
			echo "<li>Security Num **".substr($securitynumber, 2)."</li>";
			echo "<li>Card Number: *****".substr($creditcardnumber, 5)."</li>";
			echo "<li>Expiry Date: $expirydate</li>";
			echo "<li>Billing Address: $billingaddress</li>";
			echo "</ul>";
        ?>
        </div></div><br>
        <form style='padding-left:30px;' id='seebidhistory' method="post" action="seebithistory.php">
    <?php echo "<input type='hidden' name='passport' value='".$passport."'>" ?>
    <input  form='seebidhistory' class='btn btn-default' type="submit" value='See Your Bids'>
</form>
<form style='padding-left:30px;' id='edituser' method="post" action="edituserinfo.php">
    <input  form='edituser'  class='btn btn-default' type="submit" value='Edit Your Info'>
</form>
<form style='padding-left:30px;' id='logout' method="post" action="index.php">
    <input  form='logout'  class='btn btn-default' type="submit" value='Log Out'>
</form>
        <!--Flight Search User Input Panel-->
         <div class='panel-heading'>
             <h4 class='panel-title'>Start Flight Search!</h4>
        </div>
        <div class='panel-body'>
            <form id='airportinfoform' method='post' action='flight_result.php'></form>
            <label for="depdate">Enter Departure Date (Optional):</label>
            <?php echo"<input id='depdate' type='date' min='2016-01-01' form='airportinfoform' name='DepDate' value='".$dep_date."'>"; ?>
               <br>
            <label for="sel1">Select Departure City:</label>
            <select name='DepAirport' form='airportinfoform' class="form-control" id="sel1">
                   <?php
                    if ($airportResults->num_rows > 0 && $airportResults != null) {
                        // output data of each row
                        while($row = $airportResults->fetch_assoc()) {
                            if ($row["name"] == $dep_airport)
                                echo "<option selected>" . $row["name"]. "</option>";
                            else
                                echo "<option>" . $row["name"]. "</option>";
                        }
                    } 
                    else 
                        {
                       echo "<option>No Results Found. </option>";
                    }
                        //Insert options from list in database
                   ?>
           </select>
            <a href="verify.php"></a>
            <label for="sel1">Select Arrival City:</label>
            <select name='ArrAirport' form='airportinfoform' class="form-control" id="sel2">
                   <?php
                    if ($airportResults2->num_rows > 0 && $airportResults2 != null) {
                        // output data of each row
                        while($row2 = $airportResults2->fetch_assoc()) {
                            if ($row2["name"] == $arr_airport)
                                echo "<option selected>" . $row2["name"]. "</option>";
                            else
                                echo "<option>" . $row2["name"]. "</option>";
                        }
                    } 
                    else 
                        {
                       echo "<option>No Results Found.</option>";
                    }
                    $conn->close();
               ?>
            </select>
            <input form='airportinfoform' type='submit' value='Search' class='btn btn-default'>
        </div>
       </div>
    </body>

</html>