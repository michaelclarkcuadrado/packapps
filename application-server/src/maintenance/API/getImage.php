<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/11/17
 * Time: 8:02 AM
 */
require '../../config.php';
$userinfo = packapps_authenticate_user('maintenance');
$ID = $_GET['i'];
$result = packapps_downloadFromS3($availableBuckets['maintenance'], 'issue-photo-'.$ID.'.jpg');
header("Content-Type: ".$result['ContentType']);
header('Content-Disposition: attachment; filename="maintenance-issue-".$ID.".jpg"');
echo $result['Body'];