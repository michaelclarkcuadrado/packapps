<?php
/**
 * Created by PhpStorm.
 * User: MAC
 * Date: 5/11/2015
 * Time: 12:36 PM
 */
$mysqli = mysqli_connect("localhost", "ricefruit", "ricefruit", "growerReporting");
$adminauth = mysqli_query($mysqli, "SELECT isAdmin FROM GrowerData WHERE GrowerCode='" . $_SERVER['PHP_AUTH_USER'] . "'");
$admin = mysqli_fetch_array($adminauth);
$blockdata = mysqli_query($mysqli, "SELECT `Comm Desc`,VarDesc,FarmDesc,BlockDesc,`Str Desc`,format(".(date('Y')-5)."act,0),format(".(date('Y')-4)."act,0),format(".(date('Y')-3)."act,0),format(".(date('Y')-2)."act,0),format(".(date('Y')-1)."act,0),format(".date('Y')."est,0) FROM `crop-estimates` WHERE Grower='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "' AND isDeleted = '0' ORDER BY `Comm Desc`, VarDesc, FarmDesc, BlockDesc, `Str Desc` ASC;");
if (!$blockdata) {
    echo "Oops. There seems to be an error. Try again later.";
} else {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Orchard_Report_' . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . '_' . date("m-d-Y") . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, explode('&', "This file is for your records. All estimates should be submitted through the grower portal at https://grower.ricefruit.com/"));
    fputcsv($output, explode('&', " "));
    fputcsv($output, array('Commodity', 'Variety', 'Farm', 'Block', 'Strain', (date('Y')-5).' Received', (date('Y')-4).' Received', (date('Y')-3).' Received', (date('Y')-2).' Received', (date('Y')-1).' Received', date('Y').' Current Estimate'));
    while ($blockarray = mysqli_fetch_assoc($blockdata)) fputcsv($output, $blockarray);
}