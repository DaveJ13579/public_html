<?php
if (!isset($_SESSION)) { session_start(); }
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
$pow=0; // set up $pow as variable for access level of types of commands.
if($_SESSION['MM_UserGroup']=='all')  $pow=2;
if($_SESSION['MM_UserGroup']=='change')  $pow=1;

// initialize and get switches and id number
$list= ""; $library=""; $new="";$save="";$delete="";$delsure="";$ovesure="";$id=""; $title=""; $sqlquery=""; $idget=""; $filter=''; $terms=''; $filterquery=''; $keyword='';
if(isset($_GET['id'])) $idget = $_GET['id'];
if(isset($_GET['terms'])) $terms = $_GET['terms'];
if(isset($_POST['id']) && ($_POST['id']<>"")) $id = $_POST['id'];
if(isset($_POST['title']) && ($_POST['title']<>"")) $title = stripslashes($_POST['title']);
if(isset($_POST['sqlquery']) && ($_POST['sqlquery']<>"")) $sqlquery = stripslashes($_POST['sqlquery']);
if(isset($_POST['list'])) $list  = "yes";
if(isset($_POST['library'])) $library  = "yes";
if(isset($_POST['new'])) $new  = "yes";
if(isset($_POST['save'])) $save  = "yes";
if(isset($_POST['delete'])) $delete  = "yes";
if(isset($_POST['delsure'])) $delsure  = "yes";
if(isset($_POST['ovesure'])) $ovesure  = "yes";
if(isset($_POST['filter'])) $filter  = "yes";
if(isset($_POST['terms'])) $terms  = $_POST['terms'];

// filter illegal $sqlqueries and queries  from users with less than 'all' access
if((stripos($sqlquery,'users') || stripos($sqlquery,'pageslog') || stripos($sqlquery,'loginlog')) && $pow<2) $sqlquery='Not authorized to that table';
if((substr($sqlquery,0,7)<>'select ' && 
	substr($sqlquery,0,2)<>'s ') &&  
   	substr($sqlquery,0,5)<>'copy ' && 
   	substr($sqlquery,0,7)<>'delete ' && 
   	substr($sqlquery,0,7)<>'update ' && 
	$sqlquery<>'') $sqlquery='Illegal operation';
if((substr($sqlquery,0,7)=='delete ' || substr($sqlquery,0,7)=='update ') && $pow<2) $sqlquery='Illegal operation';

// allows use of 's ' instead of 'select' so that table joining 'wheres' are inserted automatically. 
if(substr($sqlquery,0,2)=='s ')  { // need to insert required table join terms 

$fields = array( // 3-D array of table joining terms that has entries for the four main tables (pickers, sites, harvests, rosters) that have interlocking keys. 
	'sites' => array(
		'harvests' => array("ID_site", "ID_site")),
	'pickers' => array(
		'harvests' => array("ID_picker", "ID_leader"),
		'rosters' => array("ID_picker", "ID_picker")),
	'harvests' => array(
		'sites' => array("ID_site", "ID_site"),
		'pickers' => array("ID_leader", "ID_picker"),
		'rosters' => array("ID_harvest", "ID_harvest")),
	'rosters' => array(
		'pickers' => array("ID_picker", "ID_picker"),
		'harvests' => array("ID_harvest", "ID_harvest"))
	);

$trunced=substr($sqlquery, stripos($sqlquery,'from ')+5, stripos($sqlquery,'where ')-stripos($sqlquery,'from ')-6).','; // trunced = string of table names only (plus a comma)

for ($ct=0; $ct<=3 ;++$ct) { // put table names used in the query into an array
	if(strlen($trunced)==0) break;
	$name[$ct]=substr($trunced,0,stripos($trunced,','));
	$trunced=substr($trunced,stripos($trunced,',')+2);
	}
$insert='';
for ($i=0; $i<count($name); ++$i) { //cycle through table name combinations and construct insert clause of table joining terms
	for ($j=$i+1; $j<count($name); ++$j) {
	if(isset($fields["$name[$i]"]["$name[$j]"][0])) 
		$insert.= ' '.$name[$i].'.'.$fields["$name[$i]"]["$name[$j]"][0].'='.$name[$j].'.'.$fields["$name[$i]"]["$name[$j]"][1]." and ";
	}}

// reconstruct sqlquery by inserting the table joining 'where' clauses
$insertpt=stripos($sqlquery, 'where')+6;
$sqlquery=substr($sqlquery,0, $insertpt).$insert.substr($sqlquery,$insertpt);
$sqlquery='select'.substr($sqlquery,1);
// echo $sqlquery;
} // done inserting table join terms

