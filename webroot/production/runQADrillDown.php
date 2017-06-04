<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 6/14/2016
 * Time: 1:05 PM
 */
if (isset($_GET['q'])) {
    include '../config.php';
    $sampleData = mysqli_query($mysqli, "SELECT Weight, Pressure1, Pressure2, Brix, Note, isPhotographed FROM run_inspections WHERE RunID='" . mysqli_real_escape_string($mysqli, $_GET['q']) . "'");
    $Note = '';
    $photographed = false;
} else {
    die("<script>window.close()</script>");
}
?>
<!doctype html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production</title>

    <!-- Disable tap highlight on IE -->
    <meta name="msapplication-tap-highlight" content="no">

    <!-- Web Application Manifest -->
    <link rel="manifest" href="manifest.json">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#2F3BA2">

    <!-- Color the status bar on mobile devices -->
    <meta name="theme-color" content="#2F3BA2">

    <link rel="stylesheet" href="styles/material.min.css">

    <!-- Material Design icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- Your styles -->
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <h4 class="mdl-dialog__title">QA Details</h4>
    <table style='margin: 25px' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
        <thead>
        <tr>
            <th class="mdl-data-table__cell--non-numeric">Pressure 1</th>
            <th class="mdl-data-table__cell--non-numeric">Pressure 2</th>
            <th class="mdl-data-table__cell--non-numeric">Brix</th>
            <th class="mdl-data-table__cell--non-numeric">Weight</th>
        </tr>
        </thead>
        <tbody>
        <?
            while ($data = mysqli_fetch_array($sampleData))
            {
                echo "<tr><td class=\"mdl-data-table__cell--non-numeric\">".$data['Pressure1']."</td><td class=\"mdl-data-table__cell--non-numeric\">".$data['Pressure2']."</td><td class=\"mdl-data-table__cell--non-numeric\">".$data['Brix']."</td><td class=\"mdl-data-table__cell--non-numeric\">".$data['Weight']."</td></tr>";
                $Note = $data['Note'];
                if ($data['isPhotographed'] == 1) {
                    $photographed = true;
                }
            }
        ?>
        </tbody>
    </table>
<p style="margin: 25px">Note: <?echo $Note;?></p>
<?if ($photographed)
{
    echo "<a href='/quality/assets/uploadedimages/runs/".$_GET['q'].".jpg'><img style='margin: 15px; width: 94%' src='/quality/assets/uploadedimages/runs/".$_GET['q'].".jpg'></a>";
} else {
    echo "<p style='margin: 25px'>No photo attached.</p>";
}
?>
</body>
<script src="scripts/material.min.js"></script>
<script src="scripts/main.js"></script>
<script src="scripts/jquery.min.js"></script>
<script language="JavaScript">

</script>