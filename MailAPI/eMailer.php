<?php
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php'); 

$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../includes/smtpmailer.inc.php');

// email function - can handle attachments
function sendmail($email, $subject,$fromemail,$message,$attachpath)  {

if($attachpath=='') { // no attachment
smtpmail($email, $subject, $message, $fromemail);

} // end of no attachment
else { // has attachment
$filename=basename($attachpath);
$ext=substr(strrchr($filename,'.'),1);

switch($ext) { // determine Content-Type for header
	case 'jpg': $ctype='image/jpg'; break;
	case 'pdf': $ctype='application/pdf'; break;
	case 'doc': $ctype='application/doc'; break;
	case 'txt': $ctype='text/plain'; break;
	case 'rtf': $ctype='application/rtf'; break;
	default: $ctype='text/plain'; break;
	}
$attach= chunk_split(base64_encode(file_get_contents($attachpath)));
$boundary = md5( time() );
$headers = sprintf("From: %s\r\nReply-To: %s\r\nMIME-Version: 1.0\r\nContent-Type: multipart/related; boundary=\"%s\"",	$fromemail, $fromemail, $boundary);
$message = sprintf("--%s\r\nContent-Type: text/plain; Content-Transfer-Encoding: quoted-printable\n\n%s\n\n--%s\r\nContent-Type: %s; name=\"%s\"\nContent-ID: <%s>\nContent-Transfer-Encoding: base64\n\n%s\n\n--%s--\n", $boundary,$message,$boundary,$ctype,$filename, $filename, $attach,$boundary);
mail($email, $subject, $message, $headers);

} // end of has attachment
} // end of sendmail function

$errmsg='';
$bg="#ccc";

// from name and email is obtained from user session
$user=$_SESSION['MM_Username'];
  $query="select fname, lname, email from pickers, users where users.ID_user=pickers.ID_picker and user_name='$user'";
  $rsUser = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
  $row = mysqli_fetch_assoc($rsUser);
  $from=$row['fname']." ".$row['lname']." ".$row['email'];
  $fromemail=$row['email']; 
  if($fromemail=='') $fromemail='info@piercecountygleaningproject.org';
  $fromname=$row['fname']." ".$row['lname']; if($fromname==' ' or $fromname==' UNREGISTERED') $fromname='Pierce County Gleaning';
  
// find colors for buttons in the database.css  
$css=file_get_contents("../database.css");
$bgcolorpos=strpos($css,'#Navigation {background-color: #')+31;
$bgcolor=substr($css,$bgcolorpos,7);
$colorpos=strpos($css,'#Navigation {color: #')+20;
$color=substr($css,$colorpos,7);

$filename=''; // initialize as no attchment
$attachpath='';

// identify source of entry and form submits
// ------------ find from directory submit - three categories of individuals names; two categories of drop downs -----------------------------------------
// The drop downs, when selected are added directly to the session array $_SESSION['to']
//of addresses that will be placed in the email.
// The three sources of individual names use wild card match and first place the matches into $_SESSION['poss'] of 'possible' choices.
// the possible matches are then selected to be added either of the 'to' SESSION array.
// pickers
// lname
if(isset($_POST['indaddv'])) { // individuals to search
unset($poss);
$ctposs=0;
$errmsg="No names found";		
$name=''; $sfield='lname'; $long=1; // in case form is blank

if($_POST['lname']<>'') {
	$name=$_POST['lname'];
	$sfield='lname'; 
	$long=strlen(stripslashes($name));
	if($long==0) {$long=1; }
} // end of isset lname

elseif($_POST['fname']<>'') {
	$name=$_POST['fname'];
	$sfield='fname'; 
	$long=strlen(stripslashes($name));
	if($long==0) {$long=1; }
} // end of isset fname

// pickers
$query = "SELECT fname, lname, email FROM pickers WHERE left($sfield,'$long')='$name' and email<>'' ORDER BY fname, lname, email";
$rsName = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($rsName);
if($numrows>0) { $errmsg="Names found"; // build poss
while ($row=mysqli_fetch_assoc($rsName)) {
//put possible names into the 'possibles' array
$poss[$ctposs]['fname']=$row['fname'];
$poss[$ctposs]['lname']=$row['lname'];
$poss[$ctposs]['email']=$row['email'];
$ctposs=$ctposs+1;
} // end of while 
} // end of build poss

// maillist
$query = "SELECT fname, lname, email FROM maillist WHERE left($sfield,'$long')='$name' and email<>'' ORDER BY fname, lname, email";
$rsName = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($rsName);
if($numrows>0) { $errmsg="Names found"; // build poss
while ($row=mysqli_fetch_assoc($rsName)) { 
//put possible names into an array
$poss[$ctposs]['fname']=$row['fname'];
$poss[$ctposs]['lname']=$row['lname'];
$poss[$ctposs]['email']=$row['email'];
$ctposs=$ctposs+1;
} // end of while 
} // end of build poss

if($errmsg<>'Names found') { unset($_SESSION['poss']); unset($_SESSION['ctposs']); }
} //end of individuals to search

