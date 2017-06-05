<?php
include '../../config.php';
//changes should be reflected in runHistory

//display tracking here
if (isset($_COOKIE['__display']) && isset($_COOKIE['blue'])){
    $clientIP = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    mysqli_query($mysqli, "INSERT INTO production_ConnectedDisplays (`IP_addr`, `connected_line`, `last_seen`, `User_agent`) VALUES ('$clientIP', '1', NOW(), '$userAgent') ON DUPLICATE KEY UPDATE last_seen = NOW(), `User_agent`='$userAgent'");
} elseif (isset($_COOKIE['__display']) && isset($_COOKIE['gray'])){
    $clientIP = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    mysqli_query($mysqli, "INSERT INTO production_ConnectedDisplays (`IP_addr`, `connected_line`, `last_seen`, `User_agent`) VALUES ('$clientIP', '2', NOW(), '$userAgent') ON DUPLICATE KEY UPDATE last_seen = NOW(), `User_agent`='$userAgent'");
} elseif (isset($_COOKIE['__display']) && isset($_COOKIE['presizer'])){
    $clientIP = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    mysqli_query($mysqli, "INSERT INTO production_ConnectedDisplays (`IP_addr`, `connected_line`, `last_seen`, `User_agent`) VALUES ('$clientIP', '3', NOW(), '$userAgent') ON DUPLICATE KEY UPDATE last_seen = NOW(), `User_agent`='$userAgent'");
}


//prepares run info for clients
$query = mysqli_query($mysqli, "Select RunID, case when RunNumber = 0 then '' when RunNumber is not null then concat('#',RunNumber) else '' end as RunNumber, Line, concat(date_format(lastEdited, '%a %b %e %Y %h:%i %p'),' by ',lastEditedBy) as lastEdited, isQA, isPreInspected from production_runs where isCompleted=0");
$allRunsArray = Array();
while($data = mysqli_fetch_assoc($query))
{
    $dumpedQuery = mysqli_query($mysqli, "SELECT CASE WHEN `Grower`='' THEN '' ELSE `Grower` END AS Grower, CASE WHEN `Variety`='' THEN '' ELSE concat(' | ', `Variety`) END AS Variety, CASE WHEN `Quality`='' THEN '' ELSE concat(' | ', `Quality`) END AS Quality, CASE WHEN `Size`='' THEN '' ELSE concat(' | ', `Size`) END AS Size, CASE WHEN `Lot`='' THEN '' ELSE concat(' | ', `Lot`) END AS Lot, CASE WHEN `Location`='' THEN '' ELSE concat(' | ', `Location`) END AS Location, AmountToDump, `isNOT` FROM `production_dumped_fruit` WHERE RunID='".$data['RunID']."'");
    $dumpedArray = mysqli_fetch_all($dumpedQuery);

    $madeQuery = mysqli_query($mysqli, "SELECT ProductNeededName, case when amountIsInBoxes then concat('(',`AmountNeeded`,')') else `AmountNeeded` end as `AmountNeeded`, PackSizeNeeded FROM `production_product_needed` WHERE RunID='".$data['RunID']."'");
    $madeArray = mysqli_fetch_all($madeQuery);

    $RunArray = array('RunID' => $data['RunID'], 'line' => $data['Line'], 'dumpedArray' => $dumpedArray, 'madeArray' => $madeArray, 'RunNumber' => $data['RunNumber'], 'lastEdited' => $data['lastEdited'], 'isQA' => $data['isQA'], 'isPreInspected' => $data['isPreInspected']);

    if($data['isQA'] > 0)
    {
        $QAquery = mysqli_query($mysqli, "SELECT format(avg(Weight),3) as Weight, ifnull(format(avg(Brix),1), 'N/A') as Brix, Note, format((sum(Pressure1) + sum(Pressure2))/(count(*)*2),2) as Pressure from run_inspections WHERE RunID='".$data['RunID']."' AND `isPreInspection`=0");
        $RunArray['QA'] = mysqli_fetch_assoc($QAquery);
    }

    if($data['isPreInspected'] > 0)
    {
        $PIquery = mysqli_query($mysqli, "SELECT format(avg(Weight),3) as Weight, ifnull(format(avg(Brix),1), 'N/A') as Brix, Note, format((sum(Pressure1) + sum(Pressure2))/(count(*)*2),2) as Pressure from run_inspections WHERE RunID='".$data['RunID']."' AND `isPreInspection`>0");
        $RunArray['PreInspection'] = mysqli_fetch_assoc($PIquery);
    }
    array_push($allRunsArray, $RunArray);
}

//'backdoor' that causes all clients to refresh. Set to same version as index.php, then wait 10 seconds, and re-comment. All clients will re-fetch to match version.
//
//$allRunsArray['refreshpl0x'] = '30';
//
echo json_encode($allRunsArray);