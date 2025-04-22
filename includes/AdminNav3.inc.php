<script>
<!--
function wopen(url, name, w, h)
{
w += 32;h += 32;
 var win = window.open(url,
  name,
  'width=' + w + ', height=' + h + ', ' +
  'location=no, menubar=no, ' +
  'status=no, toolbar=no, scrollbars=yes, resizable=yes, titlebar=no');
 win.resizeTo(w, h);
 win.focus();
}
// -->
</script> 
<?php $_SESSION['from'] = isset($_SERVER['PHP_SELF'])  ? $_SERVER['PHP_SELF'] : "";
if (!isset($_SESSION)) {   session_start(); }
require_once('../../Connections/piercecty.php');
$who = $_SESSION['MM_Username'];
if($who<>'admin' && $who<>'webmaster') {
$page=$_SERVER['PHP_SELF'];
$file=strrchr($page,"/");
$extlength=strlen(strrchr($file,"."));
$page=substr($file,1,strlen($file)-$extlength-1);

$insertsql = "INSERT INTO pageslog (whenview, who, pageview) VALUES (now(),'$who','$page')";
$result = mysqli_query($piercecty, $insertsql) or die (mysqli_error($piercecty)); }
?>
<div class="Navigation" id="Navigation">
<p><span class="bold">PUBLIC LINKS</span> | <a href="../../index.php">Home</a> | <a href="../../about.php">About</a> | <a href="../../volunteer.php">Volunteer</a> | <a href="../../site_registration.php">Donate Crop</a> | <a href="../../harvestlist.php">Harvests</a> | <a href="../../documents.php">Documents</a> | <a href="../../contact.php">Contact</a> | <br>
<span class="bold">VIEW LINKS</span> | <a href="../../Utilities/sitelist.php">Sites</a> | <a href="../../Utilities/pickerlist.php">Volunteers</a> | <a href="../../harvestlist-master.php">Harvests</a> | <a href="../../Utilities/calculator.php"  onClick="wopen('../../Utilities/calculator.php', 'popup',560, 650); return false;">Calculator</a> | <a href="../../Utilities/ContactLists.php">Contact Lists</a> | <a href="../../MailAPI/eMailer.php">eMailer</a> | <a href="../../Utilities/pickerfind.php">Picker Finder</a> | <a href="../../Utilities/seasonplanner.php<?php echo '#wk'.date('W');?>">Season Planner</a> | <a href="../../Utilities/PagesIndex.php">Index</a> |<br>
<span class="bold">CHANGE LINKS</span> | <a href="../../Utilities/siteupdate.php">Sites</a> | <a href="../../Utilities/pickerupdate.php">Volunteers</a> | <a href="../../Utilities/harvestupdate.php">Update Harvest</a> | <a href="../../Utilities/harvestinsert.php">New Harvest</a> | <a href="../../Utilities/rosterupdate.php">Rosters</a> | <a href="../../Newsletter/maillist/maillist.php">Mail list </a>| <a href="../../Utilities/seasonplanner.php<?php echo '#wk'.date('W');?>">Season Planner</a> | <a href="../../Utilities/PagesIndex.php">Index</a> | <a href="../../help/help.php"  onClick="wopen('../../help/help.php', 'popup', 640, 480); return false;">Page Help</a> |</p>      
</div>
