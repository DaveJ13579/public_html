<?php require_once('Connections/piercecty.php'); 
if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change, view";
$MM_restrictGoTo = "login.php";
require_once('includes/levelcheck.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Harvest print - select pages</title>
<link href="database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
<?php include_once('includes/AdminNav1.inc.php');?>
  <div id="mainContent">
    <h2>Harvest packet - select  pages to print</h2>

<?php if(!isset($_GET['harvest'])) { echo "No harvest number in link."; }
else { // got harvest number
$harvest= $_GET['harvest'];
?>
    <form id="form1" name="form1" method="post" action="harvestroster-printselect.php" target="blank">
      <p>Select pages to include in the printed packet.<br />
      Choose 'All separate' for separate pages for each section.<br />
      Choose 'Selected breaks' and check page break boxes to customize page breaks.</p>
      <table width="400" border="1" cellpadding="2">
        <tr align="center">
        <th><label><input type="radio" name="pages" value="separate" />
            All separate</label></th>
             <th><label><input type="radio" name="pages" value="breaks" checked="checked" />
            Selected breaks</label></th>
        </tr>
	  </table>
      <table border="1" cellpadding="2">
        <tr><td><label><input type="checkbox" name="info" checked="checked"/>
          <strong>Harvest information</strong></label></td></tr>

        <tr align="center"><td><label><input type="checkbox" name="scouting-brk" />
            break</label></td></tr>
        <tr><td><label><input type="checkbox" name="scouting"   checked="checked" />
              <strong>Scouting and planning</strong></label></td></tr>
          
        <tr align="center"><td><label><input type="checkbox" name="auth-brk"  />
            break</label></td></tr>
        <tr><td><label><input type="checkbox" name="auth" value="auth"   checked="checked"/>
              <strong>Entry Authorization</strong></label></td></tr>
    
        <tr align="center"><td><label><input type="checkbox" name="roster1-brk" />
            break</label></td></tr>
        <tr><td><label><input type="checkbox" name="roster1"   checked="checked"/>
              <strong>Roster A-L</strong></label></td></tr>

        <tr align="center"><td><label><input type="checkbox" name="roster2-brk" />
            break</label></td></tr>
        <tr><td><label><input type="checkbox" name="roster2"   checked="checked"/>
              <strong>Roster M-Z</strong></label></td></tr>

        <tr align="center"><td><label><input type="checkbox" name="waiting-brk"  />
            break</label></td></tr>
        <tr><td><label><input type="checkbox" name="special"   checked="checked" />
              <strong>Limitations and Accommodations</strong></label></td></tr>
        <tr align="center"><td><label><input type="checkbox" name="special-brk"  />
            break</label></td></tr>
        <tr><td><label><input type="checkbox" name="waiting"   checked="checked" />
              <strong>Waiting list</strong></label></td></tr>
        <tr align="center"><td><label><input type="checkbox" name="summary-brk" />
            break</label></td></tr>
        <tr><td><label><input type="checkbox" name="summary"  checked="checked"/>
              <strong>Harvest summary</strong></label></td></tr>
  
        <tr align="center"><td><label><input type="checkbox" name="donation-brk"  />
            break</label></td></tr>
        <tr><td><label><input type="checkbox" name="donation"  checked="checked"/>
              <strong>Donation form</strong></label></td></tr>

      </table>
      <p>
        <input type="submit" name="packet" id="packet" value="Get packet" />
        <input name="harvest" type="hidden" value="<?php echo $harvest; ?>" />
      </p>
    </form>
<?php } // end of got harvest number
?>
  </div>
</div>
</body>
</html>
