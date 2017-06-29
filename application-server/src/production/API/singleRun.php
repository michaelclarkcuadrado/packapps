<?php
include '../../config.php';
$query = mysqli_query($mysqli, "Select RunID, RunNumber, Line, isQA, isPreInspected from production_runs where RunID='".$_GET['Run']."' ORDER BY Line ASC");
if (!mysqli_num_rows($query)){die(http_response_code(503));}

$data = mysqli_fetch_assoc($query);

    //$RunArray = Array($data['RunID'], $data['Line']);
    $dumpedQuery = mysqli_query($mysqli, "SELECT Grower, Variety, Quality, `Size`, `Lot`, `Location`, AmountToDump, `isNOT` FROM `production_dumped_fruit` WHERE RunID='".$data['RunID']."'");
    $dumpedArray = mysqli_fetch_all($dumpedQuery);

    $madeQuery = mysqli_query($mysqli, "SELECT ProductNeededName, AmountNeeded, PackSizeNeeded, AmountIsInBoxes FROM `production_product_needed` WHERE RunID='".$data['RunID']."'");
    $madeArray = mysqli_fetch_all($madeQuery);

    $RunArray = array( (isset($_GET['duplicate']) ? 'DEFAULT' : $data['RunID']), $data['Line'], mysqli_num_rows($dumpedQuery), mysqli_num_rows($madeQuery), $dumpedArray, $madeArray, (isset($_GET['duplicate']) ? '' : $data['RunNumber']), (isset($_GET['duplicate']) ? '0' : $data['isQA']), (isset($_GET['duplicate']) ? '0' : $data['isPreInspected']));
header('Content-type: application/json');
echo json_encode($RunArray);