<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 8/22/2016
 * Time: 8:15 AM
 */
include '../../config.php';
$userData = packapps_authenticate_user('quality');
if ($userData['permissionLevel'] > 2) {
    if (isset($_GET['testType']) && isset($_GET['testID'])) {
        $ID = mysqli_real_escape_string($mysqli, $_GET['testID']);
        if ($_GET['testType'] == '(Pre) Run') {
            mysqli_query($mysqli, "UPDATE production_runs SET isPreInspected = 0 WHERE RunNumber = '$ID'");
            $runNum = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT RunID FROM production_runs WHERE RunNumber = '$ID'"))['RunID'];
            mysqli_query($mysqli, "DELETE FROM quality_run_inspections WHERE RunID = '$runNum' AND isPreInspection > 0");
        } elseif ($_GET['testType'] == 'Run') {
            mysqli_query($mysqli, "UPDATE production_runs SET isQA = 0 WHERE RunNumber = '$ID'");
            $runNum = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT RunID FROM production_runs WHERE RunNumber = '$ID'"))['RunID'];
            mysqli_query($mysqli, "DELETE FROM quality_run_inspections WHERE RunID = '$runNum' AND isPreInspection = 0");
            packapps_deleteFromS3($availableBuckets['quality'], 'runPhoto-runid-' . $ID . '.jpg');
        } elseif ($_GET['testType'] == 'RT') {
            mysqli_query($mysqli, "DELETE FROM quality_InspectedRTs WHERE receiptNum='" . $ID . "'");
            packapps_deleteFromS3($availableBuckets['quality'], 'quality-rtnum-' . $ID . '.jpg');
        }
    }
}