// end of find from individuals submit so move poss array to session['poss'] array
if(isset($poss)) { $_SESSION['poss']=$poss; $_SESSION['ctposs']=$ctposs; }

// add individuals from the $_SESSION['poss'] array to the $_SESSION['to'] array
if(isset($_POST['to'])) {
	$ind=$_POST['to'];
	$lbracket=strpos($ind,'['); $rbracket=strpos($ind,']');
	$lgth=$rbracket-$lbracket-1;
	$ind=substr($ind,$lbracket+1,$lgth);
	$x=0;
	if(isset($_SESSION['to'])) { $x=count($_SESSION['to']); }
	$_SESSION['to'][$x]['fname']=$_SESSION['poss'][$ind]['fname'];
	$_SESSION['to'][$x]['lname']=$_SESSION['poss'][$ind]['lname'];
	$_SESSION['to'][$x]['email']=$_SESSION['poss'][$ind]['email'];
	$errmsg="One name added to 'to' list";
}

// groups dropdown: 5 from pickers and one from users for the to  list
if(isset($_POST['groupto']) && $_POST['groups']<>'--select group--') { 
unset($_SESSION['poss']); unset($_SESSION['ctposs']); // clear individuals from possible array
if($_POST['groups']=='scout') { $query="select fname, lname, email from pickers where scout='Yes' AND email<>''"; }   
elseif($_POST['groups']=='leaders') { $query="select fname, lname, email from pickers where leader='Yes' AND email<>''"; }   
elseif($_POST['groups']=='users') { $query="select fname, lname, email from pickers, users where users.ID_user=pickers.ID_picker and users.ID_user<9990 and email<>''";}
elseif($_POST['groups']=='selects') { $query="select fname, lname, email from pickers  where selectteam='Yes' and email<>''";}
elseif($_POST['groups']=='branchldr') { $query="select fname, lname, email from pickers, branches where branches.ID_leader=pickers.ID_picker and email<>''";}

$rsGroup = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($rsGroup);
if($numrows==0) { $errmsg="None found"; $bg="Pink"; }
else { $errmsg=$numrows." names  added to 'to' list"; $bg="#CCC";
while ($row=mysqli_fetch_assoc($rsGroup)) {
	$x=0;
	if(isset($_SESSION['to'])) { $x=count($_SESSION['to']); }
	$_SESSION['to'][$x]['fname']=$row['fname'];
	$_SESSION['to'][$x]['lname']=$row['lname'];
	$_SESSION['to'][$x]['email']=$row['email'];
} // end of while group
} // end of else $numrows<>0
} // end of if isset groupto

