<?php 
    session_start();
    
    //initialize user variables
    //Fetch appropriate session variables for user
    $userName = "None Found";
    $userid= null;
    if (isset($_SESSION['userID']))
    {
        $userID = $_SESSION['userID'];
    }
    //Set up database connection credentials
    $servername = "localhost";
    $username = "id181715_cpsc471";
    $password = "cpsc471";
    $dbname = "id181715_dbcpsc471";
    $conn = new mysqli($servername, $username, $password,$dbname);
?>
<!DOCTYPE html>
<!--
-->
<html> 
   <head>
        <meta charset="UTF-8">
        <title></title>
          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
                <!-- Latest compiled and minified CSS -->
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>

                <!-- Latest compiled and minified JavaScript -->
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
        </head> 
   <body>
        <?php
                    //Check that the necessary information is set and who the user again what they've selected
                    if (isset($_POST['seatid'])) {
         
                        $seatid = $_POST['seatid'];
                        $flightid = $_POST['flightid'];
                         if (isset($_POST['errorInBid']))
                        {
                            echo "ERROR IN SUBMITTING BID";
                            unset($_POST['errorInBid']); 
                        }
                        if (isset($_POST['errorInPassenger']))
                        {
                            echo "ERROR IN SUBMITTING PASSENGER BID";
                            unset($_POST['errorInPassenger']); 
                            
                        }
        
                        echo "<h2>Seat ID: ".$seatid."</h2>";
                        echo "<h2>Flight ID: ".$flightid."</h2>";
                    }
                    else
                    {   echo "<h2>NO SEAT SELECTED</h2>";
                        echo "<h2>NO FLIGHT SELECTED</h2>";
                    }
                  
        ?>
       <form id='backbutton'  method='post' action='bid_for_flight.php'></form>
       <input type='submit' class='btn btn-default' name='return' value='Return' form='backbutton'>
       <?php
            //Upon pressing the back button ensure that all information is properly remembered and passed back to retain
            //Smooth transitions from page to page without having to constantly unset session variables
            $query1 = "SELECT a.cityName as arrName, a2.cityName as depName FROM AIRPORT  as a,AIRPORT as a2, FLIGHT as f WHERE f.Flight_id ='".$flightid."' AND f.airport_departure_name = a.Name AND f.airport_arrival_name = a2.Name";
            $cityRes = $conn->query($query1);
            $row = $cityRes->fetch_assoc();
            //Hide the data being sent back as hidden input in the return form
            echo "<input form='backbutton' type='hidden' name='flightid' value='".$flightid."'>";
            echo "<input form='backbutton' type='hidden' name='ArrAirport' value='".$row['arrName']."'>";
            echo "<input form='backbutton' type='hidden' name='DepAirport' value='".$row['depName']."'>";
        ?>
       
       
       
       <form id="submitBidInfo" name='submitBidInfo' method="post" action="sumbitToDatabase.php"></form>
        <button class='btn btn-default' id="addBidBtn">Add Bid</button>
        <div class="col-md-12" style='display:none;' id="formForBidInfo">
            <label>Enter Amount to Bid: </label>
            <?php 
                //Prevent users from bidding less than the base price and the currently highest bid
                //First fetch the highest bid on the seat
                $query1 = "SELECT MAX(b.cost) as max from BID as b WHERE b.seat_id = ".$seatid."";
                $maxBid = $conn->query($query1);
                if ($maxBid->error)
                    echo "Error: ".$maxBid->error;
                $row = $maxBid->fetch_assoc();
                //If no bids have been made, this max will of course return 0
                if ($row['max'] ==0)
                {
                    //Then fetch the base price of the seat and set that as the minimum default of a bid
                    $query2 = "SELECT baseprice from SEAT where seat_id = '".$seatid."'";
                    $resultQ = $conn->query($query2);
                    if ($conn->error)
                        echo $conn->error;
                    $resultSeat = $resultQ->fetch_assoc();
                    $row['max'] = $resultSeat['baseprice'];
                }
                else
                {
                    //Add plus one because every bid must be greater that the previous bid by at least one dollar
                    //This is to prevent ties.
                    $row['max'] = $row['max']+1;
                }
                echo "<input form='submitBidInfo' type='number' min='".$row['max']."' step='any' pattern='(d{3})([.])(d{2})' id='amntBid' name='amntBid' value=''>";
                echo "<input type='hidden' form='submitBidInfo' name='seatid' value='".$seatid."'>";
                echo "<input type='hidden' form='submitBidInfo' name='flightid' value='".$flightid ."'>";
            ?>
            <input class='btn btn-default' form='submitBidInfo' type='submit' name='Bid On Seat!'>
            <button class='btn btn-default' id='cancelBid'>Cancel Bid</button>
        </div>
        <script language="javascript">
            //This is collapse and hide the add bid form. 
            $('#addBidBtn').on('click', function(){
                $('#formForBidInfo').show();
            });
            $('#cancelBid').on('click', function(){
                $('#formForBidInfo').hide();
            });
        </script>

        <table>
             <thead style="color:black">
                <tr>
                    <th class="col-md-1">Bid ID</th>
                    <th class="col-md-2">(Order)Amount of Bid</th>
                    <th class="col-md-4">Time of Bid</th>
                    <th class="col-md-2">User</th>
                </tr>
            </thead>
            <tbody>
                 <?php
                //Fetch Bids 
                $queryBid = "SELECT b.bid_id as bid_id, b.cost as cost, b.time as time, d.name as name FROM BID as b, PASSENGER_HAS_BID as p,PASSENGER as d WHERE b.seat_id = '".$seatid."' AND p.passport_num = '".$userID."' AND  p.passport_num = d.passport_num  AND b.bid_id = p.bid_id ORDER BY b.cost DESC";
                $bidResults = $conn->query($queryBid);
                if ($conn->error)
                    echo "Error: ".$conn->error;

                //Check that have fetched bid results in order to display them
                if ($bidResults->num_rows > 0 && $bidResults != null ) {
                        // output data of each row
                        while($row = $bidResults->fetch_assoc()) {
                           echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                            ."<td>".$row['bid_id']."</td>"
                            ."<td>".number_format((float)$row['cost'], 2, '.', '')."$</td>"
                            ."<td>".$row['time']."</td>"
                            ."<td>".$row['name']." </td>"
                            ."</tr>";
                        }
                    }
                else
                {
                    //Inform user of lack of bids if no bids have been made on the seat.
                    echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                   ."<td>NONE FOUND</td>"
                   ."<td>N/A</td>"
                   ."<td>Date of Auction Closed: N/A</td>"
                   ."<td>Current Lead: None Found </td>"
                   . "</tr>";
                }
                    ?>
            </tbody>
        </table>
    </body>
</html>