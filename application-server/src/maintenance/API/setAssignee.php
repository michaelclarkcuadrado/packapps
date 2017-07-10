<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/10/17
 * Time: 3:20 PM
 */
require '../../config.php';
$userinfo = packapps_authenticate_user('maintenance');
if($userinfo['permissionLevel'] > 2 && isset($_POST['name']) && isset($_POST['issueID'])){
    $issueID = mysqli_real_escape_string($mysqli, $_POST['issueID']);
    $name = mysqli_real_escape_string($mysqli, $_POST['name']);
    mysqli_query($mysqli, "UPDATE maintenance_issues SET assignedTo='$name' WHERE issue_id='$issueID'");
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 UNAUTHORIZED', true, 403);
    die();
}