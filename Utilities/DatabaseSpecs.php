<?php
if (!isset($_SESSION)) { session_start(); }
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../Connections/piercecty.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>database specifications</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
<div id="mainContent">
    <h2><strong>Pierce County Database Specifications</strong></h2>
<?php
 $sql = "SHOW TABLES FROM $database_piercecty";
 $result = mysqli_query($piercecty,$sql);
 while ($trow = mysqli_fetch_row($result)) {
	$tableNames = $trow[0];
	echo "<h2>$tableNames</h2><br />";
	 echo '<table border="1" cellspacing="0"><tr>
	<th>Field</td>
	<th>Type</td>
	<th>Null</td>
	<th>Key</td>
	<th>Default</td>
	<th>Increment</td>
	<th>Notes</td>
	
	</tr>';
	$query = "show full columns from $tableNames";
	$rsQuery=mysqli_query($piercecty, $query);
	if($rsQuery and mysqli_num_rows($rsQuery)>0) {
		while($row = mysqli_fetch_row($rsQuery)) {
	echo "<tr>
	 <td>$row[0]</td>
	 <td>$row[1]</td>
	 <td>$row[3]</td>
	 <td>$row[4]</td>
 	 <td>$row[5]</td>
	<td>$row[6]</td>
	<td>$row[8]</td>
	</tr>";
	}
	} // if any rows
	echo '</table>';
 } // end of tables

?>
</div>
</div>
</body>
</html>
