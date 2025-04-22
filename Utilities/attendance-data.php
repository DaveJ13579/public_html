<?php
require_once('../Connections/piercecty.php'); 
if (!isset($_SESSION)) {  session_start(); }
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../includes/sqlcleaner.php');
if(!isset($_POST['sort1']))

{  // no POST sort variables, so compile the attend array and insert into attend table
	// This happens only the first time when loaded from an external link. 
	// Subsequent passes just resort and display based on the same compiled attend table.

// default/initial sort values since coming from external link
$sort1="signup";
$sort2="name";
$direct1="DESC";
$direct2="ASC";
$year='2013'; 

$sorter="UPDATE store set value='name' where name='sort1'";
$rsSorter = mysqli_query( $piercecty, $sorter);
$sorter="UPDATE store set value='regdate' where name='sort2'";
$rsSorter = mysqli_query( $piercecty, $sorter);

} // done compiling table because no sort1 key set 

// retrieve sort keys from POST because sort1 was set
else { 
if(isset($_POST['sort1'])) {$sort1=$_POST['sort1'];} else {$sort1="name";}
if(isset($_POST['sort2'])) {$sort2=$_POST['sort2'];} else {$sort2=$sort1;}
if(isset($_POST['direct1'])) {$direct1=$_POST['direct1'];} else {$direct1="ASC";}
if(isset($_POST['direct2'])) {$direct2=$_POST['direct2'];} else {$direct2=$direct1;}
if(isset($_POST['year'])) {$year=$_POST['year'];} else {$year=date('Y');}
}

$query = "SELECT ID_picker, lname, fname, regdate FROM pickers order by lname";
$rsQuery = mysqli_query( $piercecty, $query);
$row_query = mysqli_fetch_assoc($rsQuery);
$numrows = mysqli_num_rows($rsQuery); 

$maxquery = "SELECT max(ID_picker) as max from pickers"; // find highest ID_picker number
$rsMax = mysqli_query( $piercecty, $maxquery);
$row_max = mysqli_fetch_assoc($rsMax);
$max=$row_max['max'];

for ($i = 0; $i <= $max; $i++ ) { //Zero the array up to the highest picker number
  	$attend[$i]['ID']=0;
	$attend[$i]['name']="";
	$attend[$i]['reg']=0;
	$attend[$i]['signup']=0;
	$attend[$i]['harvested']=0;
	$attend[$i]['intake']=0;
	$attend[$i]['leader']=0;
	$attend[$i]['cancel']=0;
	$attend[$i]['absent']=0;
	$attend[$i]['added']=0;
	$attend[$i]['shadowed']=0;
	$attend[$i]['waiting']=0;
	$attend[$i]['unregistered']=0;
	$attend[$i]['percent']=0;
	
} // end of zero out the array

do { // insert values from picker table for ID, name, regdate
	$ID=$row_query['ID_picker'];
	$attend[$ID]['ID']=$ID;
	$attend[$ID]['name']=$row_query['lname'].", ".$row_query['fname'];
	$attend[$ID]['reg']=$row_query['regdate'];
} while ($row_query = mysqli_fetch_assoc($rsQuery)); 

$roster="SELECT ID_rosters, ID_picker, lname, status from rosters where year(regdate)=$year";
$rsRoster = mysqli_query( $piercecty, $roster);
$row_roster = mysqli_fetch_assoc($rsRoster);

do { // cycle through rosters incrementing attendance categories

	$ID=$row_roster['ID_picker'];
	$signincr=$attend[$ID]['signup']+1;
	$attend[$ID]['signup']=$signincr;
	$status=$row_roster['status'];
	$incr=$attend[$ID][$status]+1;
	$attend[$ID][$status]=$incr;

} while ($row_roster = mysqli_fetch_assoc($rsRoster)); 

// calculate percent
for ($i = 0; $i <= $max; $i++ ) {
	if($attend[$i]['signup'] > 0) { 
	$attend[$i]['percent']=($attend[$i]['signup']-$attend[$i]['cancel']-$attend[$i]['absent'])*100/$attend[$i]['signup'];
	}
}

// create the attend table
$createquery = "CREATE TEMPORARY TABLE IF NOT EXISTS attend (ID int, name char(30), regdate datetime, signup int, harvested int, intake int, leader int, cancel int, absent int, added int, shadowed int, waiting int, unregistered int, percent int)";
$rsCreate = mysqli_query( $piercecty, $createquery);


