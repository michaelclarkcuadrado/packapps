<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 6/28/17
 * Time: 3:36 PM
 */
/**
 * Returns all maintenance issues in the system. Optionally takes a json filter to control output
 */

require_once '../../config.php';
$userInfo = packapps_authenticate_user('maintenance');
$queryText = "SELECT maintenance_issues.issue_id, upper(Purpose) as Purpose, title, issue_description, createdByUser.`Real Name` as createdBy, DATE_FORMAT(dateCreated,'%c/%e/%Y %H:%i%p') AS dateCreated,
                      isConfirmed, confirmedByUser.`Real Name` AS confirmedBy, DATE_FORMAT(dateConfirmed,'%c/%e/%Y %H:%i%p') as dateConfirmed, isInProgress, inProgressUser.`Real Name` as inProgressBy,
                       DATE_FORMAT(DateInProgress, '%c/%e/%Y %H:%i%p') as DateInProgress, isCompleted, completedByUser.`Real Name` As completedBy, DATE_FORMAT(dateCompleted,'%c/%e/%Y %H:%i%p') as dateCompleted, 
                            solution_description, IFNULL(assignedToUser.`Real Name`, 'Unassigned') as assignedTo, Location, hasPhotoAttached, needsParts, 
                            purchasing_Items.Item_ID as NeededItemID, purchasing_Items.ItemDesc as NeededItemDesc, maintenance_issues2purchasing_items.numberNeeded as NeededItemQty
                            FROM maintenance_issues 
                              LEFT JOIN maintenance_purposes 
                                ON maintenance_issues.purpose_id = maintenance_purposes.purpose_id 
                              LEFT JOIN maintenance_issues2purchasing_items 
                                ON maintenance_issues.issue_id = maintenance_issues2purchasing_items.issue_id 
                              LEFT JOIN packapps_master_users createdByUser
                                ON maintenance_issues.createdBy = createdByUser.username
                              LEFT JOIN packapps_master_users confirmedByUser
                                ON maintenance_issues.confirmedBy = confirmedByUser.username
                              LEFT JOIN packapps_master_users inProgressUser
                                ON maintenance_issues.inProgressBy = inProgressUser.username
                              LEFT JOIN packapps_master_users completedByUser
                                ON maintenance_issues.completedBy = completedByUser.username
                              LEFT JOIN packapps_master_users assignedToUser
                                ON maintenance_issues.assignedTo = assignedToUser.username
                              LEFT JOIN purchasing_Items
                                ON maintenance_issues2purchasing_items.part_id = purchasing_Items.Item_ID";

//Apply filters on issues returned, by building a query string.
if(isset($_GET['filterJson'])){
    $filter = json_decode($_GET['filterJson'], true);
    if(empty($filter['purposes']) && empty($filter['statuses']) && $filter['assignments']['unassigned'] == false && $filter['assignments']['assignedto'] == false && $filter['assignments']['assigntoself'] == false){
        $queryText .= ';';
    } else {
        $queryText .= " WHERE";
        $isFirstWhereClause = true;
        //insert purposes into query
        $isFirstWhereClause = true;
        foreach($filter['purposes'] as $purpose) {
            if ($isFirstWhereClause) {
                $queryText .= " (";
            } else {
                $queryText .= " OR";
            }
            $purpose = mysqli_real_escape_string($mysqli, $purpose);
            $queryText .= ' maintenance_issues.purpose_id=' . $purpose;
            $isFirstWhereClause = false;
        }
        if(!$isFirstWhereClause){
            $queryText .= ")";
        }
        //insert statuses clauses
        $isFirstStatus = true;
        foreach($filter['statuses'] as $status){
            if(!$isFirstStatus){
                $queryText .= " OR";
            } else {
                if(!$isFirstWhereClause){
                    $queryText .= " AND (";
                } else {
                    $queryText .= " (";
                }
            }
            $status = mysqli_real_escape_string($mysqli, $status);
            if($status == 'new'){
                $queryText .= " (maintenance_issues.isConfirmed = 0 AND maintenance_issues.isInProgress = 0 AND maintenance_issues.isCompleted = 0)";
            } elseif ($status == 'confirmed'){
                $queryText .= " (maintenance_issues.isConfirmed = 1 AND maintenance_issues.isInProgress = 0 AND maintenance_issues.isCompleted = 0)";
            } elseif ($status == 'inprogress'){
                $queryText .= " (maintenance_issues.isConfirmed = 1 AND maintenance_issues.isInProgress = 1 AND maintenance_issues.isCompleted = 0)";
            } elseif ($status == 'completed') {
                $queryText .= " (maintenance_issues.isConfirmed = 1 AND maintenance_issues.isInProgress = 1 AND maintenance_issues.isCompleted = 1)";
            } else {
                die("Invalid filter requested.");
            }
            $isFirstStatus = false;
            $isFirstWhereClause = false;
        }
        if (!$isFirstStatus){
            $queryText .= ")";
        }
        //insert assignments clauses
        $isFirstAssignment = true;
            if($filter['assignments']['unassigned'] == true){
                if(!$isFirstAssignment){
                    $queryText .= " OR";
                } else {
                    if(!$isFirstWhereClause){
                        $queryText .= " AND";
                    } else {
                        $queryText .= " (";
                    }
                }
                $queryText .= " maintenance_issues.assignedTo IS NULL";
                $isFirstAssignment = false;
            }
        if($filter['assignments']['assignedto'] == true){
            $name = mysqli_real_escape_string($mysqli, $filter['assignments']['assignedtoname']);
            if(!$isFirstAssignment){
                $queryText .= " OR";
            } else {
                if(!$isFirstWhereClause){
                    $queryText .= " AND";
                } else {
                    $queryText .= " (";
                }
            }
            $queryText .= " maintenance_issues.assignedTo LIKE '%".$name."%'";
            $isFirstAssignment = false;
        }
        if($filter['assignments']['assignedtoself'] == true){
            $name = $userInfo['Real Name'];
            if(!$isFirstAssignment){
                $queryText .= " OR";
            } else {
                if(!$isFirstWhereClause){
                    $queryText .= " AND";
                } else {
                    $queryText .= " (";
                }
            }
            $queryText .= " maintenance_issues.assignedTo = '".$name."'";
            $isFirstAssignment = false;
        }
        if(!$isFirstAssignment){
            $queryText .= ")";
        }
    }
}

$issuesReturned = mysqli_query($mysqli, $queryText);

//create array where issues are grouped together
//partsNeeded is an array of ItemID => ItemDesc, needsParts is a bool and is 0 if doesn't need any parts
$issueArray = array();
while($issue = mysqli_fetch_assoc($issuesReturned)){
    if(array_key_exists($issue['issue_id'], $issueArray)){
        $issueArray[$issue['issue_id']]['partsNeeded'][$issue['NeededItemID']] = array('NeededItemDesc' => $issue['NeededItemDesc'], 'NeededItemQty' => $issue['NeededItemQty']);
    } else {
        if($issue['assignedTo'] == null){
            $issue['assignedTo'] = 'Unassigned';
        }
        if($issue['needsParts'] > 0){
            $issue['partsNeeded'] = array($issue['NeededItemID'] => array('NeededItemDesc' => $issue['NeededItemDesc'], 'NeededItemQty' => $issue['NeededItemQty']));
        }
        unset($issue['NeededItemID'], $issue['NeededItemDesc'], $issue['NeededItemQty']);
        $issueArray[$issue['issue_id']] = $issue;
    }
}
header('Content-type: application/json');
echo json_encode($issueArray);