// harvest rosters
// to list
if(isset($_POST['gleansto']) && $_POST['roster']<>'--select roster status--' && $_POST['glean']<>'') { 
unset($_SESSION['poss']); unset($_SESSION['ctposs']); // clear individuals from possible array
$harvnum=$_POST['glean'];
$status=$_POST['roster']; 
if($status=='roster') 
{$query="select pickers.fname, pickers.lname, email from rosters, pickers where rosters.ID_picker=pickers.ID_picker and ID_harvest=$harvnum and email<>''"; }

elseif($status=='attended') 
{$query="select pickers.fname, pickers.lname, email from rosters, pickers where rosters.ID_picker=pickers.ID_picker and ID_harvest=$harvnum and (status='harvested' or status='leader' or status='assisted') and email<>''"; }

elseif($status=='expected') 
{ $query="select pickers.fname, pickers.lname, email from rosters, pickers where rosters.ID_picker=pickers.ID_picker and ID_harvest=$harvnum and (status='signup' or status='leader' or status='assisted') and email<>''"; }

else { $query="select pickers.fname, pickers.lname, email from rosters, pickers where rosters.ID_picker=pickers.ID_picker and ID_harvest=$harvnum and status='$status' and email<>''"; }

$rsRoster = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($rsRoster);
if($numrows==0) { $errmsg="None found"; $bg="Pink"; }
else { $errmsg="$numrows roster entries added to 'to' list"; $bg="#CCC";
while ($row=mysqli_fetch_assoc($rsRoster)) {
	$x=0;
	if(isset($_SESSION['to'])) { $x=count($_SESSION['to']); }
	$_SESSION['to'][$x]['fname']=$row['fname'];
	$_SESSION['to'][$x]['lname']=$row['lname'];
	$_SESSION['to'][$x]['email']=$row['email'];
} // end of while roster
} // end of else $numrows<>0
}// end of isset gleansto

// process pasted list 
if(isset($_POST['pasteto']) and $_POST['pasted']<>'') { // process pasted list
unset($_SESSION['poss']); unset($_SESSION['ctposs']); // clear individuals from possible array
$pasted=$_POST['pasted'];
$pasted.=chr(13).chr(10); // add line feed and CR
$pastelen=strlen($pasted);

// Fields must be in order of fname,chr(9),lname,chr(9),email,chr(13)chr(10)
$x=0; // count of 'to' array
if(isset($_POST['pasteto']) and $_POST['pasteto']=='to') { $where='to'; }

$numrecords=substr_count($pasted,chr(13).chr(10),0);
$point=0;
for($j=1 ; $j<=$numrecords ; ++$j) { // find and parse each record
$nexttab=strpos($pasted,chr(9),$point);
$fname=substr($pasted,$point,$nexttab-$point);
$point=$nexttab+1;
$nexttab=strpos($pasted,chr(9),$point);
$lname=substr($pasted,$point,$nexttab-$point);
$point=$nexttab+1;
$endline=strpos($pasted,chr(13),$point);
$email=substr($pasted,$point,$endline-$point);
$point=$endline+2;

if(isset($_SESSION["$where"]))  $x=count($_SESSION["$where"]); 
$_SESSION["$where"][$x]['fname']=$fname;
$_SESSION["$where"][$x]['lname']=$lname;
$_SESSION["$where"][$x]['email']=$email;
}
} // end of process pasted list

// process attachment upload request
if(isset($_POST['uploaded'])) { //  file upload request submitted 

// Where the file is going to be placed 
$target_path = "../uploads/";
$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
if(!move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) { $errmsg='There was an error uploading the file'; } 
elseif($_FILES['uploadedfile']['size']>1000000) 
	{ $errmsg="Maximum file size exceeded";  unlink($target_path);}
else { $errmsg='File uploaded'; 
$filename=basename( $_FILES['uploadedfile']['name']);
$ext=substr(strrchr($filename,'.'),1); 
if($ext<>'txt' and $ext<>'pdf' and $ext<>'rtf' and $ext<>'doc' and $ext<>'jpg') 
	{ $errmsg='Illegal file type';  unlink($target_path);}
else { // all checks okay 
$_SESSION['filename']=$filename; // save filename and ext in session variable for send mail function
$_SESSION['ext']=$ext;

} // end of all checks okay
} // end of file uploaded
} // end of file upload request submitted

