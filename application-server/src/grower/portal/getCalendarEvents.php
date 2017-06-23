<?php
include '../../config.php';
$adminauth = mysqli_query($mysqli, "SELECT isAdmin FROM grower_growerLogins WHERE GrowerCode='" . $_SERVER['PHP_AUTH_USER'] . "'");
$admin = mysqli_fetch_array($adminauth);
if ($admin['isAdmin']){
    $growerCode = '%';
} else {
    $growerCode = ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER']));
}
$start = mysqli_real_escape_string($mysqli, $_GET['start']);
$end = mysqli_real_escape_string($mysqli, $_GET['end']);

$result = mysqli_query($mysqli, "SELECT * FROM grower_growerCalendar WHERE `Grower` LIKE '$growerCode'");
$finishedArray = array();
while ($data = mysqli_fetch_assoc($result)){
    $code = dechex(crc32($data['Grower']));
    $code = '#'.substr($code, 0, 6);
    array_push($finishedArray, array('id' => $data['ID'], 'color' => $code, 'title' => $data['Grower']." | ".$data['Variety']." | ".$data['Strain'], 'start' => $data['Start'], 'end' => $data['EndDate'], 'allDay' => true, 'editable'=> true));
}

echo json_encode($finishedArray);