
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php 
    session_start();
    //Fetch all airport information taken from the user prompt 
    $arr_airport =  $_POST['ArrAirport'];
    $dep_airport =  $_POST['DepAirport'];
    
    //If a date is set, fetch that date. It can be null
    $dep_date = $_POST['DepDate'];
    
    //For passengers that are blacklisted from certain countries we want to ensure they cannot bid on flights tjere
    $passport = $_SESSION['userID'];
    
    //Set up Connection Information
    $servername = "localhost";
    $username = "id181715_cpsc471";
    $password = "cpsc471";
    $dbname = "id181715_dbcpsc471";
    // Create connection
    $fail = true;
    $conn = new mysqli($servername, $username, $password,$dbname);
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
        //Now that we know the connection works begin fetching the necessary information from the
        //database
                $fail = false;
                //Check if we have the airport information set
                if (isset($arr_airport)&& isset($dep_airport))
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
                    
                    $sql2 = "SELECT f.Flight_id as Flight_id, arrival_time, departure_time, airport_departure_name, a1.cityName as airport_dept_city, airport_arrival_name, a2.cityName as airport_arr_city,ar.Name as airline_name FROM FLIGHT f, AIRLINE_SALE ar, AIRPORT a1, AIRPORT a2 WHERE ar.Flight_id = f.Flight_id AND f.airport_departure_name = a1.Name AND a1.cityName ='".$dep_airport."' AND f.airport_arrival_name = a2.Name AND a2.cityName ='".$arr_airport."' AND Date(f.departure_time) >= '".$dep_date."'";
                    $airportResults = $conn->query($sql);
                    if ($conn->error)
                    {
                        echo $conn->error;
                    }
                    $airportResults2 = $conn->query($sql);
                    if ($conn->error)
                    {
                        echo $conn->error;
                    }
                    $flightResults = $conn->query($sql2);
                    if ($conn->error)
                    {
                        echo "sql2 ".$conn->error;
                    }
                }
                else
                {
                    //If we do not have airport information set
                    //Fetch all possible flights from the database and display them
                    $sql = "SELECT * FROM MAJOR_CITY ORDER BY name";
                    $sql2 = "SELECT * FROM FLIGHT";
                    $airportResults = $conn->query($sql);
                    $airportResults2 = $conn->query($sql);
                    $flightResults = $conn->query($sql2);
                }
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
<div id="bg">
    <?php 
    
    //Under the circumstances of a failed connection, an error message is printed onto the screen.
    if($fail)
        {
        echo "<h4>FAILED TO CONNECT TO DATABASE PLEASE DEBUG.</h4>";
        }
        
    ?>
    <!-- Departure and Airport information is sent back to user.php just for the flight search caching, so going b
    back and forth between the pages isn't jarring and requires re-inputting the information-->
    <form id="returnToUsers" method="post" action="user.php">
            <input class='btn btn-default' type ="submit" name="return" value="Return"> 
            <?php echo"<input type='hidden' name='DepAirport' value='".$dep_airport."'>";
            echo"<input type='hidden' name='ArrAirport' value='".$arr_airport."'>";
            echo"<input type='hidden' name='DepDate' value='".$dep_date."'>";?>
        </form>
    <div class='col-md-12 panel'>
        <!-- This is the form on the top of the screen to allow the user to  search flights with new criteria -->
        <div class='col-md-4'>
            <form id='searchform' method='post' action='flight_result.php'></form>
            <label class='col-md-12'>Departure City</label>
            <select name='DepAirport' form='searchform' class="form-control" id="sel1">
                   <?php
                   //Check that we have airport options, fetched from the database,
                   // such that the user is not banned from those countries with those airports
                    if ($airportResults->num_rows > 0 && $airportResults != null) {
                        // output data of each row
                        while($row = $airportResults->fetch_assoc()) {
                            //If we have a departure airport set from user.php or a past search
                            //set that as the currently selected default option
                            if ($row["name"] == $dep_airport)
                                echo "<option selected>" . $row["name"]. "</option>";
                            else
                                echo "<option>" . $row["name"]. "</option>";
                        }
                    } 
                    else 
                    {
                        //Inform user that no such airports found with the criteria given,
                        //either because they're trying to find a flight from an airport, to the same airport
                        //Or that they're trying to set a date filter that goes beyond the date set for the flight
                       echo "<option>No Results Found. </option>";
                    }
                   ?>
           </select>
      
        </div>
        <div class='col-md-4'>
           <label class='col-md-12'>Arrival City</label>
           <select name='ArrAirport' form='searchform' class="form-control" id="sel2">
                   <?php
                   //Check that we have airport options, fetched from the database,
                   // such that the user is not banned from those countries with those airports
                    if ($airportResults2->num_rows > 0 && $airportResults2 != null) {
                        // output data of each row
                        while($row2 = $airportResults2->fetch_assoc()) {
                            //If we have an arrival airport set from user.php or a past search
                            //set that as the currently selected default option
                            if ($row2["name"] == $arr_airport)
                                echo "<option selected>" . $row2["name"]. "</option>";
                            else
                                echo "<option>" . $row2["name"]. "</option>";
                        }
                    } 
                    else 
                    {
                        //Inform user that no such airports could be found, usually this is a problem with the site connecting with
                        //the database
                       echo "<option>No Results Found.</option>";
                    }
               ?>
            </select>
        </div>
        <div class="col-md-4">
        <label for='inputdate'>Enter Departure Date(Optional):</label>
        <br/>
        <?php
        //This the to set the filter date. Set its default value to any value received from user.php
            echo "<input id='inputdate' min='2016-01-01' form='searchform' type='date' name='DepDate' value='".$dep_date."'/>";
        ?>
         </div>
         <br>
         <input type='submit' form='searchform' value='Search Flights' class='col-md-4 center-block btn btn-default'>  
      
    </div>
    <table class="table" ng-show="event.liveCount > 0">
          <thead style="color:white">
                            <tr>
                                <th class="col-md-1">Flight ID</th>
                                <th class="col-md-2">Airline Company </th>
                                <th class="col-md-2">Departure City</th>
                                <th class="col-md-2">Arrival City</th>
                                <th class="col-md-2">Departure Time</th>
                                <th class="col-md-2">Arrival Time</th>
                                <th class="col-md-1"></th>
                            </tr>
                        </thead>
                        <tbody>
	  <?php
        
                    //Check that we have flight results from the given criterion. 
                    if ($flightResults->num_rows > 0 && $flightResults != null) {
                        // output data of each row. Loop through each flight returend by the search.
                        while($row3 = $flightResults->fetch_assoc()) {
                            //Echo a unique form per flight all to connect to the same .php page
                            //This is to ensure no value in a form is ever overwritten by the proceeding row's data
                            echo '<form id="airportSeatForm'.$row3["Flight_id"].'" method="post" action="bid_for_flight.php"></form>';
                            echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                                ."<td>".$row3["Flight_id"]."<input type='hidden' form='airportSeatForm".$row3["Flight_id"]."' name='flightid' value='".$row3["Flight_id"]."'></td>"
                                ."<td>".$row3["airline_name"]."</td>"
                                ."<td>".$row3["airport_dept_city"]."<input type='hidden' form='airportSeatForm".$row3["Flight_id"]."' name='DepAirport' value='".$dep_airport."'></td>"
                                ."<td>".$row3["airport_arr_city"]."<input type='hidden' form='airportSeatForm".$row3["Flight_id"]."' name='ArrAirport' value='".$arr_airport."'></td>"
                                ."<td>".$row3["departure_time"]."</td>"
                                ."<td>".$row3["arrival_time"]."</td>"
                                ."<td><div>"
                                    . "<input form='airportSeatForm".$row3["Flight_id"]."' type='submit' value='See Seats Available' name='bid_for_flight' class='btn btn-default'>"
                                    . "<br>"
                                    . "<form id='flightPathForm".$row3["Flight_id"]."' method='post' action='path.php'></form>"
                                    . "<input type='hidden' form='flightPathForm".$row3["Flight_id"]."' name='flightid' value='".$row3["Flight_id"]."'>"
                                    . "<input class='btn btn-default' type='submit' form='flightPathForm".$row3["Flight_id"]."' name='flightPaths' value='See Paths'/></div>"
                                    . "</td>"
                                ."</tr>";
                        }
                    } 
                    else 
                        {
                        //Echo and inform the user that no such flights could be found within the given criteria
                        //Either this is a cause of a flight from a city to the same city 
                        //Or a matter of the date filter excluding too many flights
                       echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                                ."<td>None Found</td>"
                                ."<td>None Found</td>"
                                ."<td>None Found</td>"
                                ."<td>N/A</td>"
                                ."<td>N/A</td>"
                                ."<td><input type='submit' value='No Flights Available' class='btn btn-default'></td>"
                                ."</tr>";
                    }
                    $conn->close();
                    ?>
                  </tbody>
       </table>
<!--    <a href="http://causairlinebooking.000webhostapp.com/bid_for_flight.html"><button style="color:blue;border-radius:10px;position: absolute;right:0;left:10%;top:25%;">Find Bid</button></a>
-->
</div>
</body>
<!-- Styles, primarly used for the background image.-->
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