// remove individuals from the to array
if(isset($_POST['removeto'])) {
	$ind=$_POST['removeto'];
	$lbracket=strpos($ind,'['); $rbracket=strpos($ind,']');
	$lgth=$rbracket-$lbracket-1;
	$ind=substr($ind,$lbracket+1,$lgth);
	for($ct=$ind ; $ct<=count($_SESSION['to'])-2 ; ++$ct) { // move higher indexed items up to fill the gap
	$_SESSION['to'][$ct]=$_SESSION['to'][$ct+1];
	} // end of fill the gap
	unset($_SESSION['to'][$ct]); // unset the last item now moved 
	$errmsg="One name removed from 'to' list"; $bg="#CCC";
}

// clear the recipients list and all arrays
if(isset($_POST['clear'])) {
	$temp = isset($_SESSION['filename']) ? 	$_SESSION['filename'] : '';
	chdir('../uploads/'); // apparently needed to avoid permission denied errors
	if(file_exists("$temp")) unlink("$temp");
	unset($poss, $_SESSION['to'], 
						$_SESSION['poss'], 
						$_SESSION['ctposs'],
						$_SESSION['filename']);
	$errmsg="All headers cleared"; $bg="#CCC";
} // end of clear all

if(isset($_POST['sendpersonal'])) {
// limit number of emails per send to 30
$totto=isset($_SESSION['to']) ? count($_SESSION['to']) : 0;
if(($totto)>30) {$errmsg="Too many recipients. Reduce the lists to 30 or fewer."; $bg="Pink"; }
elseif(!isset($_SESSION['to'])) { $errmsg="no recipient selected";$bg="Pink"; }
elseif($_POST['subject']=='') { $errmsg="no subject";$bg="Pink"; }
elseif($_POST['message']=='') { $errmsg="no message";$bg="Pink"; }
else { // all fields okay
$message=stripslashes($_POST['message']);
$subject=stripslashes($_POST['subject']);

if(isset($_SESSION['to']) && count($_SESSION['to'])>0) { // if there are to recipients

for ($ct=0 ; $ct<=count($_SESSION['to'])-1 ; ++$ct) { // loop through to recipients
$persmes=$message;
$to=$_SESSION['to'][$ct]['email'];
$fname=$_SESSION['to'][$ct]['fname'];

// find codes for personalized first name and replace
$firstpos=strpos($persmes,'%');
if($firstpos<>0 or substr($persmes,0,1)=='%')  $persmes=substr($persmes,0,$firstpos).$fname.substr($persmes,$firstpos+1);

$email=$to; 

if(isset($_SESSION['filename']) and $_SESSION['filename']<>'')  {
	$filename=$_SESSION['filename'];
	$attachpath="../uploads/"."$filename"; }

sendmail($email, $subject, $fromemail, $persmes, $attachpath);
} // end of  to loop
// delete the attachment file
if($filename<>'') unlink("../uploads/$filename");
unset($_SESSION['to']);
unset($_SESSION['poss']);
unset($_SESSION['filename']);
				
$errmsg= $ct." messages sent";$bg="Aquamarine";

// save a copy
$subject=GetSQLValueString($subject, "text");
$to=GetSQLValueString($to, "text");
$fromname=GetSQLValueString($fromname, "text");
$fromemail=GetSQLValueString($fromemail, "text");
$message=GetSQLValueString($message, "text");
$query="insert into mailarchive (tolist, subject, fromemail, fromname, message, whensent) values ($to, $subject, $fromemail, $fromname, $message, now())";
$rsArchive= mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));

} // end of if  are to recipients
} // end of all fields okay to send
} // end send and save
// remove duplicates from to  list
if(isset($_SESSION['to'])) $_SESSION['to']=array_values(array_map("unserialize", array_unique(array_map("serialize", $_SESSION['to']))));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>eMailer</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
    <style type="text/css">
