<?
include '../../config.php';
packapps_authenticate_user('quality');

//pull info
$testID = mysqli_real_escape_string($mysqli, $_POST['ID']);

//move starch photo
if ($_FILES['starchupload']['tmp_name'] != "") {
    $check_uploaded = mysqli_query($mysqli, "SELECT * FROM `grower_Preharvest_tests` WHERE `isStarchInspected`=1 AND test_id = '$testID'");
    if (mysqli_num_rows($check_uploaded) > 0) {
        error_log("Failure, that preharvest test already has starch data.");
        echo "<script>location.replace('mobilestarch.php')</script>";
    }
    $Filename = "quality-preharvest-ID" . $testID . "-starch.jpg";
    packapps_uploadToS3($availableBuckets['quality'], $_FILES['starchupload']['tmp_name'], $Filename);
} else {
    error_log("Failure, no preharvest starch photo uploaded");
    echo "<script>location.replace('mobilestarch.php')</script>";
}

//Prepare Statement
$stmt = mysqli_prepare($mysqli, "UPDATE `grower_Preharvest_Samples` SET `Starch`=? WHERE `test_id`=? AND SampleNum=?");
mysqli_stmt_bind_param($stmt, 'sii', $Starch, $testID, $SampleNum);

//always insert the first 5, execute 10 on conditional
$cap = 5;
if ($_POST['moreappel'] == 'yes') {
    $cap += 5;
}

for ($i = 1; $i <= $cap; $i++) {
    $SampleNum = $i;
    $Starch = $_POST['Starch' . $i];
    mysqli_stmt_execute($stmt);
}
mysqli_query($mysqli, "UPDATE `grower_Preharvest_tests` SET isStarchInspected = 1");

echo "<script>location.replace('mobilestarch.php?ph=true')</script>";