// if id number is in get or post, retrieve the search string for processing 
if($idget<>"") {
	$id=$idget;
	$query="select * from sqllibrary where id=$id";
	$rsQuery = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
	$row = mysqli_fetch_assoc($rsQuery);
	$sqlquery=stripslashes($row['sqlquery']);
	$title=stripslashes($row['title']);
	}
$sqldisplay=$sqlquery;
// decode and reconstruct sqlquery for report input formatting	
if($sqlquery<>'' && strpos($sqlquery,'|')) {
// extract input variables, input labels, input default values
// place these in 2D input array $inputs[]
// e.g. select fname from pickers where ID_picker<|ID_picker,Picker number,20| and zip=|zip,zip code,97301|
// 	$inputs[0,0]='ID_picker', $inputs[0,1]='Picker number', $inputs[0,2]='1'
// $inputs[1,0]='zip', $inputs[1,1]='zip code', $inputs[1,2]='97301'

// initialize variables
$inputct=0; $inputs=array(); $pipepos1=array(); $pipepos2=array(); $commapos1=array();$commapos2=array();$another='yes';
// scan $sqlquery for locations of 2 pipes and 2 commas and extract $inputs (name, label, value) for each input group between pipe pairs
while($another=='yes') {
// extract pipe block substring
if($inputct==0) {$startscan=0;} else {$startscan=$pipepos2[$inputct-1];}
$pipepos1[$inputct]=strpos($sqlquery,'|',$startscan+1);
$pipepos2[$inputct]=strpos($sqlquery,'|',$pipepos1[$inputct]+1);
$commapos1[$inputct]=strpos($sqlquery,',',$pipepos1[$inputct]+1);
$commapos2[$inputct]=strpos($sqlquery,',',$commapos1[$inputct]+1);
// extract input  variable name
$inputs[$inputct][0]=substr($sqlquery,$pipepos1[$inputct]+1,$commapos1[$inputct]-$pipepos1[$inputct]-1);
// extract input label
$inputs[$inputct][1]=substr($sqlquery,$commapos1[$inputct]+1,$commapos2[$inputct]-$commapos1[$inputct]-1);
// extract input default value
$inputs[$inputct][2]=substr($sqlquery,$commapos2[$inputct]+1,$pipepos2[$inputct]-$commapos2[$inputct]-1);
// any more inputs?
if(strpos($sqlquery,'|',$pipepos2[$inputct]+1)) { $another='yes'; $inputct=$inputct+1; 
} else { $another='no' ;}
} // end of while another = 'yes'
// use inputs array to check for posted variables and replace in sqldisplay and sqlquery going from right to left (so pipe and comma position values do not change
for($x=$inputct;$x>=0;$x--) {
$postname=$inputs[$x][0];
if(isset($_POST["$postname"]) and ($_POST["$postname"]<>'')) { 
	$sqldisplay=substr_replace($sqldisplay,"'".$_POST["$postname"]."'",$commapos2[$x]+1,$pipepos2[$x]-$commapos2[$x]-1);
	$sqldisplay=stripslashes($sqldisplay);
	$sqlquery=substr_replace($sqlquery,"'".$_POST["$postname"]."'",$pipepos1[$x],$pipepos2[$x]-$pipepos1[$x]+1);
	$sqlquery=stripslashes($sqlquery);
	} else { // no posted value so replace pipes with values
	$sqlquery=substr_replace($sqlquery,$inputs[$x][2],$pipepos1[$x],$pipepos2[$x]-$pipepos1[$x]+1);
	$sqlquery=stripslashes($sqlquery);
	}
} // end of input fields to replace
} // end of decode and reconstruct
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SQL</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
    <style type="text/css">
