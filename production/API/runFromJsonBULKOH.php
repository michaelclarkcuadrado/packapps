<?php
include '../../config.php';
$rawArray = json_decode($_POST['array']);
$cleanArray = array();

for ($i = 0; $i < count($rawArray); $i++) {
    $temp = explode(':', $rawArray[$i]);
    $temp2 = array();
    for ($j = 0; $j < count($temp); $j++) {
        if ($j % 2) {
            array_push($temp2, $temp[$j]);
        }
    }
    $temp2 = array_map("rtrim", $temp2);
    array_push($cleanArray, $temp2);
}

$randPK = rand();
for ($i = 0; $i < count($cleanArray); $i++) {
    $params = count($cleanArray[$i]);
    if ($params >= 5) {
        mysqli_query($mysqli, "INSERT INTO production_tempRunData VALUES ('".$randPK."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][0])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][2])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][3])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][1])."', 'Tree Run', '".mysqli_real_escape_string($mysqli, "Rm #".$cleanArray[$i]['4'])."')");
    }
    else if ($params = 4)
    {
        mysqli_query($mysqli, "INSERT INTO production_tempRunData VALUES ('".$randPK."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][0])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][2])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][3])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][1])."', 'Tree Run', DEFAULT)");
    }
    else if ($params = 3)
    {
        mysqli_query($mysqli, "INSERT INTO production_tempRunData VALUES ('".$randPK."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][0])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][2])."', DEFAULT, '".mysqli_real_escape_string($mysqli, $cleanArray[$i][1])."', 'Tree Run', DEFAULT)");
    }
    else if ($params == 2)
    {
        mysqli_query($mysqli, "INSERT INTO production_tempRunData VALUES ('".$randPK."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][0])."', DEFAULT, DEFAULT, '".mysqli_real_escape_string($mysqli, $cleanArray[$i][1])."', 'Tree Run', DEFAULT)");
    }
    else if ($params == 1)
    {
        mysqli_query($mysqli, "INSERT INTO production_tempRunData VALUES ('".$randPK."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][0])."', DEFAULT, DEFAULT, DEFAULT, 'Tree Run', DEFAULT)");
    }
}

echo json_encode(array($randPK));