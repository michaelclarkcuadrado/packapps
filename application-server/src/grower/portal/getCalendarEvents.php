<?php
include '../../config.php';
$userinfo = packapps_authenticate_grower();
$growerCode = $userinfo['GrowerCode'];
$start = mysqli_real_escape_string($mysqli, $_GET['start']);
$end = mysqli_real_escape_string($mysqli, $_GET['end']);

$result = mysqli_query($mysqli, "SELECT * FROM grower_growerCalendar WHERE `Grower` LIKE '$growerCode'");
$finishedArray = array();
while ($data = mysqli_fetch_assoc($result)){
    $code = dechex(crc32($data['Grower']));
    $code = '#'.substr($code, 0, 6);
    array_push($finishedArray, array('id' => $data['ID'], 'color' => $code, 'title' => $data['Grower']." | ".$data['Variety']." | ".$data['Strain'], 'start' => $data['Start'], 'end' => $data['EndDate'], 'allDay' => true, 'editable'=> true));
}
header('Content-type: application/json');
echo json_encode($finishedArray);