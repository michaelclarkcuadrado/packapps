<?php

//Internal packapps variables
$packapps_version = 2;
//companyName is user presentable, companyShortName is internal slug and the deployment ID
$companyShortName = getenv('COMPANY_SLUG_NAME');
$companyName = getenv('COMPANY_LONG_NAME');

//security key to encrypt cookies with, if changed everyone will log out
//seperate user and grower keys to prevent a logged in grower from appearing logged into packapps, and vice versa
//as user user accounts for both are seperate and incompatible
$securityKey = getenv('PACKAPPS_SECURITY_KEY');
$growerSecurityKey = getenv('PACKAPPS_GROWER_SECURITY_KEY');

//MYSQL Server Details
$dbhost = "database-server";
$dbusername = "packapps";
$dbpassword = "packapps";
$dbport = "3306";
$operationsDatabase = 'operationsData';

//AWS S3 buckets
$availableBuckets = array(
    'purchasing' => 'packapps-purchasing-assets',
    'quality' => 'packapps-quality-uploadedimages',
    'backup' => 'packapps-sqldump-backups',
    'maintenance' => 'packapps-maintenance-photos');
//To access a public obj, prepend bucket name and append object key.
$amazonAWSURL = '.s3.amazonaws.com/';

//QA SMTP Server Details
$smtpHost = 'smtp.gmail.com';
$smtpUser = 'michael@packercloud.com';
$smtpPassword = 'gglo xjiy nwou ghpx';
$smtpPort = '587';
//Not respected by Gsuite
$smtpSendAs = 'notifications@packercloud.com';


//do db connection
$mysqli = mysqli_connect($dbhost, $dbusername, $dbpassword, $operationsDatabase, $dbport);
if (mysqli_connect_errno()) {
    die("The Packapps server has experienced an internal issue.<br> Wait a few minutes and try again. If it keeps happening, contact the administrator. the error was: <br><br><b>Failed to connect to MySQL: " . mysqli_connect_error());
}

//check if packapps has been set up and database is current
$systemRow = mysqli_query($mysqli, "SELECT packapps_version, systemInstalled, growerPortalLastInitializedYear FROM packapps_system_info");
$systemRow = mysqli_fetch_assoc($systemRow);
if($_SERVER['SCRIPT_NAME'] != '/installer.php' && $systemRow['systemInstalled'] == 0){
    die("<script>window.location.replace('/installer.php')</script>");
}
if($systemRow['packapps_version'] != $packapps_version){
    die("The Packapps server has experienced an internal issue.<br> If it keeps happening, contact the administrator. the error was: <br><br><b>Database schema does not match code schema. Broken Upgrade.");
}
if($systemRow['growerPortalLastInitializedYear'] !== date('Y')){
    require_once 'packapps_api.php';
    error_log('INCREMENTING YEAR!');
    incrementGrowerPortalEstimatesYear($mysqli);
}

require_once 'packapps_api.php';
