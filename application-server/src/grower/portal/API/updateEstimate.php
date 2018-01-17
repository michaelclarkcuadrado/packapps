<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 1/11/18
 * Time: 12:57 PM
 */

include '../../../config.php';
$userinfo = packapps_authenticate_grower();
if(isset($_GET['sameAsLastYear']) && isset($_GET['PK'])){
    //set is same as last year flag
    $sameAsLastYear = 0;
    if($_GET['sameAsLastYear'] != '0'){
        $sameAsLastYear = 1;
    }
    $PK = mysqli_real_escape_string($mysqli, $_GET['PK']);
    $growerCode = $userinfo['GrowerCode'];
    mysqli_query($mysqli, "
        UPDATE `grower_crop-estimates`
    JOIN grower_farms g ON `grower_crop-estimates`.farmID = g.farmID
    JOIN grower_GrowerLogins L ON g.growerID = L.GrowerID
    SET isSameAsLastYear = '$sameAsLastYear' WHERE PK = '$PK' AND GrowerCode = '$growerCode';
    ");
} elseif (isset($_GET['bushelVal']) && isset($_GET['PK'])){
    $growerCode = $userinfo['GrowerCode'];
    $PK = mysqli_real_escape_string($mysqli, $_GET['PK']);
    $bushelVal = mysqli_real_escape_string($mysqli, $_GET['bushelVal']);
    $curYear = date('Y');
    mysqli_query($mysqli, "
    UPDATE grower_block_bushel_history
    JOIN `grower_crop-estimates` ON grower_block_bushel_history.block_PK = `grower_crop-estimates`.PK
    JOIN grower_farms g ON `grower_crop-estimates`.farmID = g.farmID
    JOIN grower_GrowerLogins L ON g.growerID = L.GrowerID
SET bushel_value = '$bushelVal' WHERE year = '$curYear' AND GrowerCode = '$growerCode';
    ");
    mysqli_query($mysqli, "INSERT INTO grower_crop_estimates_changes_timeseries (`block_PK`, `date_Changed`, `cropYear`, `belongs_to_Grower`, `changed_by`, `new_bushel_value`) VALUES ('$PK', default, '$curYear', '" . $userinfo['GrowerCode'] . "', '" . $userinfo['GrowerCode']  . "', '$bushelVal')");
} else {
    APIFail('Required params not sent.');
}