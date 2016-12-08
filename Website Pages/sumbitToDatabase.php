<?php
session_start();

//Connection Information
$servername = "localhost";
$username = "id181715_cpsc471";
$password = "cpsc471";
$dbname = "id181715_dbcpsc471";
$conn = new mysqli($servername, $username, $password,$dbname);

//Genearte unique bid id based on the an increment of the highest bid id currently in the table
$checkLength = $conn->query("Select Max(bid_id) as num FROM BID");
$row = $checkLength->fetch_assoc();
$bidid = $row["num"]+1;

//Insert BID into table
//Echo momentarily on screen
$queryInsert = "INSERT INTO BID (bid_id,seat_id,time, isWinner,cost) VALUES ('".$bidid."','".$_POST['seatid']."','".date('Y-m-d H:i:s')."',0,'".$_POST['amntBid']."')";
if ($conn->query($queryInsert) === TRUE) {
    echo "New Bid added successfully<br/>";
    $queryInsert2 = "INSERT INTO PASSENGER_HAS_BID (bid_id,passport_num) VALUES ('".$bidid."','".$_SESSION['userID']."')";
    if ($conn->query($queryInsert2) === TRUE) {
        echo "New Passenger has bid added successfully<br/>";
    } else {
        echo "<input form='fr' type='hidden' name='errorInPassenger' value='".$conn->error."' >";
        echo "Error: " . $queryInsert2 . "<br>" . $conn->error;
    }
} 
else {
    echo "<input form='fr' type='hidden' name='errorInBid' value='".$conn->error."' >";
    echo "Error: " . $queryInsert . "<br>" . $conn->error;
}
$conn->close();
echo "<input form='fr' type='hidden' name='seatid' value='".$_POST['seatid']."'>";
echo "<input form='fr' type='hidden' name='flightid' value='".$_POST['flightid']."'>";
?>
<form id='fr' name='fr' action='bidstatus.php' method='post'></form>
<script type='text/javascript'>
    //Automatically return to previous page once the insertion is complete
    document.fr.submit();
</script>