<!--
#directory {
	padding-left: 20px;
	float: right;
	width: 320px;
	border-top-width: 1px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	height: 600px;
	overflow: auto;
}
#message {
	padding-left: 20px;
	float: left;
	height: 600px;
	width: 490px;
	border-top-width: 1px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	overflow: auto;
}
#headers {
	padding-left: 20px;
	height: 600px;
	width: 320px;
	border-top-width: 1px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	overflow: auto;
	float: right;
}

#old {
	width: 1250px;
}
#errmsg {
	background-color: "888";
	padding: 5px;
	width: 450px;
	border-top-width: 3px;
	border-right-width: 3px;
	border-bottom-width: 3px;
	border-left-width: 3px;
	border-top-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
}
-->
    </style>
</head>

<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php'); ?>
  <div id="mainContent">
  <form enctype="multipart/form-data" action="" method="post" name="mailer">
<div id="directory" style="width:"300";float:right;" >
  <h1><center>Email Directory</center></h1>
  <strong>Groups</strong>
  <table border="2" cellspacing="4" cellpadding="2" width="275">
    <tr><td><input name="groupto" type="submit" value="to" style="background-color:<?php echo $bgcolor;?>;color:<?php echo $color;?>"/></td>
	<td><select name="groups" style="width:190px;">
  <option selected="selected">--select group--</option>
  <option value="users">Database users</option>
  <option value="leaders">Harvest leaders</option>
  <option value="scout">Tree scouts</option>
  <option value="branchldr">Branch leaders</option>
  <option value="selects">Select Harvest Team</option>
</select></td>
    </tr>
  </table>
  <br />
<strong>Rosters</strong>
<table border="2" cellspacing="4" cellpadding="2" width="275">
  <tr><td rowspan="2"><input name="gleansto" type="submit" value="to" style="height:50px; background-color:<?php echo $bgcolor;?>;color:<?php echo $color;?>"/></td>
  <td>harvest number: </td> 
  <td align="center"><input name="glean" type="text" id="glean"  size="8" maxlength="4" value="<?php if(isset($_POST['glean'])) echo $_POST['glean'];?>" /></td>
</tr>
<tr><td colspan="2"><select name="roster">
      <option selected="selected">--select roster status--</option>
      <option value="harvested">Harvested</option>
      <option value="signup">Signup</option>
      <option value="absent">Absent</option>
      <option value="assisted">Assistants</option>
      <option value="cancel">Canceled</option>
      <option value="waiting">Waiting list</option>
      <option value="leader">Leader</option>
      <option value="roster">All on roster</option>
      <option value="attended">All who attended</option>
      <option value="expected">All expected</option>
    </select></td></tr></table><br />

<strong>Individuals</strong> - Enter one or more letters
<table border="2" cellspacing="4" cellpadding="2" width="275">

  <tr><td rowspan="2"><input name="indaddv" type="submit" value="find" style="height:50px; background-color:<?php echo $bgcolor;?>;color:<?php echo $color;?>" /></td>
  	  <td>First:</td><td><input name="fname" type="text" value="" size="23" maxlength="20" /></td></tr>
  <tr><td>Last:</td><td><input name="lname" type="text" value="" size="23" maxlength="40" /></td></tr>

