<?php

// from volunteer number returns first name last name
function volname($ID_picker) {
global $piercecty;
$q="select fname, lname from pickers where ID_picker=$ID_picker";
$name='not found';
if(is_numeric($ID_picker)) {
$rsq=mysqli_query($piercecty, $q) or die(mysqli_error($piercecty));
$r=mysqli_fetch_assoc($rsq);
$name=$r['fname'].' '.$r['lname'];
}
return $name;
}

// from harvest number, returns an array of ID_crop, crop name, weight of crops from one harvest
function convarr($harvest) {
global $piercecty;
$convarr=array(); 
$convq="select * from donations where ID_harvest=$harvest";
$rsConv=mysqli_query($piercecty, $convq) or die(mysqli_error($piercecty));
$ct=-1;
while ($convrow=mysqli_fetch_assoc($rsConv)) {
		++$ct;	
		$convarr[$ct]['ID_crop']=$convrow['ID_crop'];
		$convarr[$ct]['pounds']=$convrow['pounds'];
		$ID_crop=$convrow['ID_crop'];
		$cropq="select name from crops where ID_crop=$ID_crop";
		$rsCrop=mysqli_query($piercecty, $cropq) or die(mysqli_error($piercecty));
		$croprow=mysqli_fetch_assoc($rsCrop);
// echo '<br />ID:'.$ID_crop.' name:'.$croprow['name'];
	$convarr[$ct]['name']=$croprow['name'];
} 
return $convarr;
}
// compile crops list from a harvest number and return a comma separated string
function cropstring($harvest) {
$convarr=convarr($harvest);
$crops='';
foreach($convarr as $convrow) { $crops.=$convrow['name'].', '; 	}
if($crops) $crops=substr($crops,0,-2);	
return $crops;
}

// crop name from from crop number
function cropname($ID_crop) {
global $piercecty;
$cropname='unknown';
$q="select name from crops where ID_crop=$ID_crop";
$rsQ=mysqli_query($piercecty, $q);
if(mysqli_num_rows($rsQ)) {
   $r=mysqli_fetch_assoc($rsQ);
   $cropname=$r['name'];
}
return $cropname;
}


/*
// from harvest number, returns an array of ID_crop, crop name, weight of crops from one harvest
function convarr($harvest) {
global $piercecty;
$convarr=array(); $ct=1;
while ($ct<=10) {
	$convq="select crop$ct as crop, wgt$ct as wgt from harvests where ID_harvest=$harvest";
	$rsConv=mysqli_query($piercecty, $convq) or die(mysqli_error($piercecty));
	$convrow=mysqli_fetch_assoc($rsConv);
	if($convrow['crop']) {
		$ID_crop=$convrow['crop'];
		$convarr[$ct]['crop']=$convrow['crop'];
		$convarr[$ct]['wgt']=$convrow['wgt'];
		$cropq="select name from crops where ID_crop=$ID_crop";
		$rsCrop=mysqli_query($piercecty, $cropq) or die(mysqli_error($piercecty));
		if($rsCrop) {
			$croprow=mysqli_fetch_assoc($rsCrop);
			$convarr[$ct]['name']=$croprow['name'];
		}
	}
	++$ct;
}
return $convarr;
}
// compile crops list from a harvest number and return a comma separated string
function cropstring($harvest) {
$convarr=convarr($harvest);
$crops='';
foreach($convarr as $convrow) { $crops.=$convrow['name'].', '; 	}
if($crops) $crops=substr($crops,0,-2);	
return $crops;
}
*/
?>