<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 8/5/2016
 * Time: 2:35 PM
 */
//authentication
include 'config.php';
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT `Real Name`, username, isSystemAdministrator FROM packapps_master_users WHERE `username` = '$SecuredUserName'"));
    if ($checkAllowed['isSystemAdministrator'] == 0) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 FORBIDDEN', true, 403);
        die();
    } else {
        //code adapted from controlPanel.php
        //enumerate packapps
        $packapps_query = mysqli_query($mysqli, "SELECT short_app_name, long_app_name FROM packapps_appProperties WHERE isEnabled = 1");
        $installedPackapps = array();
        while($packapp = mysqli_fetch_array($packapps_query)){
            array_push($installedPackapps, $packapp);
        }

        //create userListPrivileges query
        $userListPrivileges = "SELECT concat(`Real Name`, CASE WHEN isSystemAdministrator > 0 THEN ' **' ELSE '' END) as `Real Name`,packapps_master_users.username, ifnull(DATE_FORMAT(lastLogin,'%b %d %Y %h:%i %p'), 'Never') as lastLogin, isSystemAdministrator, isDisabled";
        //add table fields to query
        foreach($installedPackapps as $packapp){
            $userListPrivileges .= ", ".$packapp['short_app_name']."_UserData.Role+0 AS ".$packapp['short_app_name']."Role";
            $userListPrivileges .= ", allowed".ucfirst($packapp['short_app_name']);
        }
        $userListPrivileges .= " FROM packapps_master_users";
        //add table joins to query
        foreach($installedPackapps as $packapp){
            $userListPrivileges .= " LEFT JOIN ".$packapp['short_app_name']."_UserData ON packapps_master_users.username=".$packapp['short_app_name']."_UserData.UserName";
        }
        $userListPrivileges = mysqli_query($mysqli, $userListPrivileges);
        $arrayToReturn = array();
        while($user = mysqli_fetch_assoc($userListPrivileges)){
            array_push($arrayToReturn, $user);
        }
        echo json_encode($arrayToReturn);
    }
}
