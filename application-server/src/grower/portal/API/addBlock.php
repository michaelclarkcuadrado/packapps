<?php
include '../../../config.php';
$userinfo = packapps_authenticate_grower();

//sanitize inputs
$farmID = mysqli_real_escape_string($mysqli, $_POST['Farm']);
$strainID = mysqli_real_escape_string($mysqli, $_POST['Strain']);
$BlockDesc = mysqli_real_escape_string($mysqli, $_POST['Block']);
$newEst = mysqli_real_escape_string($mysqli, $_POST['newEst']);
$growerCode = $userinfo['GrowerCode'];

//validate that farm belongs to grower
$checkResult = mysqli_query($mysqli, "SELECT farmID
FROM grower_farms
  JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID
WHERE farmID = '$farmID' AND GrowerCode = '$growerCode';
");

mysqli_query($mysqli, "INSERT INTO 
`grower_crop-estimates` (`farmID`, strainID, BlockDesc) 
VALUES ('$farmID', '$strainID', '$BlockDesc')")
or die(header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500));

mysqli_query($mysqli, "
INSERT INTO grower_block_bushel_history
(block_PK, year, value_type, bushel_value)
VALUES 
  ('".mysqli_insert_id($mysqli)."', '".date('Y')."', 'est', '$newEst')
  ");