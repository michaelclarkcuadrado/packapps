<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/7/17
 * Time: 10:23 AM
 */
require '../../config.php';
$userinfo = packapps_authenticate_user('maintenance');
if($userinfo['permissionLevel'] < 3){
    die(header($_SERVER['SERVER_PROTOCOL'] . ' 403 UNAUTHORIZED', true, 403));
} else {
    if(isset($_GET['issue'])){
        $ID = mysqli_real_escape_string($mysqli, $_GET['issue']);
        $photoQueryResult = mysqli_query($mysqli, "SELECT hasPhotoAttached FROM maintenance_issues WHERE issue_id = '$ID'");
        if(mysqli_fetch_assoc($photoQueryResult)['hasPhotoAttached'] > 0){
            packapps_deleteFromS3($availableBuckets['maintenance'], $companyShortName.'-issue-photo-'.$ID.'.jpg');
        }
        mysqli_query($mysqli, "DELETE FROM maintenance_issues WHERE issue_id = $ID");
        echo mysqli_error($mysqli);
    } else {
        die(header($_SERVER['SERVER_PROTOCOL'] . ' 403 UNAUTHORIZED', true, 403));
    }
}