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
        $Location = mysqli_fetch_array(mysqli_query($mysqli, "SELECT concat('Rm #',rtrim(`Room#`)) as Loc from PSOHCSV WHERE `Grade Desc` = '" . $cleanArray[$i][1] . "' and `Var Desc` = '".$cleanArray[$i][0]."' and `Size Desc` = '".$cleanArray[$i][2]."' and `LotB` = '".$cleanArray[$i][3]."' and `Grower` = '".$cleanArray[$i][4]."' LIMIT 1"));
        mysqli_query($mysqli, "INSERT INTO production_tempRunData VALUES ('".$randPK."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][0])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][1])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][2])."', 'PS (SubGw: ".mysqli_real_escape_string($mysqli, $cleanArray[$i][4]).")', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][3])."', '".mysqli_real_escape_string($mysqli, $Location['Loc'])."')");
    }
    else if ($params = 4)
    {
        mysqli_query($mysqli, "INSERT INTO production_tempRunData VALUES ('".$randPK."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][0])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][1])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][2])."', 'PS', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][3])."', DEFAULT)");
    }
    else if ($params = 3)
    {
        mysqli_query($mysqli, "INSERT INTO production_tempRunData VALUES ('".$randPK."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][0])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][1])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][2])."', 'PS', DEFAULT, DEFAULT)");
    }
    else if ($params == 2)
    {
        mysqli_query($mysqli, "INSERT INTO production_tempRunData VALUES ('".$randPK."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][0])."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][1])."', DEFAULT, 'PS', DEFAULT, DEFAULT)");
    }
    else if ($params == 1)
    {
        mysqli_query($mysqli, "INSERT INTO production_tempRunData VALUES ('".$randPK."', '".mysqli_real_escape_string($mysqli, $cleanArray[$i][0])."', DEFAULT, DEFAULT, 'PS', DEFAULT, DEFAULT)");
    }
}

echo json_encode(array($randPK));