<?php 
require_once('../../Connections/piercecty.php');
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

$labelsq="show full columns from custom3";
$rsLabels=mysqli_query($piercecty,$labelsq);
?>
<html>
<body>
<table>
<?php
while($labelrow=mysqli_fetch_assoc($rsLabels)) {
	echo "<tr><td>".$labelrow['Comment']."</td></tr>";
		} 
?>
</table>
</body>
</html>
	
