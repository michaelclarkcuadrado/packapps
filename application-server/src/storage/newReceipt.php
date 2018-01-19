<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 8/22/17
 * Time: 11:47 AM
 */
require '../config.php';
$userData = packapps_authenticate_user('storage')
?>

<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html">
    <meta name="theme-color" content="#e2eef4">
    <title>Mobile QA</title>
    <link rel="stylesheet" type="text/css" media="all" href="../styles-common/inspector.css">
    <link rel="stylesheet" type="text/css" media="all" href="../styles-common/select2.min.css">
    <link rel="stylesheet" href="../styles-common/materialIcons/material-icons.css">
    <script src="../scripts-common/jquery.min.js"></script>
    <script src="../scripts-common/select2.min.js"></script>
</head>

<body>
<div id="wrapper">
    <p style='position: fixed; top: 0; width: 100%; z-index:999'>
        <button onclick="location.replace('mobile.php')"><<< Go back</button>
    </p>

    <h1>New Receipt</h1>
    <br>
    <h2><? echo $companyName ?> Inventory Manager</h2>
    <br>
    <form style="max-width:750px;" id="ReceiptSubmissionForm" action="API/receiveNewReceipt.php" method="post" enctype="multipart/form-data">
        <div id="selectionViewer" style="display:none" class="col-2">
            <label>
                Details:
                <div style="display:table">
                    <input type="text" style="overflow-x: scroll" id="selectionGrower" readonly placeholder="">
                    <input type="text" style="overflow-x: scroll; display: none" id="selectionVariety" readonly placeholder="">
                    <input type="text" style="overflow-x: scroll; display: none" id="selectionBlock" readonly placeholder="">
                    <i id="blockChooserUndo" style="cursor: pointer; float: right" class="material-icons">backspace</i>
                </div>
            </label>
        </div>
        <div id="BlockSel" class="col-2">
            <label><span id="blockChooserTitle">Grower</span>
                <select id="blockChooserSelect">
                    <option></option>
                </select>
            </label>
        </div>
        <div id="binRow1" class="col-3" style="display: flex">
            <div style="width:100%">
                <label>Number of Bins
                    <input type="number" inputmode="numeric" pattern="[0-9]*" min="1" name="numbins1" max="100000" placeholder="0" required></label>
            </div>
            <div style="border-left: 1px solid #e4e4e4;">
                <label>Bushels Per Bin
                <input type="number" inputmode="numeric" pattern="[0-9]*" min="1" name="numbushels1" placeholder="23" max="50"></label>
            </div>
        </div>
        <div class="col-2">
            <label style="margin:auto; width:125">
                <i id="removeButton" class="material-icons">remove_circle</i>
                <i id="addButton" class="material-icons">add_circle</i>
            </label>
        </div>
        <div class="col-2">
            <label>External Reference Number
            <input type="number" inputmode="numeric" pattern="[0-9]*" name="externrefnum" min="0"></label>
        </div>
        <div class="col-submit">
            <button class="submitbtn">Receive Inventory</button>
            <br>
            <label style="border: dashed black 1px; vertical-align: middle">Received
                by <? echo $userData['Real Name'] . " on " . date('l, F jS Y') ?></label>
        </div>
    </form>
