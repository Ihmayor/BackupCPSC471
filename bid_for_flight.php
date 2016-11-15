<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php 
        session_start();
        if (isset($_SESSION['flightidforbid'])) {
            
                $test = $_SESSION["flightidforbid"];
                unset($_SESSION['flightidforbid']);
        }
        else
        {
            $test = "Invalid Session Variable";
        }
        if (isset($_GET['flightidforbid']))
        {
        $_SESSION['flightidforbid']=$_GET['flightidforbid'];
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
            <h2 style="color:black;background-color:tan">Status: Open </h2>
             <table>
             <thead style="color:white">
                            <tr>
                                <th class="col-md-2">Flight ID</th>
                                <th class="col-md-2">Bid ID</th>
                                <th class="col-md-2">Amount(Order Later)</th>
                                <th class="col-md-3">Time of Bid</th>
                                <th class="col-md-3">User</th>
                            </tr>
                        </thead>
                        <tbody>
            <?php
            
            
            
        
           
             for ($x = 0; $x <= 10; $x++) {
                echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                ."<td>".$test."</td>"
                ."<td>Bid ID: 10101</td>"
                ."<td>470.00"."$"."</td>"
                ."<td>Nov 21, 2016 14:01:22</td>"
                ."<td>User Info with BID. Generic or The Current User's name</td>"
                . "<td><a href='bidstatus.php'><button onclick='setBidStatusVariable'>See Status</button></a></td>"
                . "</tr>";
                } 
            // put your code here
            ?>
                        
             </tbody>
        </table>

                     <script text="javascript">
                                $(document).ready(function(){
                                $('.button').click(function(){
                                var clickBtnValue = $(this).val();
                                var ajaxurl = 'bid_for_flight.php',
                                data =  {'action': clickBtnValue};
                                $.post(ajaxurl, data, function (response) {
                                    
                                // Response div goes here.
                                });
                                });

                                });
                            </script>
             <input type="submit" class="button" name="action" value="addbid"/>
                  <?php
                    if (isset($_POST['action'])) {
                        switch ($_POST['action']) {
                            case 'addbid':
                                insert();
                                break;
                        }
                    }
                    function insert() {
                        echo "The insert function is called.";
                        exit;
                    }
                    ?>   
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
