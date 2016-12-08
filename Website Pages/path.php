<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php session_start();
        //Set up Credentials
        $servername = "localhost";
        $username = "id181715_cpsc471";
        $password = "cpsc471";
        $dbname = "id181715_dbcpsc471";
        // Create connection
        $fail = true;
        $conn = new mysqli($servername, $username, $password,$dbname);?>
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
        <form id="returnForm" method="post" action="flight_result.php"></form>
        <input class="btn btn-default" form="returnForm" type="submit" value="Return" name="return">
        <table>
          <thead style="color:black">
                            <tr>
                                <th class="col-md-1">Path-No</th>
                                <th class="col-md-2">Departure Airport</th>
                                <th class="col-md-2">Connecting Airport</th>
                                <th class="col-md-2">Destination Airport</th>
                            </tr>
                        </thead>
                        <tbody>
        <?php
                //Query for path information given flightid received
                $queryPath = "SELECT * FROM PATH as p WHERE p.flight_id = '".$_POST['flightid']."'";
                $pathResults = $conn->query($queryPath);
                //Error Checking
                if ($conn->error)
                    echo "Error: ".$conn->error;
                
                //Echo The results of the path.
                //if there is more than one and the maximum generated of connected paths is 2 (just for the sake of simplicity)
                if ($pathResults->num_rows > 1 && $pathResults != null ) {
                        // output data of each row
                        //Fetch and display information of flights, including the airport's city names
                        $row = $pathResults->fetch_assoc();
                        $row2 = $pathResults->fetch_assoc();
                        echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                         ."<td>".$row['path_no']."</td>"
                         ."<td>".$row['airport_name']."</td>"
                         ."<td>".$row['airport2_name']."</td>"
                         ."<td>".$row2['airport2_name']." </td>"
                         ."</tr>";

                        $arrRes= $conn->query("Select a.cityName from  AIRPORT as a WHERE a.Name = '".$row2['airport2_name']."'");
                         if($conn->error){
                            echo "Error!".$conn->error;
                        }
                        $arrCity =$arrRes->fetch_assoc(); 
                     
                        $depRes = $conn->query("Select a.cityName from  AIRPORT as a WHERE a.Name = '".$row['airport_name']."'");
                         if($conn->error){
                            echo "Error!".$conn->error;
                        }
                                              
                        $depCity = $depRes->fetch_assoc();
                        
                        echo "<input type='hidden' form='returnForm' name='ArrAirport' value='".$arrCity["cityName"]."'>"
                         . "<input type='hidden' form='returnForm'  name='DepAirport' value='".$depCity["cityName"]."'>";
                           
                           
                    }
                else if ($pathResults->num_rows > 0 && $pathResults != null ) {
                        //This conditional is to handle only one path has been generated for one flight. So connections.
                        // output data of each row
                        $row = $pathResults->fetch_assoc();
                        echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                            ."<td>".$row['path_no']."</td>"
                            ."<td>".$row['airport_name']."</td>"
                            ."<td>None this is a direct flight</td>"
                            ."<td>".$row['airport2_name']." </td>"
                            ."</tr>";
                        //Again fetch the necessary cityname information from airport information
                        $sql = "Select a.cityName from AIRPORT as a WHERE a.Name = '".$row['airport2_name']."'";
                        $arrRes = $conn->query($sql);
                          if($conn->error){
                            echo "Error!".$conn->error;
                        }
                        $arrCity =$arrRes->fetch_assoc(); 

                        $sql = "Select a.cityName from  AIRPORT as a WHERE a.Name = '".$row['airport_name']."'";
                        $depRes = $conn->query($sql);
                        if($conn->error){
                            echo "Error!".$conn->error;
                        }
                        $depCity =$depRes->fetch_assoc();

                      //This is to be used with the return button
                        echo "<input type='hidden' form='returnForm' name='ArrAirport' value='".$arrCity["cityName"]."'>"
                         . "<input type='hidden' form='returnForm'  name='DepAirport' value='".$depCity["cityName"]."'>";
                           
                    }
                else
                {  //In the case that no paths are found echo the following and inform the user.
                    echo "<tr style='background-color:white; border-style:solid;border-width: 5px;'>"
                   ."<td>NONE FOUND</td>"
                   ."<td>N/A</td>"
                   ."<td>N/A</td>"
                   ."<td>N/A </td>"
                   . "</tr>";
                }
                
                $conn->close();
            ?>
            </tbody>
        </table>
    </body>
</html>
