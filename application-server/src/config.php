<?php

//Internal packapps variables
$packapps_version = 2;
$isManagedByOrchestration = false;
$orchestraionServerURL = '';
//companyName is user presentable, companyShortName is internal slug and the deployment ID
$companyShortName = 'devenv';
$companyName = 'DEVELOPMENT ENV';

//MYSQL Server Details
$dbhost = "database-server";
//$dbhost = "p:database-server";
$dbusername = "packapps";
$dbpassword = "packapps";
$dbport = "3306";
$operationsDatabase = 'operationsData';

//AWS S3 buckets
$availableBuckets = array(
    'purchasing' => 'packapps-purchasing-assets',
    'quality' => 'packapps-quality-uploadedimages',
    'backup' => 'packapps-sqldump-backups');
//To access a public obj, prepend bucket name and append object key.
$amazonAWSURL = '.s3.amazonaws.com/';

//analytics
$piwikHost = '//grower.ricefruit.com/analytics';
$piwikUser = 'ricefruit';
$piwikPassword = 'r1cefru1t';

//QA SMTP Server Details
$smtpHost = 'smtp.gmail.com';
$smtpUser = 'michael@packercloud.com';
$smtpPassword = 'gglo xjiy nwou ghpx';
$smtpPort = '587';
//Not respected by Gsuite
$smtpSendAs = 'notifications@packercloud.com';

//security key to encrypt cookies with, if changed everyone will log out
//seperate user and grower keys to prevent a logged in grower from appearing logged into packapps, and vice versa
//as user user accounts for both are seperate and incompatible
$securityKey = "lH2OzVPQ0K9/By66YygFosxxszSgET4p/L0l18bpZPXRGCt5U5VCybYaVk9vn15c";
$growerSecurityKey = "/u0HDuaq9WgBvcEsBGeU+j3pZ5Vr5nFu91f+axiQRpYux3t6xFXQvou4Uawu51pN";

//do db connection
$mysqli = mysqli_connect($dbhost, $dbusername, $dbpassword, $operationsDatabase, $dbport);
if (mysqli_connect_errno()) {
    die("The Packapps server has experienced an internal issue.<br> Wait a few minutes and try again. If it keeps happening, contact the administrator. the error was: <br><br><b>Failed to connect to MySQL: " . mysqli_connect_error());
}

//check if packapps has been set up
$systemRow = mysqli_query($mysqli, "SELECT packapps_version, systemInstalled FROM packapps_system_info");
$systemRow = mysqli_fetch_array($systemRow);
if($systemRow['packapps_version'] != $packapps_version){
    die("The Packapps server has experienced an internal issue.<br> If it keeps happening, contact the administrator. the error was: <br><br><b>Database schema does not match code schema. Broken Upgrade.");
}
if($_SERVER['SCRIPT_NAME'] != '/installer.php' && $systemRow['systemInstalled'] == 0){
    die("<script>window.location.replace('/installer.php')</script>");
}
require_once 'packapps_api.php';
