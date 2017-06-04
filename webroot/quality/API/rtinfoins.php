<?php
include '../../config.php';

//fetch pre-finished info
$RT = mysqli_real_escape_string($mysqli, $_GET['q']);
$query = mysqli_query($mysqli, "select GrowerName, case when rtrim(`CommDesc`)='So Hem Apple' then 'Apple' else rtrim(CommDesc) end as CommDesc, case when date(Date)=curdate() then 1 else 0 end as Today, rtrim(VarDesc) as VarDesc, rtrim(StrDesc) as StrDesc, FarmDesc, BlockDesc, concat(LocationDesc, ' #', RoomNum) as Location, QtyOnHand  from BULKOHCSV where `RT#`='$RT'");
if (mysqli_num_rows($query) == '0') {
    die(http_response_code(500));
}
$output = mysqli_fetch_assoc($query);

//check if this RT has already been done
$alreadyDoneSamples = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `#Samples` FROM InspectedRTs WHERE `RTNum` = '$RT'"))['#Samples'];
if($alreadyDoneSamples == null){
    $query2 = mysqli_query($mysqli, "SELECT (CASE WHEN (count(`RTNum`))>=1 THEN 5 ELSE 10 END) AS NumSamplesRequired FROM InspectedRTs JOIN BULKOHCSV ON `RT#`=RTNum WHERE GrowerName='" . $output['GrowerName'] . "' AND VarDesc='" . $output['VarDesc'] . "' AND StrDesc='" . $output['StrDesc'] . "' AND FarmDesc='" . $output['FarmDesc'] . "' AND BlockDesc='" . $output['BlockDesc'] . "' AND DATE(`DateInspected`)=curdate()");
    $output2 = mysqli_fetch_assoc($query2);

//perform additional test to see if it is first in season
    if ($output2['NumSamplesRequired'] == 10 && ($output['CommDesc'] != 'Peach' && $output['CommDesc'] != 'Nectarine')) {
        $query2 = mysqli_query($mysqli, "SELECT (CASE WHEN (count(`RTNum`))>=1 THEN 10 ELSE 20 END) AS NumSamplesRequired FROM InspectedRTs JOIN BULKOHCSV ON `RT#`=RTNum WHERE GrowerName='" . $output['GrowerName'] . "' AND VarDesc='" . $output['VarDesc'] . "' AND StrDesc='" . $output['StrDesc'] . "' AND FarmDesc='" . $output['FarmDesc'] . "' AND BlockDesc='" . $output['BlockDesc'] . "' ");
        $output2 = mysqli_fetch_assoc($query2);
    }

    $final = array_merge($output, $output2);
} else {
    $final = array_merge($output, array('NumSamplesRequired'=>$alreadyDoneSamples,'isDone'=>1));
}

echo json_encode($final);
