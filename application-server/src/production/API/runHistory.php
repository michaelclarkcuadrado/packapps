<?php
include '../../config.php';
//changes should be reflected in curRuns

if(isset($_GET['runOffset'])) {
    $runOffset = mysqli_real_escape_string($mysqli, $_GET['runOffset']);
} else {
    $runOffset = 0;
}
if(isset($_GET['RunNum'])) {
    $RunNum = mysqli_real_escape_string($mysqli, $_GET['RunNum']);
} else {
    $RunNum = '%';
}
$query = mysqli_query($mysqli, "Select RunID, case when RunNumber = 0 then '' when RunNumber is not null then concat('#',RunNumber) else '' end as RunNumber, Line, concat(date_format(lastEdited, '%a %b %e %Y %h:%i %p'),' by ',lastEditedBy) as lastEdited, isQA, isPreInspected from production_runs where isCompleted=1 AND RunNumber LIKE '$RunNum' ORDER BY RunID DESC LIMIT 10 OFFSET ".$runOffset);
$allRunsArray = Array();
while($data = mysqli_fetch_assoc($query))
{
    //$RunArray = Array($data['RunID'], $data['Line']);
    $dumpedQuery = mysqli_query($mysqli, "SELECT CASE WHEN `Grower`='' THEN '' ELSE concat(`Grower`, ' | ') END AS Grower, CASE WHEN `Variety`='' THEN '' ELSE concat(`Variety`, ' | ') END AS Variety, CASE WHEN `Quality`='' THEN '' ELSE concat(`Quality`, ' | ') END AS Quality,  CASE WHEN `Size`='' THEN '' ELSE concat(`Size`, ' | ') END AS Size, CASE WHEN `Lot`='' THEN '' ELSE concat(`Lot`, ' | ') END AS Lot, CASE WHEN `Location`='' THEN '' ELSE concat(`Location`, ' | ') END AS Location, AmountToDump, `isNOT` FROM `production_dumped_fruit` WHERE RunID='".$data['RunID']."'");
    $dumpedArray = mysqli_fetch_all($dumpedQuery);

    $madeQuery = mysqli_query($mysqli, "SELECT ProductNeededName, case when amountIsInBoxes then concat('(',`AmountNeeded`,')') else `AmountNeeded` end as `AmountNeeded`, PackSizeNeeded FROM `production_product_needed` WHERE RunID='".$data['RunID']."'");
    $madeArray = mysqli_fetch_all($madeQuery);

    $RunArray = array('RunID' => $data['RunID'], 'line' => $data['Line'], 'dumpedArray' => $dumpedArray, 'madeArray' => $madeArray, 'RunNumber' => $data['RunNumber'], 'lastEdited' => $data['lastEdited'], 'isQA' => $data['isQA'], 'isPreInspected' => $data['isPreInspected']);

    if($data['isQA'] > 0)
    {
        $QAquery = mysqli_query($mysqli, "SELECT format(avg(Weight),3) as Weight, ifnull(format(avg(Brix),1), 'N/A') as Brix, Note, format((sum(Pressure1) + sum(Pressure2))/(count(*)*2),2) as Pressure from quality_run_inspections WHERE RunID='".$data['RunID']."'  AND `isPreInspection`=0");
        $RunArray['QA'] = mysqli_fetch_assoc($QAquery);
    }

    if($data['isPreInspected'] > 0)
    {
        $QAquery = mysqli_query($mysqli, "SELECT format(avg(Weight),3) as Weight, ifnull(format(avg(Brix),1), 'N/A') as Brix, Note, format((sum(Pressure1) + sum(Pressure2))/(count(*)*2),2) as Pressure from quality_run_inspections WHERE RunID='".$data['RunID']."'  AND `isPreInspection`>0");
        $RunArray['PreInspection'] = mysqli_fetch_assoc($QAquery);
    }


    array_push($allRunsArray, $RunArray);
}

echo json_encode($allRunsArray);