<!--
a:link { COLOR: #000000; text-decoration: none; }
a:visited { COLOR: #000000; 	text-decoration: none; }
a:hover { COLOR: #6688bb; 	text-decoration: underline; }
a:active { color: #000000; }
#rsearchresults tr td {
	padding-right: 10px;
	padding-left: 10px;
}
#results {
	overflow: scroll;
}
-->
    </style>
</head>

<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php');?>
  <div style="background-color:rgb(143,193,35); text-align:center;" >
<strong>REFERENCE LINKS </strong>| <a href="http://dev.mysql.com/doc/refman/5.1/en/string-functions.html" target="_blank">Mysql Strings</a> | <a href="http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html" target="_blank">Mysql Dates</a> | </div>
    <div id="mainContent">
    <p><strong>Report generator:</strong> See Page Help link above for instructions</p>

<form id="search" name="search" method="post" action="Reports.php">
<input name="title" type="text" value="<?php echo $title; ?>" size="150" maxlength="150" />
<textarea name="sqlquery" cols="120" rows="4" ><?php echo $sqldisplay; ?></textarea>
<table border="1" cellspacing="1" cellpadding="5">
<tr>
<?php // cycle through $inputs[] to display input fields for form 
if(isset($inputct)) {for($x=0;$x<=$inputct; $x++) { 
	
if(substr($inputs[$x][1],0,3)=='dd-') { // make a dropdown list 
$table=substr($inputs[$x][1],3);
$field=$inputs[$x][0];
$dropquery="select $field from $table order by $field";
$query2 = mysqli_query($piercecty, $dropquery) or die(mysqli_error($piercecty));
$ct=0;
while($row2 = mysqli_fetch_assoc($query2)) { $temparr[$ct]=$row2["$field"]; $ct++;}
$temparr=array_values(array_unique($temparr));
$postname=$inputs[$x][0];
$selectvalue= (isset($_POST["$postname"]) and ($_POST["$postname"]<>''))  ? str_replace("'", "", stripslashes($_POST["$postname"])) : str_replace("'", "", $inputs[$x][2]);
?>
<td><select name="<?php echo $field;?>">
<?php for($ct=0;$ct<sizeof($temparr);$ct++) { ?>
<option value="<?php echo $temparr[$ct];?>" <?php if($temparr[$ct]==$selectvalue) echo 'selected="selected"';?>><?php echo $temparr[$ct];?></option>
<?php } // end of for temparr
?></select></td><?php 

} else {	 // make a text field input

$postname=$inputs[$x][0]; // the name of the input field
?><td><?php echo $inputs[$x][1];?><input name="<?php echo $inputs[$x][0];?>" type="text" value="<?php 
// if there are posted values, put them in the field, else use the default values
if(isset($_POST["$postname"]) and ($_POST["$postname"]<>'')) {echo str_replace("'", "", stripslashes($_POST["$postname"]));} else {echo str_replace("'", "", $inputs[$x][2]);}?>" size="20"/></td><?php } // end of make a text input
} // end of cycle through inputs
} // end of inputs are set
?>
</tr></table>

<table border="1" cellspacing="1" cellpadding="5">
  <tr>
    <td><input type="submit" name="library"  value="Reports library" /></td>
    <td><input type="submit" name="list"  value="Show this report" /></td>
	 <?php if($pow>0) { ?> 
    <td><input type="submit" name="new"  value="Save as new report" /></td>
    <td><input type="submit" name="save"  value="Save changes to report" /></td><?php } ?>
	  <?php if($pow>1) { ?> 
	<td><input type="submit" name="delete"  value="Delete" /></td> <?php } ?>
    <td><input name="terms" type="text" size="15" value="<?php echo $terms; ?>" maxlength="40" /></td>
    <td><input type="submit" name="filter"  value="Search" /></td>
  </tr>
</table>
<input type="hidden" name="id"  value="<?php echo $id ?>" />
</form>

<?php 
// 9 blocks for 9 cases
// delete
if(($delete=="yes") && ($id<>"")) { ?>
 <table border="1" cellpadding="2"><tr><td>
  <form action="" method="post">
    <input type="submit" name="delsure"  value="Are you sure?" />
    <input type="hidden" name="id"  value="<?php echo $id; ?>" />
  </form> 
  </td></tr></table> <?php
} // end of delete==yes

// delsure
if(($delsure=="yes") && ($id<>"")) { 
 $query="delete from sqllibrary where id=$id";
 $rsQuery = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty)); ?>
 <table  border="1" cellpadding="2">
	<tr><td>Report deleted</td></tr>
 </table>
<?php 
} // end of delsure==yes

// save
if(($save=="yes") && ($id<>"")) { ?>

 <table border="1" cellpadding="2"><tr><td>
  <form action="" method="post">
    <input type="submit" name="ovesure"  value="Overwrite?" />
    <input type="hidden" name="id"  value="<?php echo $id; ?>" />
    <input type="hidden" name="title"  value="<?php echo $title; ?>" />
    <input type="hidden" name="sqlquery"  value="<?php echo $sqldisplay; ?>" />
  </form> 
  </td></tr></table> <?php
} // end of save==yes

// ovesure -  if are sure about overwriting a report
if(($ovesure=="yes") && ($id<>"")) { 
 $query= sprintf("update sqllibrary set title=%s, sqlquery=%s where id=%s", 
				 GetSQLValueString($title,"text"),
				 GetSQLValueString($sqldisplay,"text"),
				 GetSQLValueString($id,"int"));
 $rsQuery = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty)); ?>
 <table border="1" cellpadding="2">
	<tr><td>Report updated</td></tr>
 </table>
