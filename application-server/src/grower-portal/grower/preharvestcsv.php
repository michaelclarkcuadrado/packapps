<?php
/**
 * Created by PhpStorm.
 * User: MAC
 * Date: 8/13/2015
 * Time: 3:05 PM
 */
include '../config_grower.php';
$adminauth = mysqli_query($mysqli, "SELECT isAdmin FROM GrowerData WHERE GrowerCode='" . $_SERVER['PHP_AUTH_USER'] . "'");
$admin = mysqli_fetch_array($adminauth);
$totaltestdata = mysqli_query($mysqli, "SELECT Preharvest_Samples.PK, CASE WHEN Retain=0 THEN 'No' ELSE 'Yes' END AS Retain, date_format(`Date`, '%d-%b-%Y') AS Date, round(avg(((Pressure1)+(Pressure2))/2),3) AS Pressure , round(avg(Brix),1) AS Brix, round(stddev_pop(Brix),1) AS stddevBrix, round(avg(Weight),3) AS Weight, round(avg(Starch),1) AS Starch, round(avg(DAAverage),2) AS DA, round(stddev_pop(DA),2) AS stddevDA, concat(left(Preharvest_Samples.Inspector, (instr(Preharvest_Samples.Inspector,' ')+1)), '.') AS Inspector, CASE WHEN Notes='' THEN 'No Comment' ELSE Notes END AS Notes, `FarmDesc`, BlockDesc, VarDesc, `Comm Desc`, `Str Desc` AS StrDesc FROM Preharvest_Samples JOIN `crop-estimates`ON Preharvest_Samples.PK=`crop-estimates`.PK WHERE date_format(`Date`, '%e-%b-%Y')='" . $_GET['Date'] . "' AND Preharvest_Samples.PK=" . $_GET['ID'] . " GROUP BY Preharvest_Samples.PK, DATE(`Date`) ORDER BY DATE DESC;");
$totaltestarray = mysqli_fetch_assoc($totaltestdata);

$sampledata = mysqli_query($mysqli, "SELECT SampleNum, Pressure1, Pressure2, Brix, Starch, Weight, DA, DA2 FROM Preharvest_Samples WHERE Preharvest_Samples.Grower='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "' AND date_format(`Date`, '%e-%b-%Y')='" . $_GET['Date'] . "' AND PK='" . $_GET['ID'] . "' ORDER BY SampleNum");
$namecnct = mysqli_query($mysqli, "SELECT GrowerName FROM `GrowerData` WHERE GrowerCode='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "' LIMIT 1");
$growername = mysqli_fetch_array($namecnct);

//generate csv
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Pre-Harvest_Results_From_Block#' . $_GET['ID'] . '_' . $_GET['Date'] . '.csv');
$output = fopen('php://output', 'w');
fputcsv($output, explode('%%', $companyName));
fputcsv($output, explode('%%', "Pre-Harvest Report"));
fputcsv($output, explode('%%', ""));
fputcsv($output, explode('%%', 'Prepared for:%%' . $growername[0] . "%%Date:%%" . $totaltestarray['Date']));
fputcsv($output, explode('%%', ""));
fputcsv($output, explode('%%', "%%Grower%%Farm%%Block%%Variety%%Strain%%ReTain"));
fputcsv($output, array("", ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])), $totaltestarray['FarmDesc'], $totaltestarray['BlockDesc'], $totaltestarray['VarDesc'], $totaltestarray['StrDesc'], $totaltestarray['Retain']));
fputcsv($output, explode('%%', ""));
fputcsv($output, explode('%%', ""));
fputcsv($output, array("Sample Number", "Pressure A", "Pressure B", "Brix", "Starch", "Weight", "DA", "DA2"));
while ($samplearray = mysqli_fetch_assoc($sampledata)) {
    fputcsv($output, $samplearray);
}
fputcsv($output, explode('%%', ""));
fputcsv($output, explode('%%', ""));
fputcsv($output, explode('%%', ""));
fputcsv($output, explode('%%', "Avg. Pressure%%Avg. Brix%%Std. Dev. Brix%%Avg. Weight%%Avg. Starch%%Avg. DA%%Std. Dev. DA"));
fputcsv($output, array($totaltestarray['Pressure'], $totaltestarray['Brix'], $totaltestarray['stddevBrix'], $totaltestarray['Weight'], $totaltestarray['Starch'], $totaltestarray['DA'], $totaltestarray['stddevDA']));
fputcsv($output, explode('%%', ""));
fputcsv($output, explode('%%', ""));
fputcsv($output, array("Notes:", $totaltestarray['Notes']));
fputcsv($output, explode('%%', ""));
fputcsv($output, array("Inspected By:", $totaltestarray['Inspector']));
