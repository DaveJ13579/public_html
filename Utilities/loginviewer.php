<?php
if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all";


$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');

// date_default_timezone_set("America/Los_Angeles");
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
			           $nametemp =  "";
                       $datetemp =  "";
                       $statustemp = "";

if (isset($_POST["MM_update"]) && ($_POST["MM_update"] == "filtersform")) {
                       $nametemp =  $_POST['nametemp'];
                       $datetemp =  $_POST['datetemp'];
                       $statustemp = $_POST['statustemp'];

}
else { $nametemp = ""; }

if (isset($_POST["allrec"]) && ($_POST["allrec"] == "All")) {
	
	
	$query_rsLogins = sprintf("SELECT * FROM loginlog ORDER BY loginindex DESC");
	$rsLogins = mysqli_query($piercecty, $query_rsLogins) or die(mysqli_error($piercecty));
	$row_rsLogins = mysqli_fetch_assoc($rsLogins);
	$totalRows_rsLogins = mysqli_num_rows($rsLogins);
} else {
$query_rsLogins = sprintf("SELECT loginindex, username, password, datein, timein, status FROM loginlog
				  WHERE left(username, 4) = left(%s,4) OR datein = %s OR status = %s ORDER BY loginindex DESC",  
				  GetSQLValueString($nametemp, "text"),
				  GetSQLValueString($datetemp, "date"),
				  GetSQLValueString($statustemp, "text"));
	$rsLogins = mysqli_query($piercecty, $query_rsLogins) or die(mysqli_error($piercecty));
	$totalRows_rsLogins = mysqli_num_rows($rsLogins);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>roster viewer</title>
    <style type="text/css">
<!--
-->
    </style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>Login  viewer</strong></h2>
    <p>Filters:</p>
    <form action="loginviewer.php" method="post" name="filtersform">
    Username <input width = "100" type="text" name="nametemp" id="nametemp" />
       &nbsp;&nbsp;
    Date      <input width = "100" type="date" name="datetemp" id="datetemp" />
    Login status    <select name="statustemp" id="statustemp">
       					<option selected="selected"></option>
						<option value="Okay">Okay</option>
						<option value="Failed">Failed</option>
	  </select>    
      	       &nbsp;&nbsp;
		<input type="submit" name="allrec" id="allrec" value="All" />
		<input type="hidden" name="MM_allrec" value="filtersform" />
   <p>
      <input type="submit" name="submit" id="submit" value="Show records" />
      <input type="hidden" name="MM_update" value="filtersform" />
      
    </form>
   
<table width="1240" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
      <tr>
        <th scope="col">Index</th>
        <th scope="col">Username</th>
        <th scope="col">Password</th>
        <th scope="col">Date</th>
        <th scope="col">Time</th>
        <th scope="col">IP address</th>
        <th scope="col">Status</th>
      </tr>
      <?php while ($row_rsLogins = mysqli_fetch_assoc($rsLogins))  { 
      if(isset($row_rsLogins['username']) && $row_rsLogins['username']<>'webmaster') {
	  ?>
      <tr class="centercell">
		<td><?php echo $row_rsLogins['loginindex']; ?></td>
		<td><?php echo $row_rsLogins['username']; ?></td>
		<td><?php echo $row_rsLogins['password']; ?></td>
		<td><?php echo $row_rsLogins['datein']; ?></td>
		<td><?php echo $row_rsLogins['timein']; ?></td>
		<td><?php echo $row_rsLogins['IPaddress']; ?></td>
		<td><?php echo $row_rsLogins['status']; ?></td>
      </tr>
      <?php
	  }  // end of if not webmaster
	    } // end of while row ?>
	     
</table>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</div>
</body>
</html>
