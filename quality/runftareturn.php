<?php
/**
 * Created by PhpStorm.
 * User: MAC
 * Date: 7/20/2015
 * Time: 11:21 AM
 */
//This file parses an XLS file generated by the fruit test suite FTA and returns its data as json
//only supports 5, 10, or 15 samples in a file
include_once("../Classes/excel_reader2.php");
$xlsdata = new Spreadsheet_Excel_Reader($_FILES['0']['tmp_name'],false);

if($xlsdata->rowcount() == 23) {
    //5 samples
    //creates array of weight, press1, press2
    echo json_encode(array_merge(array("NumSamples"=>"5","BlockID"=>$xlsdata->val('18', 'B')),
    array(
    array($xlsdata->val('2', 'C'),$xlsdata->val('2', 'B'),$xlsdata->val('3', 'B')),
    array($xlsdata->val('4', 'C'),$xlsdata->val('4', 'B'),$xlsdata->val('5', 'B')),
    array($xlsdata->val('6', 'C'),$xlsdata->val('6', 'B'),$xlsdata->val('7', 'B')),
    array($xlsdata->val('8', 'C'),$xlsdata->val('8', 'B'),$xlsdata->val('9', 'B')),
    array($xlsdata->val('10', 'C'),$xlsdata->val('10', 'B'),$xlsdata->val('11', 'B')))));
}else{die(header("HTTP/1.1 500 Internal Server Error"));}