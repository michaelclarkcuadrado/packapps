<?php
//MYSQL Server Details
$dbhost = "p:localhost";
$dbusername = "ricefruit";
$dbpassword = "ricefruit";
$operationsDatabase = 'operationsData';
$growerDB = 'growerReporting';
$companyName = 'Rice Fruit';

//analytics
$piwikHost = '//grower.ricefruit.com/analytics';
$piwikUser = 'ricefruit';
$piwikPassword = 'r1cefru1t';


$mysqli = mysqli_connect($dbhost, $dbusername, $dbpassword, $growerDB);

if (mysqli_connect_errno($mysqli)) {
    die("Failed to connect to MySQL: " . mysqli_connect_error() . "<br>Try again. If it keeps happening, reboot the server.");
}