</div>
</body>
<script>
    var curNumRows = 1;

    var curgrowerID = null;
    var curvarID = null;
    var curblockID = null;
    var curGrowerName = null;
    var curVarName = null
    var curBlockDesc = null;
    var notready = true;

    var currSelect2 = null;

    $(document).ready(function (event) {
        setupChooseGrower();

        //attach listeners
        $('#addButton').on('click', addRow);
        $('#removeButton').on('click', removeRow);
        $('#ReceiptSubmissionForm').submit(function(event){
            if(notready){
                return;
            }
            // event.preventDefault();
        });

        $('#blockChooserUndo').on('click', function () {
            notready = true;
            //find current stage and undo
            if (curblockID === null) {
                if (curvarID === null) {
                    setupChooseGrower();
                } else {
                    setupChooseVar(curgrowerID, curGrowerName);
                }
            } else {
                setupChooseBlock(curgrowerID, curvarID, curGrowerName, curVarName);
            }
        });
    });

    function addRow(){
        var rowNum = curNumRows + 1;
        var htmlString = "<div id=\"binRow" + rowNum + "\" class=\"col-3\" style=\"display: flex\"><div style=\"width:100%\"><label>Number of Bins<input type=\"number\" inputmode=\"numeric\" pattern=\"[0-9]*\" min=\"1\" name=\"numbins" + rowNum + "\" max=\"100000\" inputmode=\"numeric\" pattern=\"[0-9]*\" placeholder=\"0\" required></label></div><div style=\"border-left: 1px solid #e4e4e4;\"><label>Bushels Per Bin<input type=\"number\" min=\"1\" name=\"numbushels" + rowNum + "\" placeholder=\"23\" max=\"50\"></label></div></div>";
        $(htmlString).insertAfter('#binRow' + curNumRows++);
    }

    function removeRow(){
        if(curNumRows > 1){
            $('#binRow' + curNumRows--).remove();
        }
    }

    function setupChooseGrower() {
        curgrowerID = null;
        curGrowerName = null;
        $('#BlockSel').show();
        $('#selectionViewer').hide();
        $('#selectionGrower').val('');
        var blockChooserSelect = $('#blockChooserSelect');
        blockChooserSelect.off('select2:select');
        $('#blockChooserTitle').text("Grower");
        blockChooserSelect.html("<option></option>");
        $.getJSON('API/getBlockIDHelper.php', function (data) {
            currSelect2 = blockChooserSelect.select2({
                placeholder: "Select a grower:",
                data: data,
                width: '100%',
            });
            blockChooserSelect.on('select2:select', function (e) {
                curgrowerID = e.params.data.id;
                curGrowerName = e.params.data.text;
                setupChooseVar(e.params.data.id, e.params.data.text);
            });
        });
    }

    function setupChooseVar(growerID, growerName) {
        //change titles
        $('#blockChooserTitle').text("Variety");
        $('#selectionGrower').val(growerName);
        $('#selectionVariety').hide();
        $('#selectionBlock').hide();
        $('#selectionViewer').show();
        //destroy old values
        $('#BlockSel').show();
        curvarID = null;
        curVarName = null;
        var blockChooserSelect = $('#blockChooserSelect');
        blockChooserSelect.off('select2:select');
        blockChooserSelect.html("<option></option>");
        $.getJSON('API/getBlockIDHelper.php', {growerID: growerID}, function (data) {
            currSelect2 = blockChooserSelect.select2({
                placeholder: "Select a variety:",
                data: data,
                width: '100%',
            });
            blockChooserSelect.on('select2:select', function (e) {
                curvarID = e.params.data.id;
                curVarName = e.params.data.text;
                setupChooseBlock(growerID, e.params.data.id, growerName, e.params.data.text);
            });
        });
    }

    function setupChooseBlock(growerID, varID, growerName, VarName) {
        curblockID = null;
        curBlockDesc = null;
        //change titles
        $('#blockChooserTitle').text('Block');
        $('#selectionVariety').val(VarName).show();
        $('#selectionBlock').hide();
        //destroy old values
        $('#BlockSel').show();
        blockID = null;
        var blockChooserSelect = $('#blockChooserSelect');
        blockChooserSelect.off('select2:select');
        blockChooserSelect.html('<option></option>');
        $.getJSON('API/getBlockIDHelper.php', {growerID: growerID, VarietyID: varID}, function (data) {
            currSelect2 = blockChooserSelect.select2({
                placeholder: "Select a block:",
                data: data,
                width: '100%',
            });
            blockChooserSelect.on('select2:select', function (e) {
                curblockID = e.params.data.id;
                curBlockDesc = e.params.data.text;
                //update selection title
                $('#selectionBlock').val(e.params.data.text).show();
                //close out chooser and hide
                $('#BlockSel').hide();
                notready = false;
            });
        });
    }
</script>
</html>