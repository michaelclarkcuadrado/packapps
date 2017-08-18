<?php
include_once("../scripts-common/Mobile_Detect.php");
$detect = new Mobile_Detect();
require '../config.php';
packapps_authenticate_user('quality');
$rts = mysqli_query($mysqli, "
SELECT
  quality_InspectedRTs.receiptNum          AS `RT#`,
  GrowerName                               AS Grower,
  commodity_name                           AS CommDesc,
  VarietyName                              AS VarDesc,
  date(quality_InspectedRTs.DateInspected) AS Date,
  `#Samples`                               AS NumSamples
FROM quality_InspectedRTs
  JOIN storage_grower_receipts ON quality_InspectedRTs.receiptNum = storage_grower_receipts.id
  JOIN `grower_gfbvs-listing` ON grower_block = PK
WHERE quality_InspectedRTs.DAFinished = '0' AND (`#Samples` = '20' OR `#Samples` = '10')
ORDER BY quality_InspectedRTs.DateInspected ASC
");
?>
<html style="width: 100%">
<head>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Mobile DA">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="icon" sizes="196x196" href="apple-touch-icon.png">
    <meta name="theme-color" content="#e2eef4">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<style type="text/css">
    body {
        text-align: center;
        background-color: #e2eef4;
    }

    tr, td {
        border: solid #000000 1px;
    }

    input[type='text'] {
        width: 100%

    }

    input.submitbtn {
        background-image: -moz-linear-gradient(#97c16b, #8ab959);
        background-image: -webkit-linear-gradient(#97c16b, #8ab959);
        background-image: linear-gradient(#97c16b, #8ab959);
        border-bottom: 1px solid #648c3a;
        cursor: pointer;
        color: #fff;
    }

    input.submitbtn:hover {
        background-image: -moz-linear-gradient(#8ab959, #7eaf4a);
        background-image: -webkit-linear-gradient(#8ab959, #7eaf4a);
        background-image: linear-gradient(#8ab959, #7eaf4a);
    }

    input.submitbtn:active {
        height: 34px;
        border-bottom: 0;
        margin: 1px 0 0 0;
        background-image: -moz-linear-gradient(#7eaf4a, #8ab959);
        background-image: -webkit-linear-gradient(#7eaf4a, #8ab959);
        background-image: linear-gradient(#7eaf4a, #8ab959);
        -moz-box-shadow: inset 0 1px 3px 1px rgba(0, 0, 0, 0.3);
        -webkit-box-shadow: inset 0 1px 3px 1px rgba(0, 0, 0, 0.3);
        box-shadow: inset 0 1px 3px 1px rgba(0, 0, 0, 0.3);
    }
</style>
<title>Mobile DA</title>
<? if (isset($_GET['da'])) {
    echo "<b>DA for RT# " . $_GET['da'] . " received.</b>";
} ?>
<? if ($detect->isMobile()) {
    echo "<p style='top: 0px; width: 100%'><button onclick=\"location.replace('mobileQA.php')\"><<< Go back</button></p>";
} ?>

<form action="DAsubmit.php" method="post">
    <table style="width: 100%" id="table">
        <? if (mysqli_num_rows($rts) == '0') {
            echo("<script>setTimeout(function(){
   window.location.reload(1);
}, 45000);</script><b><mark>There are no more RTs to test at the moment. &#9787</mark></b><br>");
        } ?>
        <h2>DA Testing Results</h2>
        <a style='font-size: small' href='#' onclick='location.reload();'>Refresh</a><br><br>
        <div id="RTinfo"></div>
        <tr>
            <td>RT Number</td>
            <td style="text-align: center">
                <select id='RT_sel' onchange="createRows()" style='width: 100%' name="RT" autofocus required>
                    <option disabled selected>Select RT</option>
                    <?
                    while ($receivedtodo = mysqli_fetch_assoc($rts)) {
                        echo "<option value='" . $receivedtodo['RT#'] . ":" . $receivedtodo['NumSamples'] . "'>" . $receivedtodo['Date'] . " - RT#" . $receivedtodo['RT#'] . " - " . $receivedtodo['Grower'] . " - " . $receivedtodo['CommDesc'] . " - " . $receivedtodo['VarDesc'] . "</ option>";
                    }
                    ?>
                </select></td>
        </tr>
        <tr id="rowsGoHere"></tr>
      </table>
    <input class="submitbtn" type="submit" value="Send DA">
</form>
<br><br>
<script src="assets/js/jquery.min.js"></script>
<script>

    function createRows()
    {
        var val = $("#RT_sel").attr('readonly', true).val();
        var iter = val.substr(val.indexOf(":") +1);
        var string = String("<tr id ='rowsGoHere'>");
        var hopper = true;
        for (var i = 1; i <= iter; i++)
        {
            string = string + "<tr><td>"+i+(hopper ? "A" : "B")+"</td><td><input style='width: 100%' max='5' type='number' step='any' name='"+i+(hopper ? "A" : "B")+"' placeholder='"+i+(hopper ? "A" : "B")+"' required></td></tr>";
            hopper = !hopper;
            string = string + "<tr><td>"+i+(hopper ? "A" : "B")+"</td><td><input style='width: 100%' max='5' type='number' step='any' name='"+i+(hopper ? "A" : "B")+"' placeholder='"+i+(hopper ? "A" : "B")+"' required></td></tr>";
            hopper = !hopper;
        }
        string = string + "</tr>";
        $("#rowsGoHere").nextAll().remove();
        $("#rowsGoHere").replaceWith(string);
    }

    //make enter act like tab - for numpads
    $('body').on('keydown', 'input, select, textarea', function (e) {
        var self = $(this)
            , form = self.parents('form:eq(0)')
            , focusable
            , next
            ;
        if (e.keyCode == 13) {
            focusable = form.find('input,a,select,textarea').filter(':enabled');
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

    function logout() {
        var xmlhttp;
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        }
        // code for IE
        else if (window.ActiveXObject) {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (window.ActiveXObject) {
            // IE clear HTTP Authentication
            document.execCommand("ClearAuthenticationCache", false);
            window.location.href = 'logout/logoutheader.php';
        } else {
            xmlhttp.open("GET", 'logout/logoutheader.php', true, "User Name", "logout");
            xmlhttp.send("");
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4) {
                    window.location.href = 'logout/logoutheader.php';
                }
            }
        }
        return false;
    }
</script>
</html>