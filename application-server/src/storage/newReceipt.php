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
    <form id="inspectorSubmissionForm" action="" method="post" enctype="multipart/form-data">
        <div id="selectionViewer" style="display:none" class="col-2">
            <label>
                Selection:
                <div style="display:flex">
                    <input type="text" style="overflow-x: scroll" id="selectionTitle" readonly placeholder="">
                    <i id="blockChooserUndo" style="cursor: pointer;" class="material-icons">undo</i>
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
        <div class="col-3">
            <label>Color Quality</label>
            <label class="ColorNonGoldsSelector" style="text-align: center">GOOD<input class="ColorNonGoldsSelector" type="radio" name="color" value="Good" required></label>
            <label class="ColorNonGoldsSelector" style="text-align: center">FAIR<input class="ColorNonGoldsSelector" type="radio" name="color" value="Fair"></label>
            <label class="ColorNonGoldsSelector" style="text-align: center">POOR<input class="ColorNonGoldsSelector" type="radio" name="color" value="Poor"></label>
            <label class="ColorGoldsSelector" style="text-align: center; display: none">Green<input class="ColorGoldsSelector" type="radio" name="color" value="Green" disabled></label>
            <label class="ColorGoldsSelector" style="text-align: center; display: none">Yellow<input class="ColorGoldsSelector" type="radio" name="color" value="Yellow" disabled></label>
            <label class="ColorGoldsSelector" style="text-align: center; display: none">Blush<input class="ColorGoldsSelector" type="checkbox" name="blushcolor" value="1" disabled></label>
        </div>
        <!--Bitter Pit-->
        <div class="col-4">
            <label>Bitter pit</label>
            <label style="text-align: center">None to Light
                <input type="radio" onclick="$('#isBitterPit').slideUp();$('#bitterPitCloseUpPic').attr('disabled', true).attr('required', false);" name="isBitterPitPresent" value='0' id="bitterpit1"
                       required/></label>
            <label style="text-align: center">Moderate to Heavy
                <input type="radio" onclick="$('#isBitterPit').slideDown();$('#bitterPitCloseUpPic').attr('disabled', false).attr('required', true);" name="isBitterPitPresent" value="1"
                       id="bitterpit2"/></label>
        </div>
        <div id="isBitterPit" style="display: none" class='col-4'>
            <label>
                Take a photo of the bitter pit damage.
                <input type="file" id="bitterPitCloseUpPic" name='bitterPitDamageCloseUp' accept="image/jpeg" disabled>
            </label>
        </div>
        <!--Bruising-->
        <div class="col-4">
            <label>Bruising</label>
            <label style="text-align: center">None
                <input type="radio"
                       onclick="$('.isBruisingHeavy').slideUp().attr('disabled', true).attr('required', false).attr('checked', false);$('.isBruisingSevere').slideUp().attr('disabled', true).attr('required', false);"
                       name="isBruised" value='None' id="isBruised0" required/></label>
            <label style="text-align: center">Lighter
                <input type="radio"
                       onclick="$('.isBruisingHeavy').slideUp().attr('disabled', true).attr('required', false).attr('checked', false);$('.isBruisingSevere').slideUp().attr('disabled', true).attr('required', false);"
                       name="isBruised" value='Light' id="isBruised1"/></label>
            <label style="text-align: center">Heavier
                <input type="radio" onclick="$('.isBruisingHeavy').slideDown().attr('disabled', false).attr('required', true);" name="isBruised" value="Heavy" id="isBruised2"/></label>
        </div>
        <div style="display: none" class='col-4 isBruisingHeavy'>
            <label>Is the bruising severe?</label>
            <label style="text-align: center">No
                <input class="isBruisingHeavy" type="radio" onclick="$('.isBruisingSevere').slideUp().attr('disabled', true).attr('required', false);" name="isBruisedSevere" value='No'
                       id="isBruisedSevere1" required disabled/></label>
            <label style="text-align: center">Yes
                <input class="isBruisingHeavy" type="radio" onclick="$('.isBruisingSevere').slideDown().attr('disabled', false).attr('required', true);" name="isBruisedSevere" value='Yes'
                       id="isBruisedSevere1" disabled/></label>
        </div>
        <div style="display: none" class='col-4 isBruisingSevere'>
            <label>
                Take a photo of the bruising damage.
                <input type="file" class="isBruisingSevere" name='bruisingDamageCloseUp' accept="image/jpeg" disabled>
            </label>
        </div>

        <!--Color-->
        <div style="display: none" class="col-4 ColorGoldsSelector">
            <label>
                Russet
                <select class="ColorGoldsSelector" name="russetpercent" disabled>
                    <option value="None">None</option>
                    <option value="Light">Light</option>
                    <option value="Moderate">Moderate</option>
                    <option value="Heavy">Heavy</option>
                    <option value="Severe">Severe</option>
                </select>
            </label>
        </div>
        <div class="col-4">
            <label>Additional Notes
                <input type="text" maxlength="255" name="notes" placeholder="Anything else?" autocomplete="off"></label>
        </div>
        <div class="col-submit">
            <button class="submitbtn">Receive Inventory</button>
            <br>
            <label style="border: dashed black 1px; vertical-align: middle">Inspected
                by <? echo $userData['Real Name'] . " on " . date('l, F jS Y') ?></label>
        </div>
    </form>
</div>
</body>
<script>
    var curgrowerID = null;
    var curvarID = null;
    var curblockID = null;
    var curGrowerName = null;
    var curVarName = null
    var curBlockDesc = null;

    var currSelect2 = null;
    $(document).ready(function () {
        setupChooseGrower();
        $('#blockChooserUndo').on('click', function() {
            //find current stage and undo

        });
    });

    function setupChooseGrower() {
        curgrowerID = null;
        curGrowerName = null;
        $('#BlockSel').show();
        $('#selectionViewer').hide();
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
        $('#selectionTitle').val(growerName);
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
        $('#selectionTitle').val(growerName + ' - ' + VarName);
        $('selectionViewer').show();
        //destroy old values
        $('#BlockSel').show();
        blockID = null;
        var blockChooserSelect = $('#blockChooserSelect');
        blockChooserSelect.off('select2:select');
        blockChooserSelect.html('<option></option>');
        $.getJSON('API/getBlockIDHelper.php', {growerID: growerID, VarietyID: varID}, function(data){
            currSelect2 = blockChooserSelect.select2({
                placeholder: "Select a block:",
                data: data,
                width: '100%',
            });
            blockChooserSelect.on('select2:select', function(e){
                curblockID = e.params.data.id;
                curBlockDesc = e.params.data.text;
                //update selection title
                $('#selectionTitle').val(growerName + ' - ' + VarName + ' - ' + e.params.data.text);
                //close out chooser and hide
                $('#BlockSel').hide();
            });
        });
    }
</script>
</html>