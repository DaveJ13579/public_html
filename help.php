<?php 
// catches all Page Help links and parses the page that sent it to put together the link to the help file for that page.
if (!isset($_SESSION)) { session_start(); }
$from = isset($_SESSION['from'])  ? $_SESSION['from'] : "";
$start = strripos($from, '/');
$end = strripos($from, '.php');
$len = strlen($from);
$ind = substr($from,$start+1,$end-$start-1);
$helpfile = $ind."-help.php";
if(!file_exists($helpfile)) {$helpfile = "no-help.php";}
include_once($helpfile);
?>

