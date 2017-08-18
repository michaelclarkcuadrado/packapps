<?php
require '../config.php';

$userData = packapps_authenticate_user('quality');

if ($userData['Role'] !== "QA") {
    die("UNAUTHORIZED");
};
$Note = mysqli_real_escape_string($mysqli, $_POST['Notes']);
$RT = mysqli_real_escape_string($mysqli, $_POST['RT']);

//void RT
if (isset($_GET['del'])) {
    $_GET['del'] = mysqli_real_escape_string($mysqli, $_GET['del']);
    mysqli_query($mysqli, "DELETE FROM quality_InspectedRTs WHERE receiptNum='" . $_GET['del'] . "'");
    packapps_deleteFromS3($availableBuckets['quality'], 'quality-rtnum-'.$_GET['del'].'.jpg');
    echo "<script>location.replace('QA.php?qa=" . $_GET['del'] . " has been <mark>voided</mark> and not #QA')</script>";
} else {
    //insert final inspection info
    mysqli_query($mysqli, "UPDATE `quality_InspectedRTs` SET `Note`='" . $Note . "', `isFinalInspected`='1' WHERE receiptNum='" . $RT . "'");

    //Prepare Statement
    $stmt = mysqli_prepare($mysqli, "UPDATE `quality_AppleSamples` SET `Pressure1`=?, `Pressure2`=?, `Brix`=?, `Weight`=?,`FinalTestedBy`=? WHERE `receiptNum`=? AND SampleNum=?");
    mysqli_stmt_bind_param($stmt, 'ddddsii', $Pressure1, $Pressure2, $Brix, $Weight, $userData['username'], $RT, $Num);
    for ($i = 1; $i < $_POST['NumSamples'] + 1; $i++) {
        $Num = $i;
        $Pressure1 = $_POST['pressure' . $i . '-1'];
        $Pressure2 = $_POST['pressure' . $i . '-2'];
        $Weight = $_POST['weight' . $i];
        if ($_POST['NumSamples'] > 5) {
            $Brix = $_POST['brix' . $i];
        } else {
            $Brix = null;
        }
        mysqli_stmt_execute($stmt);
    }
    echo "<script>location.replace('QA.php?qa=$RT#QA')</script>";
}