<?php
//This produces the inside of the RT selector for QA.php's final inspection screen, so that it can be AJAX'd from a reload button
include '../../config.php';

$rts = mysqli_query($mysqli, "SELECT quality_InspectedRTs.RTNum AS `RT#`, ifnull(BULKOHCSV.Grower,'?') AS Grower, ifnull(BULKOHCSV.VarDesc,'?') AS VarDesc, ifnull(BULKOHCSV.Date, date(quality_InspectedRTs.DateInspected)) AS Date FROM quality_InspectedRTs LEFT JOIN BULKOHCSV ON quality_InspectedRTs.RTNum=BULKOHCSV.`RT#` WHERE quality_InspectedRTs.isFinalInspected = '0' ORDER BY quality_InspectedRTs.DateInspected ASC ");
?>
    Select an RT for lab testing: <select onchange="RTInsert();" class='selector'>
        <option value="" disabled
                selected><? echo(mysqli_num_rows($rts) == 0 ? "No RTs left. &#9787;" : "Select RT"); ?></option>
        <?php while ($receivedtodo = mysqli_fetch_assoc($rts)) {
            echo "<option value='" . $receivedtodo['RT#'] . "'>" . $receivedtodo['Date'] . " - RT#" . $receivedtodo['RT#'] . " - " . $receivedtodo['Grower'] . " - " . $receivedtodo['VarDesc'] . "</ option>";
        } ?>
    </select> <a style='font-size: small' href='#' onclick='RTlistreload();'> <i class='fa fa-refresh'></i></a>