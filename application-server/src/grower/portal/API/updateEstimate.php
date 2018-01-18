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
    //set is same as last year flag, and reset estimate to default prediction
    $isSameAsLastYear = 0;
    if($_GET['sameAsLastYear'] == 1){
        $isSameAsLastYear = 1;
    }
    $PK = mysqli_real_escape_string($mysqli, $_GET['PK']);
    //take growercode for security
    $growerCode = $userinfo['GrowerCode'];
    mysqli_query($mysqli, "
        UPDATE `grower_crop-estimates`
    JOIN grower_farms g ON `grower_crop-estimates`.farmID = g.farmID
    JOIN grower_GrowerLogins L ON g.growerID = L.GrowerID
    JOIN grower_block_bushel_history h ON `grower_crop-estimates`.PK = h.block_PK
    JOIN (SELECT block_PK, bushel_value AS 'last_year_value' FROM grower_block_bushel_history WHERE year = YEAR(NOW()) -1 AND value_type = 'est') t1 ON t1.Block_PK = h.Block_PK
    SET isSameAsLastYear = '$isSameAsLastYear', bushel_value = t1.last_year_value
    WHERE `grower_crop-estimates`.PK = '$PK' AND GrowerCode = '$growerCode';
    ");
} elseif (isset($_GET['bushelVal']) && isset($_GET['PK'])){
    //set estimate to value, set isSameAsLastYear to false
    $growerCode = $userinfo['GrowerCode'];
    $PK = mysqli_real_escape_string($mysqli, $_GET['PK']);
    $bushelVal = intval(mysqli_real_escape_string($mysqli, $_GET['bushelVal']));
    mysqli_query($mysqli, "
    
UPDATE grower_block_bushel_history
    JOIN `grower_crop-estimates` ON grower_block_bushel_history.block_PK = `grower_crop-estimates`.PK
    JOIN grower_farms g ON `grower_crop-estimates`.farmID = g.farmID
    JOIN grower_GrowerLogins L ON g.growerID = L.GrowerID
SET bushel_value = '$bushelVal', isSameAsLastYear = 0 WHERE PK = '$PK' AND value_type = 'est' AND year = YEAR(NOW()) AND GrowerCode = '$growerCode';
    ");
    mysqli_query($mysqli, "INSERT INTO grower_crop_estimates_changes_timeseries (`block_PK`, `date_Changed`, `cropYear`, `belongs_to_Grower`, `changed_by`, `new_bushel_value`) VALUES ('$PK', default, '$curYear', '" . $userinfo['GrowerCode'] . "', '" . $userinfo['GrowerCode']  . "', '$bushelVal')");
} else {
    APIFail('Required params not sent.');
}