<?php 
} // end of ovesure==yes

//new
if(($new=="yes") && ($title<>"") && ($sqlquery<>"")) { 
 $query= sprintf("insert into sqllibrary (title, sqlquery) values (%s, %s)", 
				 GetSQLValueString($title,"text"),
				 GetSQLValueString($sqldisplay,"text"));
  $rsQuery = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty)); ?>
 <table border="1" cellpadding="2">
	<tr><td>Report added to library</td></tr>
 </table>
<?php 
} // end of new==yes

// search for one or two terms
if($terms<>'') { 
	// find comma
	$comma=stripos($terms,',');
	// compile filterquery based on comma or not
	if($comma===false) 
	{ $filterquery="select * from sqllibrary where locate('$terms', title)"; }
	else { 
	$term1=substr($terms, 0, $comma);
	$term2=substr($terms,$comma+1);
	$filterquery="select * from sqllibrary where (locate('$term1', title) or locate('$term2', title))";
	} 
} // end of search for terms

// execute sqlqueries - copy, delete, update, select

// copy records
if(($list=="yes") && (strtolower(substr($sqlquery,0,4))=="copy")) {
$switch='copy'; 
// parse the fromtable. Format is  'copy fromtable where fromfield=xxx'
// find space after copy xxx
$space2=strpos($sqlquery,' ',5);
$fromtable=substr($sqlquery,5,$space2-5);
// parse the fromfield
// find space3
$space3=strpos($sqlquery, ' ',$space2+1);
$equals=strpos($sqlquery, '=', $space3);
$fieldlen=$equals-$space3-1;
$fromfield=substr($sqlquery,$space3+1,$fieldlen);
// parse id number
$ID=substr($sqlquery,$equals+1);
$query="drop temporary table if exists tmp";
$result = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$query="create temporary table tmp select * from $fromtable where $fromfield=$ID";
$result = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$query="update tmp set $fromfield=NULL";
$result = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$query="insert into $fromtable select * from tmp";
$result = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
echo $switch.": ".mysqli_affected_rows($piercecty)." records affected"; 
} // end of copy a record

// delete  a record
if(($list=="yes") &&  (substr($sqlquery,0,6)=="delete") && ($pow>1)) {
$result4 = mysqli_query($piercecty, $sqlquery) or die(mysqli_error($piercecty));
echo mysqli_affected_rows($piercecty)." records affected"; 
} // end of delete section

// update  a record
if(($list=="yes") &&  (substr($sqlquery,0,6)=="update") && ($pow>0)) {
$result45 = mysqli_query($piercecty, $sqlquery) or die(mysqli_error($piercecty));
echo mysqli_affected_rows($piercecty)." records affected"; 
} // end of update section

