<?php
include '../config.php';

//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
    $checkAllowed = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `Real Name`, Role, isSectionManager as isAdmin, allowedQuality FROM master_users JOIN quality_UserData ON master_users.username=quality_UserData.UserName WHERE master_users.username = '$SecuredUserName'"));
    if (!$checkAllowed['allowedQuality'] > 0) {
        die ("<script>window.location.replace('/')</script>");
    } else {
        $RealName = $checkAllowed;
        $Role = $checkAllowed['Role'];
    }
}
// end authentication
include_once("Classes/Mobile_Detect.php");
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
    <? if ($RealName[1] == 'QA' && $detect->isMobile()) {
        echo "<p style='position: fixed; top: 0; width: 100%'><button onclick=\"location.replace('/quality')\"><<< Go back</button></p>";
    } ?>
    <h1>New Inventory Alert</h1>
    <br>
    <? if (isset($_GET['success'])) {
        echo "<h1><mark>&#x2713; Notice sent.</mark></h1>";
    } ?>

    <h2><?echo $companyName?> Quality Assurance Lab</h2>

    <form id="form" onsubmit="$('#finalButton').attr('disabled', true).html('Please wait...')" action="noticeSubmit.php" method="post" enctype="multipart/form-data">
        <div class="col-4">
            <label style="text-align: center">use barcode
                <input type="radio" onchange="changeInputType()" onclick="$('.ticketnum').slideUp(),$('.barcode').slideDown();" name="inputTypeSelection" value='0' id="bitterpit1" required /></label>
            <label style="text-align: center">type ticket number
                <input type="radio" onchange="changeInputType()" onclick="$('.barcode').slideUp(),$('.ticketnum').slideDown();" name="inputTypeSelection" value="1" id="bitterpit2" /></label>
        </div>
        <div style="display:none" class="barcode col-2">
            <label>
                Scan a barcode
                <input id="barcode" type='file'>
            </label>
        </div>
        <div style="display:none" class="ticketnum col-2">
            <label>
                Ticket Number
                <input id="Ticket1" type=number placeholder="Ticket">
            </label>
        </div>
        <div style="display: none" class="ticketnum col-2">
            <label>
                Confirm ticket Number
                <input id='Ticket2' type=number placeholder="Confirm Ticket">
            </label>
        </div>
        <div class="col-2" id="Ticketinfo">
        </div>

        <div style="display: none" class="notes col-4">
            <label>Notes on this run/bin
                <input type="text" required maxlength="255" id="notesbox" name="notes" placeholder="Notes" autocomplete="off"></label>
        </div>
        <div style="display: none" class="notes col-4">
            <label>Attach a photo (optional)
                <input type="file" accept="image/jpeg" name="binPicUpload" ></label>
        </div>
        <div style="display: none" class="presizedbad col-4">
            <label>Mark this run as bad on Inventory Explorer?</label>
            <label style="text-align: center">Yes
                <input type="radio" name="markRunAsBad" value='1' id="markBadRun" required /></label>
            <label style="text-align: center">No
                <input type="radio" name="markRunAsBad" value='0' id="markBadRun1" required /></label>
        </div>
        <div class="col-submit">
            <input type="hidden" name="RunNum" id="hiddenRunNum" value="">
            <input type="hidden" name="BinType" id="hiddenBinType" value="">

            <input type="hidden" name="GrowerName" id="hiddenGrowerName" value="">
            <input type="hidden" name="FarmDesc" id="hiddenFarmDesc" value="">
            <input type="hidden" name="BlockDesc" id="hiddenBlockDesc" value="">
            <input type="hidden" name="VarDesc" id="hiddenVarDesc" value="">

            <button id="finalButton" disabled class="submitbtn">Send Notice to Email List</button><br>
            <label style="border: dashed black 1px; vertical-align: middle"><? echo $RealName[0] . " - " . date('l, F jS Y') ?></label>
        </div>
    </form>