<?php
if(isset($_SESSION['poss'])) {$poss=$_SESSION['poss']; $ct=$_SESSION['ctposs'];}
if(isset($poss)) { $i=0;

while ($i<$ct) { 
if($i<$ct-1 && $poss[$i]['fname']==$poss[$i+1]['fname'] && $poss[$i]['lname']==$poss[$i+1]['lname'] && $poss[$i]['email']==$poss[$i+1]['email'])
	{$i=$i+1;} else { // next not duplicate so echo line
?>
<tr> 
<th colspan="3">&nbsp;</th>
</tr>
<tr>
	<td><input name="to" type="submit" value="to [<?php echo $i;?>]" style="background-color:<?php echo $bgcolor;?>;color:<?php echo $color;?>"/></td>
	<td  colspan="2"><?php echo $poss[$i]['fname']." ".$poss[$i]['lname'];?></td>
</tr>
<tr>
	<td></td>
	<td colspan="2"><?php echo $poss[$i]['email'];?></td>
</tr>
<?php $i=$i+1; } // end of not duplicate
} // end of while
} // end of if isset $poss
?></table>
<br />
  <strong>Pasted list</strong> - Paste a table from the Reports Generator
  <table border="2" cellspacing="4" cellpadding="2">
    <tr><td><input name="pasteto" type="submit" value="to" style="height: 50px; background-color:<?php echo $bgcolor;?>;color:<?php echo $color;?>"/></td>
  	 <td><textarea name="pasted" cols="20" rows="2"></textarea></td></tr>
  </table><br />

<strong>Attachment</strong>
<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
<input name="uploadedfile" type="file" /><br />
<input name="uploaded" type="submit" value="Upload File" style="background-color:<?php echo $bgcolor;?>;color:<?php echo $color;?>"/>

</div> <!-- end of div=directory -->
<div id="headers">
  <h1>
    <center>
      Headers
    </center>
  </h1>
  <p><strong>Subject:</strong><br />
    <input name="subject" type="text" size="40" maxlength="80" value="<?php if(isset($_POST['subject'])) echo stripslashes($_POST['subject']);?>" />
  </p>
  <p><strong>From:</strong><br />
    <?php   echo $fromname." ".$fromemail;   ?>
  </p>
  <p><strong>To:</strong></p>
  <p/>
  <p>
    <?php 
  if(isset($_SESSION['to'])) {
	?>
  </p>
  <table border="2" cellspacing="4" cellpadding="2">
    <?php for ($ctto=0 ; $ctto<=count($_SESSION['to'])-1 ; ++$ctto) { ?>
    <tr>
      <td><?php echo $_SESSION['to'][$ctto]['fname']." ".$_SESSION['to'][$ctto]['lname'];?></td>
      <td><input type="submit" name="removeto" value="remove [<?php echo $ctto;?>]" style="background-color:<?php echo $bgcolor;?>;color:<?php echo $color;?>"/></td>
    </tr>
    <tr>
      <td><?php echo $_SESSION['to'][$ctto]['email'];?></td>
    </tr>
    <?php
  } // end of print to loop
  ?>
  </table>
  <?php
  } // end of if isset SESSION[to]
  ?>
  <p></p>
  <p><strong>Attachment: </strong> <?php if(isset($_SESSION['filename'])) echo $_SESSION['filename']; ?></p>
  <p>
    <input type="submit" name="clear" value="Clear all headers" style="background-color:<?php echo $bgcolor;?>;color:<?php echo $color;?>"/>
  </p>
</div>
<!-- end of div=headers -->
<div id="message">
  <h1>
    <center>
      Message
    </center>
  </h1>
  <p>
    <textarea name="message" id="message2" cols="56" rows="20"><?php if(isset($_POST['message'])) echo stripslashes($_POST['message']);?>
</textarea>
  </p>
  <p>
<input type="submit" style="height:50px;font-weight:bold; background-color:<?php echo $bgcolor;?>;color:<?php echo $color;?>" name="sendpersonal" value="Send individual emails" />

  </p>
  <div id="errmsg" style="background-color:<?php echo $bg;?>">
    <p><?php echo $errmsg."<br />The 'to' list has ";
	if(isset($_SESSION['to'])) { echo count($_SESSION['to']);} else {echo '0';}
	echo " items.";?> </p>
  </div>
</div>
<!-- end of div=message -->
  </form>
  
  </div> <!-- end of div=maincontent -->
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
