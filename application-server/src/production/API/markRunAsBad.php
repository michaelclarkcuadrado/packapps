<?php
include '../../config.php';
if (isset($_POST['Run']))
{
    $query = mysqli_query($mysqli, "SELECT * FROM PSOHCSV_flagged_bad_runs WHERE Run='".mysqli_real_escape_string($mysqli, $_POST['Run'])."'");
    if(mysqli_num_rows($query) == 0) {
        mysqli_query($mysqli, "INSERT INTO PSOHCSV_flagged_bad_runs VALUES (" . mysqli_real_escape_string($mysqli, $_POST['Run']) . ", default, default)");
    }
    else {
        mysqli_query($mysqli, "DELETE FROM PSOHCSV_flagged_bad_runs WHERE Run='".mysqli_real_escape_string($mysqli, $_POST['Run'])."'");
    }
}
