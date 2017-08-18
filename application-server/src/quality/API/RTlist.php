<?php
//This produces the inside of the RT selector for QA.php's final inspection screen, so that it can be AJAX'd from a reload button
include '../../config.php';

$rts = mysqli_query($mysqli, "
    SELECT
      quality_InspectedRTs.receiptNum          AS `RT#`,
      GrowerName                               AS Grower,
      VarietyName                              AS VarDesc,
      date(quality_InspectedRTs.DateInspected) AS Date
    FROM quality_InspectedRTs
      JOIN storage_grower_receipts ON quality_InspectedRTs.receiptNum = storage_grower_receipts.id
      JOIN `grower_gfbvs-listing` ON storage_grower_receipts.grower_block = `grower_gfbvs-listing`.PK
    WHERE quality_InspectedRTs.isFinalInspected = '0'
    ORDER BY quality_InspectedRTs.DateInspected ASC
");?>
Select a Ticket for Testing: <select onchange="RTInsert();" class='selector'>
        <option value="" disabled
                selected><? echo(mysqli_num_rows($rts) == 0 ? "No Receipts left. &#9787;" : "Select RT"); ?></option>
        <?php while ($receivedtodo = mysqli_fetch_assoc($rts)) {
            echo "<option value='" . $receivedtodo['RT#'] . "'>" . $receivedtodo['Date'] . " - Receipt#" . $receivedtodo['RT#'] . " - " . $receivedtodo['Grower'] . " - " . $receivedtodo['VarDesc'] . "</ option>";
        } ?>
    </select> <a style='font-size: small' href='#' onclick='RTlistreload();'> <i class='fa fa-refresh'></i></a>