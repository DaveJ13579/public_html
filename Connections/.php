<?php 
if($_SERVER["SERVER_NAME"] == "localhost"  &&  $_SERVER["SERVER_ADDR"] == "::!") {
// on local server
$hostname_piercecty = "localhost";
$database_piercecty = "piercecty";
$username_piercecty = "root";
$password_piercecty = "";
} else {
// on production server
$hostname_piercecty = "localhost";
$database_piercecty = "twpalygj_piercecty";
$username_piercecty = "twpalygj_dyates";
$password_piercecty = "passemezz1";
}
$piercecty = mysqli_connect($hostname_piercecty, $username_piercecty, $password_piercecty, $database_piercecty);
date_default_timezone_set('America/Los_Angeles');
$queryt="set time_zone='-8:00'";
$rsTime=mysqli_query( $piercecty, $queryt);
?>