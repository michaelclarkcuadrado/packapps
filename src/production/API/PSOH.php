<?php

//This file takes the id from the jstree node that it generates, and then parses it to return the next level of the tree in the next request. It also checks to see if fruit has been marked bad, and calculates percentages throughout the tree.

include '../../config.php';
$array = array();
if (!strpos($_GET['id'], ':')) {
//Load varieties
    $query = mysqli_query($mysqli, "SELECT `Var Desc` AS Var, format(sum(`On Hand`),0) as OH FROM PSOHCSV GROUP BY Var");
    while ($resultArray = mysqli_fetch_assoc($query)) {
        array_push($array, array('text' => "<span class='mdl-badge mdl-badge--no-background' data-badge='".$resultArray['OH']."'>".$resultArray['Var']."</span>", 'children' => true, 'id' => 'variety:' . $resultArray['Var']));
    }
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
    $i = 0;
    //load grade
    if (count($params) == 1) {
        $query = mysqli_query($mysqli, "SELECT `Grade Desc` AS Grade, format(sum(`On Hand`),0) as OH FROM PSOHCSV WHERE `Var Desc` = '".$params[0]."' and rtrim(`Location Desc`) != 'Missing' GROUP BY Grade");
        while ($resultArray = mysqli_fetch_assoc($query)) {
            array_push($array, array('text' => "<span class='mdl-badge mdl-badge--no-background' data-badge='".$resultArray['OH']."'>".$resultArray['Grade']."</span>", 'children' => true, 'id' => $_GET['id'] . ":" . 'grade:' . $resultArray['Grade']));
        }
        echo json_encode($array);
    } //load size of fruit
    else if (count($params) == 2) {
        $query = mysqli_query($mysqli, "SELECT `Size Desc` AS `Size`, if(sum(isBad > 0), 1, 0) as isBad, sum(`On Hand`) as OH FROM PSOHCSV LEFT JOIN `PSOHCSV_flagged_bad_runs` on `PSOHCSV_flagged_bad_runs`.`Run`=`PSOHCSV`.`Run#` WHERE `Grade Desc` = '" . $params[1] . "' and `Var Desc` = '".$params[0]."'  and rtrim(`Location Desc`) != 'Missing'  GROUP BY `Size Desc` Order by isBad DESC, CAST(LEFT(`Size Desc`, LOCATE(' ', `Size Desc`)) AS INT) ASC");
        while ($resultArray = mysqli_fetch_assoc($query)) {
            if($resultArray['isBad'] == 1)
            {
                $amount = mysqli_fetch_array(mysqli_query($mysqli, "SELECT format(sum(`On Hand`),0) as badAmt FROM `PSOHCSV` JOIN `PSOHCSV_flagged_bad_runs` on `PSOHCSV_flagged_bad_runs`.`Run`=`PSOHCSV`.`Run#` WHERE `Grade Desc` = '" . $params[1] . "' and `Var Desc` = '".$params[0]."'  and rtrim(`Location Desc`) != 'Missing' and  `Size Desc` = '".$resultArray['Size']."' GROUP BY `Size`"));
            }
            array_push($array, array('text' => "<span class='mdl-badge mdl-badge--no-background ".($resultArray['isBad'] == 1 ? (($amount['badAmt'] / $resultArray['OH'] * 100) < 90 ? 'bad-group' : 'bad-run') : '')."' data-badge='".$resultArray['OH']."'>".$resultArray['Size'].($resultArray['isBad'] == 1 ? " (".round($amount['badAmt'] / $resultArray['OH'] * 100, 2)."% bad) " : '')."</span>", 'children' => true, 'id' => $_GET['id'] . ":" . 'size:' . $resultArray['Size']));
        }
        echo json_encode($array);
    } //load LotB of fruit
    else if (count($params) == 3) {
        $query = mysqli_query($mysqli, "SELECT `LotB` AS `LotB`, if(sum(isBad > 0), 1, 0) as isBad, sum(`On Hand`) as OH FROM PSOHCSV LEFT JOIN `PSOHCSV_flagged_bad_runs` on `PSOHCSV_flagged_bad_runs`.`Run`=`PSOHCSV`.`Run#` WHERE `Grade Desc` = '" . $params[1] . "' and `Var Desc` = '".$params[0]."' and `Size Desc` = '".$params[2]."'  and rtrim(`Location Desc`) != 'Missing' GROUP BY `LotB` Order by isBad Desc");
        while ($resultArray = mysqli_fetch_assoc($query)) {
            if($resultArray['isBad'] == 1)
            {
                $amount = mysqli_fetch_array(mysqli_query($mysqli, "SELECT format(sum(`On Hand`),0) as badAmt FROM `PSOHCSV` JOIN `PSOHCSV_flagged_bad_runs` on `PSOHCSV_flagged_bad_runs`.`Run`=`PSOHCSV`.`Run#` WHERE `Grade Desc` = '" . $params[1] . "' and `Var Desc` = '".$params[0]."'  and rtrim(`Location Desc`) != 'Missing' and `Size Desc` = '".$params[2]."' and `LotB`='".$resultArray['LotB']."' GROUP BY `LotB`"));
            }
            array_push($array, array('text' => "<span class='mdl-badge mdl-badge--no-background ".($resultArray['isBad'] == 1 ? (($amount['badAmt'] / $resultArray['OH'] * 100) < 90 ? 'bad-group' : 'bad-run') : '')."' data-badge='".$resultArray['OH']."'>".$resultArray['LotB'].($resultArray['isBad'] == 1 ? " (".round($amount['badAmt'] / $resultArray['OH'] * 100, 2)."% bad) " : '')."</span>", 'children' => true, 'id' => $_GET['id'] . ":" . 'LotB:' . $resultArray['LotB']));
        }
        echo json_encode($array);
    } //load Grower of fruit
    else if (count($params) == 4) {
        $query = mysqli_query($mysqli, "SELECT `Grower` AS `Grower`, if(sum(isBad > 0), 1, 0) as isBad, sum(`On Hand`) as OH FROM PSOHCSV LEFT JOIN `PSOHCSV_flagged_bad_runs` on `PSOHCSV_flagged_bad_runs`.`Run`=`PSOHCSV`.`Run#` WHERE `Grade Desc` = '" . $params[1] . "' and `Var Desc` = '".$params[0]."' and `Size Desc` = '".$params[2]."' and `LotB` = '".$params[3]."'  and rtrim(`Location Desc`) != 'Missing' GROUP BY `Grower` Order by isBad Desc");
        while ($resultArray = mysqli_fetch_assoc($query)) {
            if($resultArray['isBad'] == 1)
            {
                $amount = mysqli_fetch_array(mysqli_query($mysqli, "SELECT format(sum(`On Hand`),0) as badAmt FROM `PSOHCSV` JOIN `PSOHCSV_flagged_bad_runs` on `PSOHCSV_flagged_bad_runs`.`Run`=`PSOHCSV`.`Run#` WHERE `Grade Desc` = '" . $params[1] . "' and `Var Desc` = '".$params[0]."'  and rtrim(`Location Desc`) != 'Missing' and `Size Desc` = '".$params[2]."' and `LotB` = '".$params[3]."' and `Grower`='".$resultArray['Grower']."' GROUP BY `Size`"));
            }
            array_push($array, array('text' => "<span class='mdl-badge mdl-badge--no-background ".($resultArray['isBad'] == 1 ? (($amount['badAmt'] / $resultArray['OH'] * 100) < 90 ? 'bad-group' : 'bad-run') : '')."' data-badge='".$resultArray['OH']."'>".$resultArray['Grower'].($resultArray['isBad'] == 1 ? " (".round($amount['badAmt'] / $resultArray['OH'] * 100, 2)."% bad) " : '')."</span>", 'children' => true, 'id' => $_GET['id'] . ":" . 'Grower:' . $resultArray['Grower']));
        }
        echo json_encode($array);
    } //load runs
    else if (count($params) == 5) {
        $query = mysqli_query($mysqli, "SELECT PSOHCSV.`Date`, `Run#`, `LotA`, `LotB`, `LotC`, if(sum(isBad > 0), 1, 0) as isBad, format(sum(`On Hand`),0) as OH FROM PSOHCSV LEFT JOIN `PSOHCSV_flagged_bad_runs` on `PSOHCSV_flagged_bad_runs`.`Run`=`PSOHCSV`.`Run#` WHERE `Grade Desc` = '" . $params[1] . "' and `Var Desc` = '".$params[0]."' and `Size Desc` = '".$params[2]."' and `LotB` = '".$params[3]."' and `Grower` = '".$params[4]."' and rtrim(`Location Desc`) != 'Missing' GROUP BY `Run#` Order by isBad Desc") or die(error_log(mysqli_error($mysqli)));
        while ($resultArray = mysqli_fetch_assoc($query)) {
            array_push($array, array('a_attr' => array('href' => 'API/markBadRun.php?run='.$resultArray['Run#']),'icon' => ($resultArray['isBad'] == 0 ? '/production/scripts/themes/default/check.png' : '/production/scripts/themes/default/cross.png'), 'text' => "<span class='mdl-badge mdl-badge--no-background ".($resultArray['isBad'] == 1 ? 'bad-run' : '')."' data-badge='".$resultArray['OH']."'>".$resultArray['Date']." | Run:".$resultArray['Run#']." | ".$resultArray['LotA']." | ".$resultArray['LotB']." | ".$resultArray['LotC']."</span>", 'id' => $_GET['id'] . ":" . 'Run:' . $resultArray['Run#']));
        }
        echo json_encode($array);
    }
}