// list report
if(($list=="yes") &&  (substr($sqlquery,0,6)=="select")) {
	
// calc the record set

$query = mysqli_query($piercecty, $sqlquery) or die(mysqli_error($piercecty));
$row = mysqli_fetch_array($query,  MYSQLI_BOTH);
$numrows=mysqli_num_rows($query);
if($numrows > 0) {$keys = array_keys($row); }
echo "Number of records: ".$numrows;
	
	if($numrows>0) { ?>
<div id="results">
	<?php
	$keyplus=stripos($title,'+');
	$displaytitle= $keyplus ? substr($title,0,$keyplus) : $title;
		// ....cycle through inputs[x][] to build addon string for display title
	if(isset($inputct)) {
		for($x=0;$x<=$inputct;$x++) { $displaytitle.=' ['.str_replace("'","",$inputs[$x][2]).'] '; } 
		}
	?>
    <table  border="1" cellpadding-left="4" cellspacing="2" id="rsearchresults">
    <tr><th colspan="<?php echo count($keys); ?>"><?php echo $displaytitle; ?></th></tr>
    <tr><td colspan="<?php echo count($keys); ?>">&nbsp;</td></tr>

	<?php echo '<tr>';
	for($i=1;$i<count($keys);$i=$i+2) { 
		 echo '<th>'.$keys[$i].'</th>';} 
		 echo '</tr>';
		 
		$ct=1; do { 
			echo	 '<tr>';
           for($i=0;$i<(count($row)/2);$i++) { 
		   		echo '<td>'.$row[$i].'</td>'; }
	    echo '</tr>';
        $ct++; } while ($row = mysqli_fetch_array($query,  MYSQLI_BOTH)); 
	} ?>     
    </table>
</div>
<?php
} // end of if list==yes

// library

// find all keywords(categories)  in all report titles, then remove duplicates and order the array
			$i=0; $keywords[0]='';
			$keyquery="select id, title from sqllibrary";
			$rsKey=mysqli_query($piercecty,$keyquery);
			while ($keyrow=mysqli_fetch_assoc($rsKey)) {
			$titlekeys=$keyrow['title'];
				while($plus=stripos($titlekeys,'+')) {
					$titlekeys=substr($titlekeys,$plus+1);
					$plus2=stripos($titlekeys,'+');
					$keywords[$i]=substr($titlekeys,0,$plus2);
					++$i;
					$titlekeys=substr($titlekeys,$plus2+1);
					}
			}
			$keywords=array_unique($keywords);
			$keywords=array_values($keywords);
			sort($keywords);
			$i=count($keywords);

echo "<br /><strong>Reports Library:</strong> Click on report titles below to load, then click on 'Show this report'<br /><br />";
$query="select * from sqllibrary where 1=1";
if($library=='yes')  $terms='';
if($filterquery<>'' && $library<>'yes') $query=$filterquery; // if search terms  used

echo '<table border="1" cellspacing="2" cellpadding="5">';
if($i) {  // there are keywords so do  table rows 
for($j=0 ; $j<$i ; ++$j) { // cycle through keywords 
$subkey=$keywords[$j];

if($j==0) { echo '<tr valign="top">';} 
elseif($j%3==0 && $j>0) { echo '</tr><tr valign="top">';}
echo '<td width="25%"><strong>'.$subkey.'</strong><br />';

$subquery=$query." and locate('+$subkey+', title)";
$rsQuery = mysqli_query($piercecty, $subquery) or die(mysqli_error($piercecty)); 
while ($row= mysqli_fetch_assoc($rsQuery)) {
	$keyplus=stripos($row['title'],'+');
	$displaytitle= $keyplus ? substr($row['title'],0,$keyplus) : $row['title'];
 	?>
	<a href="Reports.php?id=<?php echo $row['id'];?>&terms=<?php echo "$terms";?>"><?php echo $displaytitle;?></a><br />
	<?php } 
echo '</td>';
} // no more key words
echo '</tr>';
} // end of do table rows
echo '</table>';

// all the rest of the library that does not have keywords
$query="select * from sqllibrary order by id desc";
if($library=='yes')  $terms='';
if($filterquery<>'' && $library<>'yes') $query=$filterquery; // if filter terms used
$rsQuery = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty)); 
while ($row= mysqli_fetch_assoc($rsQuery)) { 
if(!stripos($row['title'],'+')) { ?>
<a href="Reports.php?id=<?php echo $row['id'];?>&terms=<?php echo "$terms";?>"><?php echo $row['title'];?></a><br />
<?php } // if not have a keyword
} // all rows
?> 

</div>
</div>
</body>
</html>
