<?php if(!isset($_SESSION)) session_start();
// echo $_SERVER["SERVER_ADDR"].' '.$_SERVER["SERVER_NAME"];
if($_SERVER["SERVER_ADDR"]=="::1" || $_SERVER["SERVER_ADDR"]=="127.0.0.1" ) {
// on local server
$hostname_piercecty="localhost";
$database_piercecty="piercec4_piercecty";
$username_piercecty="root";
$password_piercecty="";
} else {
 //  on production server
$hostname_piercecty = "localhost";
$database_piercecty = "piercec4_piercecty";
$username_piercecty = "piercec4_gweb";
$password_piercecty = "piercegweb1";
}
$piercecty = mysqli_connect($hostname_piercecty, $username_piercecty, $password_piercecty, $database_piercecty);
date_default_timezone_set('America/Los_Angeles');
$queryt="set time_zone='-8:00'";
$rsTime=mysqli_query( $piercecty, $queryt);
// set SQL mode so that upgrades to MySQL 5.7 do not crash some queries
$querymode ="SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'";
$rsSQLmode=mysqli_query($piercecty, $querymode);?>