// insert the attend array into the attend table
for ($i = 0; $i <= $max; $i++ ) { 

if($attend[$i]['ID']>0) {

$t1=$attend[$i]['ID'];
$t2=$attend[$i]['name'];
$t2 = preg_replace("/[^a-zA-Z0-9s]/", " ", $t2);
$t3=$attend[$i]['reg'];
$t4=$attend[$i]['signup'];
$t5=$attend[$i]['harvested'];
$t6=$attend[$i]['intake'];
$t7=$attend[$i]['leader'];
$t8=$attend[$i]['cancel'];
$t9=$attend[$i]['absent'];
$t10=$attend[$i]['added'];
$t11=$attend[$i]['shadowed'];
$t12=$attend[$i]['waiting'];
$t13=$attend[$i]['unregistered'];
$t14=$attend[$i]['percent'];

$build = "INSERT into attend(ID, name, regdate, signup, harvested, intake, leader, cancel, absent, added, shadowed, waiting, unregistered, percent)
   VALUES($t1,'$t2','$t3',$t4,$t5,$t6,$t7,$t8,$t9,$t10,$t11,$t12,$t13,$t14)";
   
$rsBuild = mysqli_query( $piercecty, $build);

} // end range for name <> ""
} // done insert attend array into attend table

// clear out the select diplay array
for($i=1; $i<3; ++$i) {
$select[$i]['name']="";
$select[$i]['regdate']="";
$select[$i]['signup']="";
$select[$i]['harvested']="";
$select[$i]['intake']="";
$select[$i]['leader']="";
$select[$i]['cancel']="";
$select[$i]['absent']="";
$select[$i]['added']="";
$select[$i]['percent']="";
$select[$i]['ASC']="";
$select[$i]['DESC']="";
}