</div>
<br>
<script>
    $("#barcode").on('change', upload);
    function upload(event) {
        var barcode = event.target.files;
        var data = new FormData();
        $.each(barcode, function (key, value) {
            data.append(key, value);
        });
        $("#Ticketinfo").replaceWith("<div class='col-1' id='Ticketinfo'><label style='text-align: center'>Please wait...<br></label></div>");
        $.ajax({
            type: 'post',
            url: 'API/barcodeAndTicketInfo.php',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data2) {
                replaceTicketInfo(data2);
            },
            error: function () {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    $("#Ticketinfo").replaceWith("<div class='col-1' id='Ticketinfo'><label style='text-align: center; color: red'>That's not in the system!<br></label></div>");
                    $.ajax(this);
                }
            }
        });
    }
    $("#Ticket2, #Ticket1").on("change", function () {
        if ($("#Ticket1").val() == $("#Ticket2").val()) {
            $.ajax({
                type: 'GET',
                url: "API/barcodeAndTicketInfo.php?q=" + $("#Ticket1").val(),
                dataType: 'json',
                tryCount: 0,
                retryLimit: 3,
                success: function (data) {
                    replaceTicketInfo(data);
                },
                error: function () {
                    this.tryCount++;
                    if (this.tryCount <= this.retryLimit) {
                        $("#Ticketinfo").replaceWith("<div class='col-1' id='Ticketinfo'><label style='text-align: center; color: red'>That's not in the system!<br></label></div>");
                        $.ajax(this);
                    }
                }
            });
        } else {
            if ($("#Ticket2").val() != "") {
                $("#Ticketinfo").replaceWith("<div class='col-1' id='Ticketinfo'><label style='text-align: center; color: red'>Those tickets Don't Match!</label></div>");
                navigator.vibrate(400);
            }
        }
    });

    function changeInputType()
    {
        $("#hiddenRunNum").val('');
        $('#hiddenBinType').val('');
        $("#Ticketinfo").replaceWith("<div class='col-1' id='Ticketinfo'></div>").slideUp();
        $(".notes").slideUp();
        $(".presizedbad").slideUp();
        $("#finalButton").attr('disabled', true);
        $("#notesbox").val("");
        $('#hiddenGrowerName').val('');
        $('#hiddenFarmDesc').val('');
        $('#hiddenBlockDesc').val('');
        $('#hiddenVarDesc').val('');
    }


    function replaceTicketInfo(data)
    {
        if(data.error == '1') {
            $("#Ticketinfo").replaceWith("<div class='col-1' id='Ticketinfo'><label style='text-align: center; color: red'>The barcode did not scan. Try refocusing the photo.<br></label></div>");
        } else {
            $("#Ticketinfo").replaceWith("<div class='col-1' id='Ticketinfo'><label style='text-align: center'><img src=images/" + data.CommDesc + ".png> " + data.CommDesc + "<br>Variety: " + data.VarDesc + "<br>Grower: " + data.GrowerName + "<br>Grade/Farm: " + data.FarmDesc + "<br>Size/Block: " + data.BlockDesc + "<br>Total Bu: " + data.QtyOnHand + "<br><hr style='opacity: .7'></div>");
            $(".notes").slideDown();
            if(data[0] == 'presized')
            {
                $(".presizedbad").slideDown();
            }
            $('#hiddenRunNum').val(data['Run']);
            $('#hiddenBinType').val(data[0]);
            $("#finalButton").attr('disabled', false);

            $('#hiddenGrowerName').val(data.GrowerName);
            $('#hiddenFarmDesc').val(data.FarmDesc);
            $('#hiddenBlockDesc').val(data.BlockDesc);
            $('#hiddenVarDesc').val(data.VarDesc);
        }
    }


//    //make enter act like tab - for numpads
//    $('body').on('keydown', 'input, select, textarea', function (e) {
//        var self = $(this)
//            , form = self.parents('form:eq(0)')
//            , focusable
//            , next
//            ;
//        if (e.keyCode == 13) {
//            focusable = form.find('input[type=\'number\'],a,select,textarea').filter(':enabled');
//            next = focusable.eq(focusable.index(this) + 1);
//            if (next.length) {
//                next.focus();
//            } else {
//                event.preventDefault();
//                return false;
//            }
//            return false;
//        }
//    });
</script>
</body>
</html>