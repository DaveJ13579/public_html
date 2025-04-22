<?php 
// STEP 1
// imports data straight from trees table to the sites table altering column names as necessary
// download fresh trees table from firstharvest-piercecty
// drop current trees locally
// import trees locally
// empty and reset sites
// run script
require_once('../../Connections/piercecty.php');
require_once('../../includes/sqlcleaner.php');

$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

$inq="select * from trees";
$rsIn=mysqli_query($piercecty, $inq) or die(mysqli_error($piercecty));
while($row=mysqli_fetch_assoc($rsIn)) {
extract($row);
$ID_site=$row['ID_crop'];
$farm=$row['tlname'];
$contact1=trim($row['tfname'].' '.$farm);
$phone1=$row['tphone'];
$phone2=$row['tphone2'];
$email1=$row['temail'];
$region=$row['neighbor'];
$crops=$row['crop_type'];
$size=$row['crop_num'];
$howhear=$row['how_hear'];
$otherinfo=$row['other_info'];
$Active=$row['Active'];
$venue=$row['venue'];


$insertq = sprintf("INSERT INTO `sites` (ID_site, farm, contact1, phone1, phone2, email1, address, city, state, zip, maddress, mcity, mstate, mzip, region, property_rel, landlord, crops, size, location, height, when_ripe, disease, disease_text, spray, spray_text, howhear, otherinfo, latitude, longitude, Active, venue, regdate) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($ID_site, "int"),
                       GetSQLValueString($farm, "text"),
                       GetSQLValueString($contact1, "text"),
                       GetSQLValueString($phone1, "text"),
                       GetSQLValueString($phone2, "text"),
					   GetSQLValueString($email1, "text"),
                       GetSQLValueString($address, "text"),
                       GetSQLValueString($city, "text"),
                       GetSQLValueString($state, "text"),
                       GetSQLValueString($zip, "text"),
                       GetSQLValueString($maddress, "text"),
                       GetSQLValueString($mcity, "text"),
                       GetSQLValueString($mstate, "text"),
                       GetSQLValueString($mzip, "text"),
					   GetSQLValueString($region, "text"),
					   GetSQLValueString($property_rel, "text"),
					   GetSQLValueString($landlord, "text"),
					   GetSQLValueString($crops, "text"),
					   GetSQLValueString($size, "text"),
					   GetSQLValueString($location, "text"),
					   GetSQLValueString($height, "text"),
					   GetSQLValueString($when_ripe, "text"),
					   GetSQLValueString(ucfirst($disease), "text"),
					   GetSQLValueString($disease_text, "text"),
					   GetSQLValueString(ucfirst($spray), "text"),
					   GetSQLValueString($spray_text, "text"),
					   GetSQLValueString($howhear, "text"),
					   GetSQLValueString($otherinfo, "text"),
					   GetSQLValueString($latitude, "text"),
					   GetSQLValueString($longitude, "text"),
					   GetSQLValueString(ucfirst($Active), "text"),
					   GetSQLValueString($venue, "text"),
					   GetSQLValueString($regdate, "date"));

// echo $insertq; exit;

$rsInsert=mysqli_query($piercecty,$insertq) or die(mysqli_error($piercecty));
}

echo 'done';
?>
