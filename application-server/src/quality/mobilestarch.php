<?php
include_once("Classes/Mobile_Detect.php");
$detect = new Mobile_Detect();
require '../config.php';

$rts = mysqli_query($mysqli, "SELECT quality_InspectedRTs.RTNum AS `RT#`, ifnull(BULKOHCSV.Grower,'?') AS Grower, ifnull(`CommDesc`, '?') AS CommDesc, ifnull(BULKOHCSV.VarDesc,'?') AS VarDesc, ifnull(BULKOHCSV.Date, date(quality_InspectedRTs.DateInspected)) AS Date FROM quality_InspectedRTs LEFT JOIN BULKOHCSV ON quality_InspectedRTs.RTNum=BULKOHCSV.`RT#` WHERE quality_InspectedRTs.`#Samples`=20 AND StarchFinished=0 AND CommDesc != 'Peach' AND CommDesc != 'Nectarine' ORDER BY quality_InspectedRTs.DateInspected ASC ");

?>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="icon" sizes="196x196" href="apple-touch-icon.png">
    <meta name="theme-color" content="#e2eef4">
    <meta name="viewport" content="width=device-width">
    <meta name="theme-color" content="#e2eef4">
</head>
<style type="text/css">
    body {
        text-align: center;
        background-color: #e2eef4;
    }

    tr, td {
        border: solid #000000 1px;
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
<title>Starch on RT</title>
<? if (isset($_GET['ph'])) {
    echo "<b>Starch for Block# " . $_GET['ph'] . " received.</b>";
}
if ($detect->isMobile()) {
    echo "<p style='top: 0;'><button onclick='location.replace(\"mobileQA.php\")'><<< Go back</button></p>";
} ?>
<form action="starchsubmit.php" method="post" enctype="multipart/form-data">
    <? if (mysqli_num_rows($rts) == '0') {
        echo("<script>setTimeout(function(){
   window.location.reload(1);
}, 45000);</script><b><mark>There are no more RTs to test at the moment. &#9787</mark></b><br>");
    } ?>
    <h2>Starch for Received RT</h2>

    <div id="RTinfo"></div>
    <table id="table">
        <tr>
            <td>RT Number</td>
            <td style="text-align: center"><select id='RT_sel' style='width: 100%' name="RT" autofocus required>
                    <option disabled selected>Select RT</option>
                    <?
                    while ($receivedtodo = mysqli_fetch_assoc($rts)) {
                        echo "<option value='" . $receivedtodo['RT#'] . "'>" . $receivedtodo['Date'] . " - RT#" . $receivedtodo['RT#'] . " - " . $receivedtodo['Grower'] . " - " . $receivedtodo['CommDesc'] . " - " . $receivedtodo['VarDesc'] . "</ option>";
                    }
                    ?>
                </select></td>
        </tr>
        </tr>
        <tr>
            <td>Sample 1</td>
            <td><input style="width: 100%" max="10" min="0" type="number" name="Starch1" placeholder="Starch" step=".5"
                       required></td>
        </tr>
        <tr>
            <td>Sample 2</td>
            <td><input style="width: 100%" max="10" min="0" type="number" name="Starch2" placeholder="Starch" step=".5"
                       required></td>
        </tr>
        <tr>
            <td>Sample 3</td>
            <td><input style="width: 100%" max="10" min="0" type="number" name="Starch3" placeholder="Starch" step=".5"
                       required></td>
        </tr>
        <tr>
            <td>Sample 4</td>
            <td><input style="width: 100%" max="10" min="0" type="number" name="Starch4" placeholder="Starch" step=".5"
                       required></td>
        </tr>
        <tr>
            <td>Sample 5</td>
            <td><input style="width: 100%" max="10" min="0" type="number" name="Starch5" placeholder="Starch" step=".5"
                       required></td>
        </tr>
        <tr>
            <td>Sample 6</td>
            <td><input style='width: 100%' max='10' min="0" type='number' name='Starch6' placeholder='Starch' step='.5'
                       required></td>
        </tr>
        <tr>
            <td>Sample 7</td>
            <td><input style='width: 100%' max='10' min="0" type='number' name='Starch7' placeholder='Starch' step='.5'
                       required></td>
        </tr>
        <tr>
            <td>Sample 8</td>
            <td><input style='width: 100%' max='10' min="0" type='number' name='Starch8' placeholder='Starch' step='.5'
                       required></td>
        </tr>
        <tr>
            <td>Sample 9</td>
            <td><input style='width: 100%' max='10' min="0" type='number' name='Starch9' placeholder='Starch' step='.5'
                       required></td>
        </tr>
        <tr>
            <td>Sample 10</td>
            <td><input style='width: 100%' max='10' min="0" type='number' name='Starch10' placeholder='Starch' step='.5'
                       required></td>
        </tr>
    </table>
    <br>
    <div style="border: dashed black 1px">Take Starch photo<br><input type="file" accept="image/jpeg"
                                                                      name="starchupload" required><br></div>
    <br><br>
    <input class="submitbtn" type="submit" value="Submit Starch">
</form>
<br><br>
<script src="assets/js/jquery.min.js"></script>
<script>
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
</script>
</html>
