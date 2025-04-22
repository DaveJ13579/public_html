<?php function encode($ID) {
$lenID=strlen($ID);
for ($i=1 ; $i<=6-$lenID ;++$i) $ID.=rand(0,9);
$eID=$ID.$i;
$eID.=strrev($eID);
$eID=base_convert($eID,11,16);
return $eID;}

function decode($eID) {
$ID=base_convert($eID,16,11);
$ID=str_pad($ID, 14, '0', STR_PAD_LEFT);
$ID1=substr($ID,0,7);
$ID2=strrev(substr($ID,7));
if($ID1<>$ID2) {$ID=-1;} 
else { $lenpad=substr($ID1,6,1);
$ID=substr($ID,0,strlen($ID1)-$lenpad);}
return $ID;} 
?>