<?php if (!function_exists("GetSQLValueString")) { function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")  
{ if (PHP_VERSION < 6) {     $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;   }
global $piercecty;
$theValue = $theValue!==NULL ?  $theValue : ''; // workaround for php 8.1 deprecating NULL in mysqli_real_escape_string 
$theValue = mysqli_real_escape_string($piercecty, $theValue);
  switch ($theType) {  case "text":  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";       break;    
    case "long":
    case "int":       $theValue = ($theValue != "") ? intval($theValue) : "NULL";   break;
    case "double":       $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";       break;
    case "date":       $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";       break;
    case "defined":       $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;       break;
  }
  // $theValue=htmlspecialchars($theValue);
  return $theValue;
} } ?>