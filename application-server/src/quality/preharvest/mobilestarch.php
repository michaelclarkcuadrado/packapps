<?php
include_once("../../scripts-common/Mobile_Detect.php");
$detect = new Mobile_Detect();
include '../../config.php';
packapps_authenticate_user('quality');

$pendingstarchdata = mysqli_query($mysqli, "
SELECT
  `grower_gfbvs-listing`.GrowerName AS Grower,
  test_id                           AS testID,
  grower_Preharvest_tests.block_PK  AS PK,
  BlockDesc,
  VarietyName                       AS VarDesc
FROM grower_Preharvest_tests
  JOIN `grower_gfbvs-listing` ON grower_Preharvest_tests.block_PK = `grower_gfbvs-listing`.PK
WHERE isStarchInspected = '0' AND `Date` >= NOW() - INTERVAL 5 DAY
GROUP BY test_id");
?>
<html style="width: 100%" xmlns="http://www.w3.org/1999/html">
<head>
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="icon" sizes="196x196" href="apple-touch-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    input[type='text'] {
        width: 100%

    }

    input.submitbtn {
        background-image: linear-gradient(#97c16b, #8ab959);
        border-bottom: 1px solid #648c3a;
        cursor: pointer;
        color: #fff;
    }

    input.submitbtn:hover {
        background-image: linear-gradient(#8ab959, #7eaf4a);
    }

    input.submitbtn:active {
        height: 34px;
        border-bottom: 0;
        margin: 1px 0 0 0;
        background-image: linear-gradient(#7eaf4a, #8ab959);
        -moz-box-shadow: inset 0 1px 3px 1px rgba(0, 0, 0, 0.3);
        -webkit-box-shadow: inset 0 1px 3px 1px rgba(0, 0, 0, 0.3);
        box-shadow: inset 0 1px 3px 1px rgba(0, 0, 0, 0.3);
    }
</style>
<title>Starch Pre-Harvest</title>
<? if (isset($_GET['ph'])) {
    echo "<b>Starch information received.</b>";
}
if ($detect->isMobile()) {
    echo "<p style='top: 0px; width: 100%'><button onclick='location.replace(\"../mobileQA.php\")'><<< Go back</button></p>";
} ?>
<form action="starchsubmit.php" method="post" enctype="multipart/form-data">

    <h2>Starch for Pre-Harvest</h2>
    <a style='font-size: small' href='#' onclick='location.reload();'>Refresh</a><br><br>
    <div id="RTinfo"></div>
    <table style="width: 100%" id="table">
        <tr>
            <td>Starch Samples</td>
            <td style="text-align: center">
                <select id='ID_sel' style='width: 100%' class='required' name="ID" autofocus required>
                    <option disabled selected>Select Sample</option>
                    <?php while ($pendingstarcharray = mysqli_fetch_assoc($pendingstarchdata)) {
                        echo "<option value='" . $pendingstarcharray['testID'] . "'>ID: " . $pendingstarcharray['PK'] . " --  Grower: " . $pendingstarcharray['Grower'] . " --  Variety: " . $pendingstarcharray['VarDesc'] . " --  Block: " . $pendingstarcharray['BlockDesc'] . "</ option>";
                    } ?>
                </select></td>
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
            <td colspan="2"><input max="5" type="checkbox" id="moreappel" name="moreappel" value="yes">10 Fruit in
                Sample
            </td>
        </tr>
    </table>
    <br>
    <div style="border: dashed black 1px">Take Starch photo<br><input type="file" accept="image/jpeg"
                                                                      name="starchupload" required><br></div>
    <br><br>
    <input class="submitbtn" type="submit" value="Publish to Grower">
</form>
<br><br>
<script src="../assets/js/jquery.min.js"></script>
<script>
    //add 5 apples to sample
    $('#moreappel').on('change', function () {
        if ($(this).is(':checked') == true) {
            $("<tr class='more'><td>Sample 6</td><td><input style='width: 100%' max='10' type='number' name='Starch6' placeholder='Starch' step='.5' required></td></tr><tr class='more'><td>Sample 7</td><td><input style='width: 100%' max='10' type='number' name='Starch7' placeholder='Starch' step='.5' required></td></tr><tr class='more'><td>Sample 8</td><td><input style='width: 100%' max='10' type='number' name='Starch8' placeholder='Starch' step='.5' required></td></tr><tr class='more'><td>Sample 9</td><td><input style='width: 100%' max='10' type='number' name='Starch9' placeholder='Starch' step='.5' required></td></tr><tr class='more'><td>Sample 10</td><td><input style='width: 100%' max='10' type='number' name='Starch10' placeholder='Starch' step='.5' required></td></tr>").appendTo("#table");
        } else {
            $('.more').remove();
        }
    });

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