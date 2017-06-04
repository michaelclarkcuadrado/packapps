<?php
include '../../config.php';
$query = mysqli_query($mysqli, "Select `Variety`, `Grade`, `Size`, `Grower`, `Lot`, `Location` FROM `production_tempRunData` WHERE PK='".$_GET['Run']."'");
mysqli_query($mysqli, "DELETE FROM production_tempRunData Where PK='".$_GET['Run']."'");
$array = array();
while($RunArray = mysqli_fetch_array($query))
{
    array_push($array, $RunArray);
}
echo json_encode($array);