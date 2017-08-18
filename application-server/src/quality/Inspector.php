<?php
require '../config.php';
$userData = packapps_authenticate_user('quality');

include_once("../scripts-common/Mobile_Detect.php");
$detect=new Mobile_Detect();
?>
<!doctype html>

<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="PackApps">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="icon" sizes="196x196" href="apple-touch-icon.png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html">
    <meta name="theme-color" content="#e2eef4">
    <title>Mobile QA</title>
    <link rel="stylesheet" type="text/css" media="all" href="assets/css/inspector.css">
    <script src="assets/js/jquery.min.js"></script>
</head>

<body>
<div id="wrapper">
    <? if ($userData['Role'] == 'QA' && $detect->isMobile()) {
        echo "<p style='position: fixed; top: 0; width: 100%'><button onclick=\"location.replace('/quality')\"><<< Go back</button></p>";
    } ?>
    <h1>New Receipt Quality Report</h1>
    <br>
    <? if (isset($_GET['ins'])) {
        echo "<h1><mark>&#x2713; Data for #" . $_GET['ins'] . " received.</mark><br><a href='WeightSamples.php?autofill=". $_GET['ins'] ."'><button>Weigh this Ticket</button></a></h1>";
    } ?>

    <h2><?echo $companyName?> Quality Assurance Lab</h2>
    <form id="inspectorSubmissionForm" action="Inspectorsubmit.php" method="post" enctype="multipart/form-data">
        <div class="col-2">
            <label>
                Receipt Number
                <input id="RT" type=number placeholder="#" name="RT" required>
            </label>
        </div>
        <div class="col-2">
            <label>
                Confirm Receipt Number
                <input id='RT2' type=number placeholder="Confirm #" name="RT2" required>
            </label>
        </div>
        <div class="col-2" id="RTinfo">
        </div>
        <div class="col-3">
            <label>
                Bin Photo
                <input type="file" name='binpicupload' accept="image/jpeg" required>
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
                    <input type="radio" onclick="$('#isBitterPit').slideUp();$('#bitterPitCloseUpPic').attr('disabled', true).attr('required', false);" name="isBitterPitPresent" value='0' id="bitterpit1" required /></label>
            <label style="text-align: center">Moderate to Heavy
                    <input type="radio" onclick="$('#isBitterPit').slideDown();$('#bitterPitCloseUpPic').attr('disabled', false).attr('required', true);" name="isBitterPitPresent" value="1" id="bitterpit2" /></label>
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
                <input type="radio" onclick="$('.isBruisingHeavy').slideUp().attr('disabled', true).attr('required', false).attr('checked', false);$('.isBruisingSevere').slideUp().attr('disabled', true).attr('required', false);" name="isBruised" value='None' id="isBruised0" required/></label>
            <label style="text-align: center">Lighter
                <input type="radio" onclick="$('.isBruisingHeavy').slideUp().attr('disabled', true).attr('required', false).attr('checked', false);$('.isBruisingSevere').slideUp().attr('disabled', true).attr('required', false);" name="isBruised" value='Light' id="isBruised1" /></label>
            <label style="text-align: center">Heavier
                <input type="radio" onclick="$('.isBruisingHeavy').slideDown().attr('disabled', false).attr('required', true);" name="isBruised" value="Heavy" id="isBruised2" /></label>
        </div>
        <div style="display: none" class='col-4 isBruisingHeavy'>
            <label>Is the bruising severe?</label>
            <label style="text-align: center">No
                <input class="isBruisingHeavy" type="radio" onclick="$('.isBruisingSevere').slideUp().attr('disabled', true).attr('required', false);" name="isBruisedSevere" value='No' id="isBruisedSevere1" required disabled/></label>
            <label style="text-align: center">Yes
                <input class="isBruisingHeavy" type="radio" onclick="$('.isBruisingSevere').slideDown().attr('disabled', false).attr('required', true);" name="isBruisedSevere" value='Yes' id="isBruisedSevere1" disabled/></label>
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
            <label>
                Scab
                <select name="scabpercent" required>
                    <option value="None">None</option>
                    <option value="Light">Light</option>
                    <option value="Moderate">Moderate</option>
                    <option value="Heavy">Heavy</option>
                    <option value="Severe">Severe</option>
                </select>
            </label>
        </div>
        <div class="col-4">
            <label>
                San Jose Scale
                <select name="SJScalepercent" required>
                    <option value="None">None</option>
                    <option value="Light">Light</option>
                    <option value="Moderate">Moderate</option>
                    <option value="Heavy">Heavy</option>
                    <option value="Severe">Severe</option>
                </select>
            </label>
        </div>
        <div class="col-4">
            <label>
                Sunburn
                <select name="sunBurnpercent" required>
                    <option value="None">None</option>
                    <option value="Light">Light</option>
                    <option value="Moderate">Moderate</option>
                    <option value="Heavy">Heavy</option>
                    <option value="Severe">Severe</option>
                </select>
            </label>
        </div>
        <div class="col-4">
            <label>
                Stink Bug Damage
                <select name="stbugpercent" required>
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
            <button class="submitbtn">Submit Report to QA Lab</button><br>
            <label style="border: dashed black 1px; vertical-align: middle">Inspected
                by <? echo $userData['Real Name'] . " on " . date('l, F jS Y') ?></label>
        </div>
    <input id="numSamples" type="hidden" name="NumSamples" value="10"/>
    </form>
