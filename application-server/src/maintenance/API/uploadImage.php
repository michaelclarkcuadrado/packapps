<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/19/17
 * Time: 3:08 PM
 */
require '../../config.php';
$userinfo = packapps_authenticate_user('maintenance');
if($userinfo['permissionLevel'] > 2){
    $issue = mysqli_real_escape_string($mysqli, $_POST['issue']);
    packapps_uploadToS3($availableBuckets['maintenance'], $_FILES['picture']['tmp_name'], 'issue-photo-'.$_POST['issue'].".jpg");
    mysqli_query($mysqli, "UPDATE maintenance_issues SET hasPhotoAttached=1 WHERE issue_id='$issue'");
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 UNAUTHORIZED', true, 403);
    die();
}