<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 6/3/2016
 * Time: 12:29 PM
 */
require '../config.php';
include_once("Classes/excel_reader2.php");
$xlsdata = new Spreadsheet_Excel_Reader($_FILES['xlsupload']['tmp_name'],false);


    //init vars
    $RunID = $_POST['RunID'];
    $Weight = 0;
    $Pressure1 = 0;
    $Pressure2 = 0;
    $Brix = 0;
    $Note = $_POST['notes'];
    $isPreInspection = $_POST['isPreInspection'];

    if ($_POST['isPreInspection'] > 0)
    {
        mysqli_query($mysqli, "UPDATE production_runs SET isPreInspected = 1 WHERE RunID= '" . $_POST['RunID'] . "'");
    } else {
        mysqli_query($mysqli, "UPDATE production_runs SET isQA = 1 WHERE RunID= '" . $_POST['RunID'] . "'");
    }

    $stmt = mysqli_prepare($mysqli, "INSERT INTO quality_run_inspections VALUES (?, ?, ?, ?, ?, ?, ?, default, default)");
    mysqli_stmt_bind_param($stmt, 'iddddsi', $RunID, $Weight, $Pressure1, $Pressure2, $Brix, $Note, $isPreInspection);

    $NumSamples = ($xlsdata->rowcount()-13)/2;

    for($i = 1; $i <= $NumSamples; $i++)
    {
        $Weight = $xlsdata->val($i*2, 'C');
        $Pressure1 = $xlsdata->val($i*2, 'B');
        $Pressure2 = $xlsdata->val(($i*2)+1, 'B');
        if($i <= 5 && $_POST['brix'.$i] != 0) {
            $Brix = $_POST['brix' . $i];
        } else {
            $Brix = null;
        }
        mysqli_stmt_execute($stmt);
        error_log(mysqli_stmt_error($stmt));
    }
    $stmt->close();
    echo "<script>location.replace('QA.php#runQA')</script>";

