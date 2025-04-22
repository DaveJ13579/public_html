<?php
$isValid = false; 
$arrGroups = explode(",", $MM_authorizedUsers); 
if (in_array($_SESSION['MM_UserGroup'], $arrGroups)) $isValid = true; 

if (!((isset($_SESSION['MM_Username'])) && ($isValid))) {   
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?". $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo."?accesscheck=" . urlencode($MM_referrer);
  if (isset($_SESSION['MM_Username']) && !$isValid) $MM_restrictGoTo=$_SESSION['LastGoodPage'];
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
$_SESSION['LastGoodPage']=$_SERVER['PHP_SELF'];
?>