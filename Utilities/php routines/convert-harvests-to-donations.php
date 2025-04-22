<?php
// converts crops and weights in harvests to donations
// Run it ONLY once
require_once('../../Connections/piercecty.php'); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body class="SH">
<div id="container">
<div id="mainContent">
<?php
$harvestsq="select ID_harvest from harvests order by ID_harvest";
$rsHarvests=mysqli_query($piercecty,$harvestsq);
while($hrow=mysqli_fetch_assoc($rsHarvests)) {
extract($hrow);
echo "<br /><br />HARVEST: $ID_harvest";
for($ct=1;$ct<11;++$ct) {
$cropq="select crop$ct as ID_crop, wgt$ct as pounds from harvests where ID_harvest=$ID_harvest";
$rsCrops=mysqli_query($piercecty,$cropq);
$croprow=mysqli_fetch_assoc($rsCrops);
extract($croprow);
if($ID_crop>0 and $pounds>0) {
$insertcropsq="insert into donations (ID_harvest, ID_crop, pounds) values ($ID_harvest, $ID_crop, $pounds)";
echo "<br />$insertcropsq";
$rsInsert=mysqli_query($piercecty,$insertcropsq);
} // end of if crop in slot
} // end of 10 slots
} // end of all harvests
?>
</div>
</div>
</body>
</html>
