<?php
//fetches block info by Block ID
include '../../config.php';
packapps_authenticate_user('quality');
$pk = mysqli_real_escape_string($mysqli, $_GET['q']);
$query = mysqli_query($mysqli, "select GrowerName as grower, VarietyName as variety, `strainName` as strain, IFNULL(NULLIF(farmName, ''), 'Unknown') as farm, IFNULL(NULLIF(BlockDesc, ''), 'Unknown') as block FROM `grower_gfbvs-listing` WHERE `PK`='$pk'");
if (mysqli_num_rows($query) == '0') {
    die(json_encode(array('Error' => 'NULL')));
}
echo json_encode(mysqli_fetch_assoc($query));
