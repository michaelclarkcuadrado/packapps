<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/11/17
 * Time: 11:32 AM
 */
require '../../config.php';
$userinfo = packapps_authenticate_user("maintenance");
if(isset($_GET['i'])){
    $issue = mysqli_real_escape_string($mysqli, $_GET['i']);
    error_log($issue);
    mysqli_query($mysqli, "UPDATE maintenance_issues SET hasPhotoAttached=0 WHERE issue_id='$issue'");
    packapps_deleteFromS3($availableBuckets['maintenance'], "issue-photo-".$issue.".jpg");
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 INTERNAL SERVER ERROR', true, 500);
    die();
}