</div>
<script>
    $("#RT2, #RT").on("change", function(){
        pullRTData();
    });

    $('#inspectorSubmissionForm').submit(function() {
        $('.submitbtn').replaceWith(
            "Submitting...."
        );
    });

    function pullRTData() {
        if ($("#RT").val() == $("#RT2").val()) {
            $.ajax({
                type: 'GET',
                url: "API/rtinfoins.php?q=" + $("#RT").val(),
                dataType: 'json',
                tryCount: 0,
                retryLimit: 3,
                success: function (data) {
                    $("#RTinfo").replaceWith("<div class='col-1' id='RTinfo'><label style='text-align: center'><img src=images/" + data.CommDesc + ".png> " + data.CommDesc + "<br>Grower: " + data.GrowerName + "<br>Farm: " + data.FarmDesc + "<br>Block: " + data.BlockDesc + "<br>Variety: " + data.VarDesc + "<br>Strain: " + data.StrDesc + "<br>Bins/Units: " + data.QtyOnHand + "<br>Headed to: " + data.Location + "<br><hr style='opacity: 0'><div style='font-size: larger'>This delivery requires <mark>" + data.NumSamplesRequired + "</mark> samples.</div></label>" + (data.isDone > 0 ? "<label style='border: dashed red;text-align: center;vertical-align: middle;color: red'>Warning: This delivery has already been completed!</label>" : "") + (data.Today == 0 ? "<label style='border: dashed red;text-align: center;vertical-align: middle;color: red'>Warning: This RT is <u>NOT</u> from today.</label>" : "") + "<hr style='width: 100%; color: grey'></div>");
                    $("#numSamples").val(data.NumSamplesRequired);
                    if(data.isGoldApple > 0)
                    {
                        activateGolds()
                    }
                    else
                    {
                        deactivateGolds()
                    }
                },
                error: function () {
                    this.tryCount++;
                    if (this.tryCount <= this.retryLimit) {
                        $("#RTinfo").replaceWith("<div class='col-1' id='RTinfo'><label style='text-align: center; color: red'>That's not in the system! (Yet).<br> It might still be filtering in. Wait or try again.<br><br><button type='button' onclick='pullRTData()'>Check Again</button><br></label><label style='text-align: center;'>Are these fruit Golds or Gingergold apples?<input onchange='if (this.checked){activateGolds()} else {deactivateGolds()}' type='checkbox' name='goldsManualOverride' value='1' ></label></div>");
                        deactivateGolds();
                        $.ajax(this);
                    }
                }
            });
        } else {
            if ($("#RT2").val() != "") {
                $("#RTinfo").replaceWith("<div class='col-1' id='RTinfo'><label style='text-align: center; color: red'>Those RTs Don't Match!</label></div>");
                deactivateGolds();
                navigator.vibrate(400);
            }
        }
    }

    function activateGolds()
    {
        $(".ColorGoldsSelector").show().attr("disabled", false);
        $(".ColorNonGoldsSelector").hide().attr("disabled", true);
    }

    function deactivateGolds()
    {
        $(".ColorNonGoldsSelector").show().attr("disabled", false);
        $(".ColorGoldsSelector").hide().attr("disabled", true);
    }

    //make enter act like tab - for numpads
    $('body').on('keydown', 'input, select, textarea', function (e) {
        var self = $(this)
            , form = self.parents('form:eq(0)')
            , focusable
            , next
            ;
        if (e.keyCode == 13) {
            focusable = form.find('input[type=\'number\'],a,select,textarea').filter(':enabled');
            next = focusable.eq(focusable.index(this) + 1);
            if (next.length) {
                next.focus();
            } else {
                event.preventDefault();
                return false;
            }
            return false;
        }
    });
</script>
</body>
</html>