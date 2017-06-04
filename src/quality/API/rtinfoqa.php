<?php
include '../../config.php';

//fetch pre-finished info
$query=mysqli_query($mysqli,"select GrowerName, `CommDesc` as CommDesc, VarDesc, StrDesc, FarmDesc, BlockDesc, Date, QtyOnHand, BuOnHand, ClassDesc as ReceiptType, `Color Quality` as `ColorQuality`, Blush, `#Samples` as NumSamples, `Bruise`, `BitterPit`, `Russet`, `SanJoseScale`, `SunBurn`, `Scab`, `StinkBug`, `Note`, date(`DateInspected`) as DateInspected, BuOnHand as Bu, QtyOnHand as Qty,  `InspectedBy`, `FTAup`, `DAFinished`, `StarchFinished` from InspectedRTs left join BULKOHCSV on `RT#`=`RTNum` where `RTNum`='".$_GET['q']."'");
if(mysqli_num_rows($query)== '0'){die(http_response_code(500));}
$array=mysqli_fetch_assoc($query);

//fetch FTA/DA data
$stmt=mysqli_query($mysqli, "select SampleNum, ifnull(Pressure1, 0) as Pressure1, ifnull(Pressure2, 0) as Pressure2, ifnull(Weight, 0) as Weight from AppleSamples where `RT#` = '".$_GET['q']."'");
$stmt2=mysqli_fetch_all($stmt ,1);

$output=array_merge(array_map('rtrim',$array), $stmt2);
echo json_encode($output);
