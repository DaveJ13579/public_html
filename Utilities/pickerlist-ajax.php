<?php require_once('../Connections/piercecty.php'); 


if (!isset($_GET['ID'])) { echo "No picker ID sent"; } else
{ 
$ID=$_GET['ID'];
$query="select * from pickers where ID_picker=$ID";
$rsPicker=mysqli_query($piercecty, $query);
$r=mysqli_fetch_assoc($rsPicker);

$t= "<br /><br /><strong>".$r['fname']." ".$r['lname']."</strong><br />"; 
$t.="<br />".$r['email'];
$t.="<br />".$r['phone'];
$t.="<br /><br /><strong>Zip code: </strong>".$r['zip'];
$t.="<br /><strong>Emergency contact: </strong>".$r['emerg'];
$t.="<br /><strong>Emergency number: </strong>".$r['ephone'];
$t.="<br /><br /><strong>Most recent contact: </strong>".substr($r['contactdate'],0,10);
$t.="<br /><br /><strong>Attendance: </strong><br />";
echo $t;

$query2="select count(status) as count, status from rosters where ID_picker=$ID group by status";
$rsAtt=mysqli_query($piercecty, $query2);
$tot=0; $att=0;

echo "<table cellpadding='5' border='2' >";
while ($row=mysqli_fetch_assoc($rsAtt)) { 
echo "<tr><td>";
	$tot+= $row['count'];
	if($row['status']<>'absent' and $row['status']<>'cancel') $att+=$row['count'];
	echo "<strong>".$row['status']."</strong>:</td><td>".$row['count']."</td></tr>"; }
	echo "<tr><td><strong>attended:</strong></td><td>".$att."</td></tr>";
	echo "<tr><td><strong>signups:</strong></td><td>".$tot."</td></tr></table>";
	
echo "<br /><br >";
$link='<a href="pickerupdate.php?temp1='.$ID.' " target="_blank">Update details</a>';
echo $link;
echo "<br />";
$link2='<a href="voldetail.php?voltemp='.$ID.' " target="_blank">View more details</a>';
echo $link2;
((mysqli_free_result($rsPicker) || (is_object($rsPicker) && (get_class($rsPicker) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsAtt) || (is_object($rsAtt) && (get_class($rsAtt) == "mysqli_result"))) ? true : false);}
?>
