<?php
//creates json to answer queries from jstree
include '../../config.php';

$array = array();
if (!strpos($_GET['id'], ':')) {
//Load varieties
    $query = mysqli_query($mysqli, "SELECT `VarDesc` AS Var, format(sum(`BuOnHand`),0) as OH FROM BULKOHCSV WHERE `BuOnHand` != 0 GROUP BY Var");
    while ($resultArray = mysqli_fetch_assoc($query)) {
        array_push($array, array('text' => "<span class='mdl-badge mdl-badge--no-background' data-badge='".$resultArray['OH']."'>".$resultArray['Var']."</span>", 'children' => true, 'id' => 'variety:' . $resultArray['Var']));
    }
    header('Content-type: application/json');
    echo json_encode($array);
} else {
    //prepare parameters from request
    $queryParamsArray = explode(":", $_GET['id']);
    $params = array();
    for ($i = 0; $i < count($queryParamsArray); $i++) {
        if ($i % 2) {
            array_push($params, mysqli_real_escape_string($mysqli, $queryParamsArray[$i]));
        }
    }

    //load grower
    if (count($params) == 1) {
        $query = mysqli_query($mysqli, "SELECT `Grower` AS Grower, format(sum(`BuOnHand`),0) as OH FROM BULKOHCSV WHERE `VarDesc` = '".$params[0]."' and `BuOnHand` != 0 GROUP BY Grower");
        while ($resultArray = mysqli_fetch_assoc($query)) {
            array_push($array, array('text' => "<span class='mdl-badge mdl-badge--no-background' data-badge='".$resultArray['OH']."'>".$resultArray['Grower']."</span>", 'children' => true, 'id' => $_GET['id'] . ":" . 'grower:' . $resultArray['Grower']));
        }
        header('Content-type: application/json');
        echo json_encode($array);
    } //load farm
    else if (count($params) == 2) {
        $query = mysqli_query($mysqli, "SELECT `Farm` AS `Farm`, format(sum(`BuOnHand`),0) as OH FROM BULKOHCSV WHERE `Grower`= '" . $params[1] . "' and `VarDesc` = '".$params[0]."'  and `BuOnHand` != 0 GROUP BY `Farm`");
        while ($resultArray = mysqli_fetch_assoc($query)) {
            array_push($array, array('text' => "<span class='mdl-badge mdl-badge--no-background' data-badge='".$resultArray['OH']."'>Farm: ".$resultArray['Farm']."</span>", 'children' => true, 'id' => $_GET['id'] . ":" . 'Farm:' . $resultArray['Farm']));
        }
        header('Content-type: application/json');
        echo json_encode($array);
    } //load Block
    else if (count($params) == 3) {
        $query = mysqli_query($mysqli, "SELECT `Block` AS `Block`, format(sum(`BuOnHand`),0) as OH FROM BULKOHCSV WHERE `Grower` = '" . $params[1] . "' and `VarDesc` = '".$params[0]."' and `Farm` = '".$params[2]."'  and `BuOnHand` != 0 GROUP BY `Block`");
        while ($resultArray = mysqli_fetch_assoc($query)) {
            array_push($array, array('text' => "<span class='mdl-badge mdl-badge--no-background' data-badge='".$resultArray['OH']."'>Block: ".$resultArray['Block']."</span>", 'children' => true, 'id' => $_GET['id'] . ":" . 'Block:' . $resultArray['Block']));
        }
        echo json_encode($array);
    } //load Room
    else if (count($params) == 4) {
        $query = mysqli_query($mysqli, "SELECT `RoomNum` AS `RoomNum`, format(sum(`BuOnHand`),0) as OH FROM BULKOHCSV WHERE `Grower` = '" . $params[1] . "' and `VarDesc` = '".$params[0]."' and `Farm` = '".$params[2]."' and `Block` = '".$params[3]."'  and `BuOnHand` != 0 GROUP BY `RoomNum`");
        while ($resultArray = mysqli_fetch_assoc($query)) {
            array_push($array, array('text' => "<span class='mdl-badge mdl-badge--no-background' data-badge='".$resultArray['OH']."'>Room #".$resultArray['RoomNum']."</span>", 'children' => true, 'id' => $_GET['id'] . ":" . 'roomnum:' . $resultArray['RoomNum']));
        }
        echo json_encode($array);
    } //load Average info for these fruit
    else if (count($params) == 5) {
        $color = mysqli_fetch_assoc(mysqli_query($mysqli, "select Color, count(*) as magnitude from quality_RTsWQuality WHERE `Grower` = '" . $params[1] . "' and `Var Desc` = '".$params[0]."' and `Farm` = '".$params[2]."' and `Block` = '".$params[3]."' and `BuOnHand` != 0 and Color != '' Group By Color order by magnitude desc limit 1"));
        $query = mysqli_query($mysqli, "SELECT `RT#`, round(avg(PressureAvg),2) as Pressure, round(avg(CASE WHEN `DAAvg` <> '' THEN DAAvg ELSE NULL END),2) as DA, round(avg(CASE WHEN `Brix` <> '' THEN Brix ELSE NULL END),2) as Brix, round(avg(`AverageWeight`),2) as Weight, format(sum(`BuOnHand`),0) as OH FROM quality_RTsWQuality WHERE `Grower` = '" . $params[1] . "' and `Var Desc` = '".$params[0]."' and `Farm` = '".$params[2]."' and `Block` = '".$params[3]."'  and `BuOnHand` != 0 and isQA != 'FALSE' GROUP BY `Block`");
        if (mysqli_num_rows($query) == 0) {
            array_push($array, array('icon' => '/production/scripts/themes/default/cross.png','text' => "<span class='mdl-badge mdl-badge--no-background' data-badge='".$resultArray['OH']."'>No QA data available in this room.</span>", 'id' => $_GET['id'] . ":" . 'RT:' . $resultArray['RT#']));
        } else {
            while ($resultArray = mysqli_fetch_assoc($query)) {
                array_push($array, array('a_attr' => array('href' => '/quality/assets?RT='.$resultArray['RT#']),'icon' => '/production/scripts/themes/default/check.png','text' => "<span class='mdl-badge mdl-badge--no-background' data-badge='".$resultArray['OH']."'>Color: ".$color['Color'].", Pres: ".$resultArray['Pressure'].", DA: ".$resultArray['DA'].", Brix: ".$resultArray['Brix']." Weight: ".$resultArray['Weight']."</span>", 'id' => $_GET['id'] . ":" . 'RT:' . $resultArray['RT#']));
            }
        }
        echo json_encode($array);
    }
}
