<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/20/17
 * Time: 9:58 AM
 */
require '../../config.php';
$userinfo = packapps_authenticate_user('maintenance');
if($userinfo['permissionLevel'] > 2){
    $statuses = array(
        'created' => array('index' => 0, 'flagColumnName' => null, 'usernameColumnName' => 'createdBy', 'dateColumnName' => 'dateCreated', 'displayName' => 'New', 'isLast' => false ),
        'confirmed' => array('index' => 1, 'flagColumnName' => 'isConfirmed', 'usernameColumnName' => 'confirmedBy', 'dateColumnName' => 'dateConfirmed', 'displayName' => 'Confirmed', 'isLast' => false ),
        'inprogress' => array('index' => 2, 'flagColumnName' => 'isInProgress', 'usernameColumnName' => 'inProgressBy', 'dateColumnName' => 'dateInProgress', 'displayName' => 'In Progress', 'isLast' => false ),
        'completed' => array('index' => 3, 'flagColumnName' => 'isCompleted', 'usernameColumnName' => 'completedBy', 'dateColumnName' => 'dateCompleted', 'displayName' => 'Completed', 'isLast' => true )
    );
    if ($_POST['direction'] > 0){
        $isIncrement = 1;
    } else {
        $isIncrement = 0;
    }
    $issueid = mysqli_real_escape_string($mysqli, $_POST['issue']);
    //determine Current State
    $issue = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT isConfirmed, isInProgress, isCompleted FROM maintenance_issues WHERE issue_id = $issueid"));
    $status = 'created';
    if($issue['isConfirmed'] > 0){
        $status = "confirmed";
    }
    if($issue['isInProgress'] > 0){
        $status = "inprogress";
    }
    if($issue['isCompleted'] > 0){
        $status = "completed";
    }
    $curIndex = $statuses[$status]['index'];
    if($isIncrement == 1){
        //increment the status by one
        //bounds check
        if($statuses[$status]['isLast']) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 UNAUTHORIZED', true, 403);
            die();
        }
        //increment and find new value
        $curIndex++;
        foreach($statuses as $key=>$value){
            if($value['index'] == $curIndex){
                $status = $key;
                break;
            }
        }
        if($statuses[$status]['isLast']) {
            if(isset($_POST['solDesc'])){
                $solutionDescription = mysqli_real_escape_string($mysqli, $_POST['solDesc']);
                mysqli_query($mysqli, "UPDATE maintenance_issues SET solution_description = '$solutionDescription'");
            }
        }
        //update all fields in db
        mysqli_query($mysqli, "UPDATE maintenance_issues SET ".$statuses[$status]['flagColumnName']."=1, ".$statuses[$status]['usernameColumnName']."='".$userinfo['username']."', ".$statuses[$status]['dateColumnName']."=NOW() WHERE issue_id = $issueid");
        echo json_encode(array('display' => $statuses[$status]['displayName'], 'isLast' => $statuses[$status]['isLast'], 'isFirst' => $statuses[$status]['index'] == 0));
    } else {
        //decrement status
        //bounds check
        if($statuses[$status]['index'] == 0){
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 UNAUTHORIZED', true, 403);
            die();
        }
        //decrement and find new value
        //update all fields in db
        mysqli_query($mysqli, "UPDATE maintenance_issues SET ".$statuses[$status]['flagColumnName']."=0, ".$statuses[$status]['usernameColumnName']."=null, ".$statuses[$status]['dateColumnName']."=0 WHERE issue_id = $issueid");
        echo json_encode(array('display' => $statuses[$status]['displayName'], 'isLast' => $statuses[$status]['isLast'], 'isFirst' => $statuses[$status]['index'] == 0));
    }
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 UNAUTHORIZED', true, 403);
    die();
}