// insert selected fields into display array
$select[1][$sort1]='checked="checked"';
$select[1][$direct1]='checked="checked"';
$select[2][$sort2]='checked="checked"';
$select[2][$direct2]='checked="checked"';


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Attendance</title>
<style type="text/css">
<!--
-->
</style>
<link href="../database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2 class="SH"><strong>Attendance table</strong>    </h2>
    <p>Click table headings to select first and second sort columns. Click picker names for details and harvest history.</p>
    <table width="1240" border="1">
    <form id="sorter" name="sort" method="post" action="attendance-data.php">
       <tr align="center">
       	  <td><strong>Sort #1</strong></td>
          <td><label><input type="radio" name="sort1" value="name" id="sort1_0" <?php echo $select[1]['name'];?>/>Name</label></td>
          <td><label><input type="radio" name="sort1" value="regdate" id="sort1_1" <?php echo $select[1]['regdate'];?> />Registration</label></td>
          <td><label><input type="radio" name="sort1" value="signup" id="sort1_2" <?php echo $select[1]['signup'];?>/>Signups</label></td>
          <td><label><input type="radio" name="sort1" value="harvested" id="sort1_3" <?php echo $select[1]['harvested'];?>/>Harvested</label></td>
          <td><label><input type="radio" name="sort1" value="intake" id="sort1_4" <?php echo $select[1]['intake'];?>/>Assistant</label></td>
          <td><label><input type="radio" name="sort1" value="leader" id="sort1_5" <?php echo $select[1]['leader'];?>/>Leader</label></td>
          <td><label><input type="radio" name="sort1" value="cancel" id="sort1_6" <?php echo $select[1]['cancel'];?>/>Cancel</label></td>
          <td><label><input type="radio" name="sort1" value="absent" id="sort1_7" <?php echo $select[1]['absent'];?>/>Absent</label></td>
          <td><label><input type="radio" name="sort1" value="added" id="sort1_9" <?php echo $select[1]['added'];?>/>Added</label></td>
          <td><label><input type="radio" name="sort1" value="percent" id="sort1_8" <?php echo $select[1]['percent'];?>/>Percent</label></td>
          <th><label><input type="radio" name="direct1" value="ASC" id="direct1_0" <?php echo $select[1]['ASC'];?>/>Up</label></th>
          <th><label><input type="radio" name="direct1" value="DESC" id="direct1_1" <?php echo $select[1]['DESC'];?>/>Down</label></th>
        </tr>
        <tr align="center">
       	  <td><strong>Sort #2</strong></td>
          <td><label><input type="radio" name="sort2" value="name" id="sort2_0" <?php echo $select[2]['name'];?>/>Name</label></td>
          <td><label><input type="radio" name="sort2" value="regdate" id="sort2_1" <?php echo $select[2]['regdate'];?> />Registration</label></td>
          <td><label><input type="radio" name="sort2" value="signup" id="sort2_2" <?php echo $select[2]['signup'];?>/>Signups</label></td>
          <td><label><input type="radio" name="sort2" value="harvested" id="sort2_3" <?php echo $select[2]['harvested'];?>/>Harvested</label></td>
          <td><label><input type="radio" name="sort2" value="intake" id="sort2_4" <?php echo $select[2]['intake'];?>/>Assistant</label></td>
          <td><label><input type="radio" name="sort2" value="leader" id="sort2_5" <?php echo $select[2]['leader'];?>/>Leader</label></td>
          <td><label><input type="radio" name="sort2" value="cancel" id="sort2_6" <?php echo $select[2]['cancel'];?>/>Cancel</label></td>
          <td><label><input type="radio" name="sort2" value="absent" id="sort2_7" <?php echo $select[2]['absent'];?>/>Absent</label></td>
          <td><label><input type="radio" name="sort2" value="absent" id="sort2_9" <?php echo $select[2]['added'];?>/>Added</label></td>
          <td><label><input type="radio" name="sort2" value="percent" id="sort2_8" <?php echo $select[2]['percent'];?>/>Percent</label></td>
          <th><label><input type="radio" name="direct2" value="ASC" id="direct2_0" <?php echo $select[2]['ASC'];?>/>Up</label></th>
          <th><label><input type="radio" name="direct2" value="DESC" id="direct2_1" <?php echo $select[2]['DESC'];?>/>Down</label></th>
        </tr>
      <p>&nbsp;</p>

      <tr class="center_cell">
		<th>Row count</th>
        <th>Name</th>
        <th>Registration date</th>
        <th>Total signups</th>
        <th>Harvested</th>
        <th>Assistant</th>
        <th>Leader</th>
        <th>Cancel</th>
        <th>Absent</th>
        <th>Added</th>
        <th>Percent</th>
        <th><select name="year"/>
        <?php for($yr=date('Y'); $yr>=2010;--$yr) { ?>
        <option value="<?php echo $yr; ?>" <?php if($year==$yr) echo "selected"; ?>><?php echo $yr; ?></option>
        <?php } ?>
         </th>
        <th><input type="submit" name="submit" id="submit" value="Submit" /></th>
      </tr>
      </form>
      <?php
		$query_attend = "SELECT ID, name, regdate, signup, harvested, intake, leader, cancel, absent, added, shadowed, unregistered, percent FROM attend where signup>0 ORDER BY $sort1 $direct1, $sort2 $direct2";
		$rsAttend = mysqli_query( $piercecty, $query_attend);
		$row_attend = mysqli_fetch_assoc($rsAttend);
		
		$rows=0;		
		do { $rows++; ?>
      <tr>
		<td align="center"><?php echo $rows; ?> </td> 
        <td><a href="../voldetail.php?voltemp=<?php echo $row_attend['ID']?>"><?php echo $row_attend['name']; ?></a></td>
        <td align="center"><?php echo substr($row_attend['regdate'],1,10); ?></td>
        <td align="center"><?php echo $row_attend['signup']; ?></td>
        <td align="center"><?php echo $row_attend['harvested']; ?></td>
        <td align="center"><?php echo $row_attend['intake']; ?></td>
        <td align="center"><?php echo $row_attend['leader']; ?></td>
        <td align="center"><?php echo $row_attend['cancel']; ?></td>
        <td align="center"><?php echo $row_attend['absent']; ?></td>
        <td align="center"><?php echo $row_attend['added']; ?></td>
        <td align="center"><?php echo round($row_attend['percent'],0); ?></td>
         <?php } while ($row_attend = mysqli_fetch_assoc($rsAttend)); ?>
      </tr>
    </table>

    </div><!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
    <br class="clearfloat" />
    <!-- end #container --></div>
</body>
</html>
