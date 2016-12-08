<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
session_start();
$servername = "localhost";
$username = "id181715_cpsc471";
$password = "cpsc471";
$passport= $_POST["passport"];
$dbname = "id181715_dbcpsc471";
$con=mysqli_connect($servername,$username,$password,$dbname);
// Check connection
if (mysqli_connect_errno($con))
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
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
<head>
</head>
<body>
<div id="bg">
<form id="returnToUsers" method="post" action="user.php">
        <input class='btn btn-default' type ="submit" name="return" value="Return"> 
        <?php echo"<input type='hidden' name='DepAirport' value='".$dep_airport."'>";
        echo"<input type='hidden' name='ArrAirport' value='".$arr_airport."'>";
        echo"<input type='hidden' name='DepDate' value='".$dep_date."'>";?>
	</form>
<div class="col-md-6 col-md-offset-3  form-group panel panel-default">
     <div class='panel-heading'>
        Details of your bid history
     </div>
	 <?php
		$result = $con->query("SELECT * FROM PASSENGER_HAS_BID AS P WHERE P.passport_num='$passport'");
		if($result->num_rows == 0) {
			redirect('You have not made any bids so your bid history is empty. Please bid on a flight to open this page.','/user.php');
		}else{
			while($row=$result->fetch_object()){
				$bid=$row->bid_id;
				$result1=$con->query("SELECT * FROM BID AS B WHERE B.bid_id='$bid'");
				if($result1->num_rows==0){
					redirect('bit history for you is empty','/user.php');
				}else{
					while($row1=$result1->fetch_object()){
						$seat_id=$row1->seat_id;
						$cost=$row1->cost;
						$time=$row1->time;
						$win=$row1->isWinner;
						$result2=$con->query("SELECT * FROM SEAT AS S WHERE S.seat_id='$seat_id'");
						if($result2->num_rows==0){
							redirect('bit history for you is empty','/user.php');
						}
						$row2=$result2->fetch_object();
						$flight_id=$row2->flight_id;
						echo "bid id: {$bid}";
						echo "<br />\n";
						echo "seat id: {$seat_id}";
						echo "<br />\n";
						echo "Time: {$time}";
						echo "<br />\n";
						echo "Cost: {$cost}";
						echo "<br />\n";
						echo "Flight id: {$flight_id}";
						echo "<br />\n";
						if($win==0){
							$dt=date("Ymd");
							$endAuctionDate = $row2->end_auction_date;
							if ( $dt > date('Ymd',strtotime($endAuctionDate))){
                                                            $queryres = $con->query("SELECT MAX(b.cost) as maxcost, b.seat_id from BID as b where b.seat_id = '".$seat_id."' GROUP BY b.seat_id");
                                                            if ($con->error)
                                                                {echo $con->error;}
							    $queryRow = $queryres->fetch_object();
							    $maxBid = $queryRow->maxcost;
							    if($cost == $maxBid){
                                                                $con->query(" update BID set isWinner = 1 where bid_id = '".$bid."'"); 
                                                                    if ($con->error)
                                                                    {echo $con->error;}
								echo "Winner: True";
                                                                
                                                                $fQuery = $con->query("Select * from FLIGHT as f where f.Flight_id = '".$flight_id."'");
                                                                $fQueryRes = $fQuery->fetch_object();
                                                                $departure = $fQueryRes->airport_departure_name;
                                                                $arrival = $fQueryRes->airport_arrival_name;
                                                                $datetime = $fQueryRes->departure_time;
                                                                $dateFlight = date('Y-m-d', strtotime($datetime));
                                                                $time = date('H:i:s', strtotime($datetime));
                                                                $name = $_SESSION["userName"];
                                                                echo "<br><button class='btn btn-default' data-toggle='collapse' data-target='#".$flight_id."Ticket'>See Ticket</button>";
                                                                echo "<div class='collapse' style='border: 2px solid grey' id='".$flight_id."Ticket'>"
                                                                . "<div>"
                                                                . "Name: {$name}<br>"
                                                                . "From: {$departure} <br>"
                                                                . "Flight: {$flight_id}<br>"
                                                                . "To: {$arrival}<br>"
                                                                . "Date: {$dateFlight}<br>"
                                                                . "Time: {$time}<br>"
                                                                . "</div>"
                                                                . "</div>";

							    }
                                                            else{
								echo "Winner:False";
                                                            }								
							}
							else{
								echo "Winner: In Progress";
							}
						}
                                                else{
      							echo "Winner:True";

                                                        $fQuery = $con->query("Select * from FLIGHT as f where f.Flight_id = '".$flight_id."'");
                                                        $fQueryRes = $fQuery->fetch_object();
                                                        $departure = $fQueryRes->airport_departure_name;
                                                        $arrival = $fQueryRes->airport_arrival_name;
                                                        $datetime = $fQueryRes->departure_time;
                                                        $dateFlight = date('Y-m-d', strtotime($datetime));
                                                        $time = date('H:i:s', strtotime($datetime));
                                                        $name = $_SESSION["userName"];
                                                        echo "<br><button class='btn btn-default' data-toggle='collapse' data-target='#".$flight_id."Ticket'>See Ticket</button>";
                                                        echo "<div class='collapse' style='border: 2px solid grey' id='".$flight_id."Ticket'>"
                                                        . "<div>"
                                                        . "Name: {$name}<br>"
                                                        . "From: {$departure} <br>"
                                                        . "Flight: {$flight_id}<br>"
                                                        . "To: {$arrival}<br>"
                                                        . "Date: {$dateFlight}<br>"
                                                        . "Time: {$time}<br>"
                                                        . "</div>"
                                                        . "</div>";

						} 
						echo "<br />\n";
						echo "<br />\n";
						echo "<br />\n";
						
					}
				}
			}
		}
	?>
	 
</div>
	
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