<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php 
        session_start();
        //Fetch information for airport and flight as passed by flight_result.php. This is primarly stored for return page
        $arr_airport =  $_POST['ArrAirport'];
        $dep_airport =  $_POST['DepAirport'];
        $flightid = $_POST['flightid'];
        
        //Connection Information
        $servername = "localhost";
        $username = "id181715_cpsc471";
        $password = "cpsc471";
        $dbname = "id181715_dbcpsc471";
        
        // Create connection
        $fail = true;
        $conn = new mysqli($servername, $username, $password,$dbname);

        //Check that flight information has been passed
        if(isset($flightid))
        {
            $conn = new mysqli($servername, $username, $password,$dbname);
            // Check connection
            if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
                        $seatResults = null;
                        echo("Connection Failed");
            }
            else
            {   
                    //Get all the seats associated with this flight id that was chosen form the past flight_result.php page
                    $sql = "SELECT * FROM SEAT s where s.flight_id = '".$flightid."'";
                    $seatResults = $conn->query($sql);
            }
        }
        else {
            //Inform the user of a problem that occurred when passing flight information.
            echo "<h1>Failure</h1>";
            $flightid = "N/A";
            $flightid = $test;
            echo($testing);
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
                <form id='backbutton' method='post' action='flight_result.php'></form>
                    <input type='submit' form='backbutton' class='btn btn-default' name='return' value='Return'>
                    <?php
                    //This is to create smooth return transition to previous page without constantly resetting session variables.
                      echo "<input type='hidden' form='backbutton' name='ArrAirport' value='".$arr_airport."'>";  
                      echo "<input type='hidden' form='backbutton' name='DepAirport' value='".$dep_airport."' >";
                ?>
                <div class='col-md-12 panel'>
                    <?php
                        //Check the auction date and close any seat from being bid upon if the end auction date has passed.
                        $queryStatus = "Select MAX(s.end_auction_date) as close_time from SEAT as s where s.flight_id = '".$flightid."'";
                        $queryRes = $conn->query($queryStatus);
                        if($queryRes->error)
                        {
                            echo "Error: ".$queryRes->error;
                        }
                        if ($queryRes->num_rows  == 0 )
                        {
                             echo "ERORR CANNOT FIND END AUCTION DATE";
                        }
                        
                        //Fetch the information from the sql results and check it against the current date.
                        $row = $queryRes-> fetch_assoc();  
                        $today = date("Ymd"); 
                        $checkDate = $row["close_time"];
                        if ( date('Ymd', strtotime($checkDate)) < $today)
                        {
                            $isOpen = false;
                            echo "<h2 class='col-md-8' >Status: Closed</h2>";
                        }
                        else
                        {
                            $isOpen = true;
                            echo "<h2 class='col-md-8' >Status: Open</h2>";
                        }
                        
                    ?>
                    <br>
                </div>
                    <table>
                    <thead style="color:white">
                                   <tr>
                                       <th class="col-md-1">Flight ID</th>
                                       <th class="col-md-1">Seat ID</th>
                                       <th class="col-md-4">Close Date of Auction</th>
                                       <th class="col-md-2">Current Lead User</th>
                                       <th class="col-md-2"></th>
                                   </tr>
                               </thead>
                               <tbody >
                   <?php
                   //Check that we do indeed have seat results fro the igven flight id
                   if ($seatResults->num_rows > 0 && $seatResults != null) {
                        // output data of each row
                        while($row = $seatResults->fetch_assoc()) {
                            
                            //Get the name of the current highest bidder on a seat
                            $querySeat = "Select p2.name as Name from BID as b, PASSENGER_HAS_BID as p, PASSENGER as p2 WHERE b.seat_id = '".$row['seat_id']."' AND p.bid_id = b.bid_id AND p2.passport_num = p.passport_num AND b.cost IN (Select MAX(b2.cost) from BID as b2 GROUP BY b2.bid_id)";
                            $findUser = $conn->query($querySeat);
                            if ($conn->error)
                            {
                                echo "Error: ".$conn->error;
                            }
                            $userOwner = "None";
                            if ($findUser->num_rows > 0)
                            {
                                $row2 = $findUser->fetch_assoc();
                                $userOwner = $row2['Name'];
                            }
                            
                            //If the auction is open, enable the bidding functionality on the seats
                            if($isOpen)
                            {    
                                echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                                .'<form id="seatInfoForm'.$row['seat_id'].'" method="post" action="bidstatus.php"></form>'
                                ."<td>".$flightid."<input form='seatInfoForm".$row['seat_id']."' type='hidden' name='flightid' value='".$flightid."'"."</td>"
                                ."<td>".$row['seat_id']."<input form='seatInfoForm".$row['seat_id']."' type='hidden' name='seatid' value='".$row['seat_id']."'></td>"
                                ."<td>".$row['end_auction_date']."</td>"
                                ."<td>".$userOwner." </td>"
                                ."<td><input type='submit' form='seatInfoForm".$row['seat_id']."' value='Bid On Seat' class='btn btn-default'></td>"
                                . "</tr>";
                            }
                            else
                            {
                            //If the auction is closed, display the seat information but prevent the adding of bids
                                echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                                .'<form id="seatInfoForm'.$row['seat_id'].'method="post" action="bidstatus.php"></form>'
                                ."<td>".$flightid."<input form='seatInfoForm".$row['seat_id']."' type='hidden' name='flightid' value='".$flightid."'"."</td>"
                                ."<td>".$row['seat_id']."<input form='seatInfoForm".$row['seat_id']."' type='hidden' name='seatid' value='".$row['seat_id']."'></td>"
                                ."<td>".$row['end_auction_date']."</td>"
                                ."<td>".$userOwner." </td>"
                                ."<td><input disabled='true' type='submit' form='seatInfoForm".$row['seat_id']."' value='Bid On Seat' class='btn btn-default'></td>"
                                . "</tr>";
                            }
                                
                         
                        }
                    } 
                    //Inform the user that there were no seat results found for the given flight id
                    else 
                        {
                            echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                            ."<td>None Found</td>"
                            ."<td>None Found</td>"
                            ."<td>Date of Auction Closed: Jan 01, 0000 00:00:00</td>"
                            ."<td>Current Lead: None Found </td>"
                            ."<td></td>"
                            . "</tr>";
                    }
                   ?>

                    </tbody>
               </table>

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