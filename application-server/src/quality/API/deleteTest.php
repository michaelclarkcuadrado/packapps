<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 8/22/2016
 * Time: 8:15 AM
 */
include '../../config.php';
if (isset($_GET['testType']) && isset($_GET['testID'])) {
    $ID = mysqli_real_escape_string($mysqli, $_GET['testID']);
    if($_GET['testType'] == '(Pre) Run') {
        mysqli_query($mysqli, "UPDATE production_runs SET isPreInspected = 0 WHERE RunNumber = '$ID'");
        $runNum = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT RunID FROM production_runs WHERE RunNumber = '$ID'"))['RunID'];
        mysqli_query($mysqli, "DELETE FROM quality_run_inspections WHERE RunID = '$runNum' AND isPreInspection > 0");
    } elseif ($_GET['testType'] == 'Run') {
        mysqli_query($mysqli, "UPDATE production_runs SET isQA = 0 WHERE RunNumber = '$ID'");
        $runNum = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT RunID FROM production_runs WHERE RunNumber = '$ID'"))['RunID'];
        mysqli_query($mysqli, "DELETE FROM quality_run_inspections WHERE RunID = '$runNum' AND isPreInspection = 0");
        unlink('../assets/uploadedimages/runs/'.$ID.'.jpg');
    } elseif ($_GET['testType'] == 'RT') {
        mysqli_query($mysqli, "DELETE FROM quality_InspectedRTs WHERE RTNum='" . $ID . "'");
        exec("rm ../assets/uploadedimages/" . $ID . ".jpg ../assets/uploadedimages/" . $ID . "starch.jpg ../assets/uploadedimages/" . $ID . "bitterpit.jpg ../assets/uploadedimages/" . $ID . "bruising.jpg");
    }
}