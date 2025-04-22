<?php require_once('../../Connections/piercecty.php'); 
function tabledisplay($query){
	global $piercecty;
	echo $query."<br /><table><tr><td>Field</td><td>Key or Default</td></tr>";
	$rsQuery=mysqli_query($piercecty, $query);
	while($row = mysqli_fetch_assoc($rsQuery)) {
		echo '<tr><td>'.$row['Field'].'</td><td>'.$row['Default'].'</td></tr>';
	}
	echo '</table><br />';
}
echo "Pierce County Gleaning Project database tables specifications<br /><br />";
$query="describe crops"; tabledisplay($query);
$query="describe custom2";tabledisplay($query);
$query="describe harvests";tabledisplay($query);
$query="describe hits";tabledisplay($query);
$query="describe loginlog";tabledisplay($query);
$query="describe mailarchive";tabledisplay($query);
$query="describe maillist";tabledisplay($query);
$query="describe pickers";tabledisplay($query);
$query="describe rosters";tabledisplay($query);
$query="describe sites";tabledisplay($query);
$query="describe spots";tabledisplay($query);
$query="describe sqllibrary";tabledisplay($query);
$query="describe store";tabledisplay($query);
$query="describe surveyowner";tabledisplay($query);
$query="describe temp";tabledisplay($query);
$query="describe users";tabledisplay($query);
?>

