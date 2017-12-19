<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 11/21/17
 * Time: 12:31 PM
 */
require '../../config.php';
$userinfo = packapps_authenticate_user();

if (isset($_GET['growerID'])) {
    $grower_id = mysqli_real_escape_string($mysqli, $_GET['growerID']);
} else {
    die('<script>location.href = '/'</script>');
}

$growerData = mysqli_query($mysqli, "SELECT * FROM grower_GrowerLogins WHERE GrowerCode = '$grower_id'");

$growerRows = mysqli_query($mysqli, "SELECT * FROM
  grower_GrowerLogins
JOIN grower_farms ON grower_GrowerLogins.GrowerID = grower_farms.growerID
JOIN `grower_crop-estimates` ON grower_farms.farmID = `grower_crop-estimates`.farmID
JOIN grower_strains ON `grower_crop-estimates`.strainID = grower_strains.strain_ID
JOIN grower_varieties ON grower_strains.variety_ID = grower_varieties.VarietyID
JOIN grower_commodities ON grower_varieties.commodityID = grower_commodities.commodity_ID
WHERE GrowerCode = '$grower_id'");

$growerReceiptData = "";
?>
<!doctype html>
<html lang="en" xmlns:v-bind="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grower Management</title>

    <!-- Color the status bar on mobile devices -->
    <!--    <meta name="theme-color" content="rgb(0,188,212)">-->
    <link rel="stylesheet" href="../../styles-common/material.min.css">
    <!-- Material Design icons -->
    <link rel="stylesheet" href="../../styles-common/materialIcons/material-icons.css">
    <!-- Your styles -->
    <link rel="stylesheet" href="../../styles-common/styles.css">
</head>
<body>
<div class="mdl-layout mdl-js-layout
            mdl-layout--fixed-header">
    <header class="mdl-layout__header mdl-color--blue">
        <div class="mdl-layout__header-row">
            <span style="color:white" class="mdl-layout-title"><i style="vertical-align: text-bottom;" class="material-icons">public</i> Grower Management</span>
            <div class="mdl-layout-spacer"></div>
            <button id="refreshButton" style="color:white" class="mdl-button mdl-js-button mdl-button--icon">
                <i class="material-icons">sync</i>
            </button>
            <button class="mdl-button mdl-js-button mdl-button--icon">
                <a style="color: white; text-decoration: none" href="../" class="material-icons">close</a>
            </button>
        </div>
    </header>

    <div id="locationsBar" class="mdl-layout__drawer">
        <span class="mdl-layout-title"></span>
        <!-- Static All Rooms button -->
        <nav class="mdl-navigation">
            <div style="position:relative; overflow:hidden; text-overflow: ellipsis;" class="mdl-js-ripple-effect mdl-navigation__link">
                <span class="mdl-ripple"></span>
                Growers View
            </div>
        </nav>
    </div>

    <main class="mdl-layout__content mdl-color--grey-200">
        <table style="    width: calc(100% - 30px);
    border: 1px solid black;
    margin: 15px;
    text-align: center;">
            <tr>
                <th>Farm</th>
                <th>Block</th>
                <th>Variety</th>
                <th>Strain</th>
                <th>Delete</th>
            </tr>
            <?php
            while($grower = mysqli_fetch_assoc($growerRows)){
                echo "<tr ".($grower['isFinished'] > 0 ? "style='background-color: lightblue'" : ($grower['isDeleted'] > 0 ? "style='background-color: lightpink'" : '')).">
               <td>".$grower['farmName']."</td> 
               <td>".$grower['BlockDesc']."</td> 
               <td>".$grower['VarietyName']."</td> 
               <td>".$grower['strainName']."</td> 
               <td><span class='material-icons'>" .($grower['isDeleted'] > 0 ? 'restore' : 'delete'). "</span></td>
                </tr>";
            }
            ?>
        </table>
    </main>
</div>
<div id='snackbar' style='z-index: 100' class="mdl-js-snackbar mdl-snackbar">
    <div class="mdl-snackbar__text"></div>
    <button class="mdl-snackbar__action" type="button"></button>
</div>
</body>
<script src="../../scripts-common/material.min.js"></script>
<script src="../../scripts-common/vue.min.js"></script>
<script src="../../scripts-common/jquery.min.js"></script>
<script src="../../scripts-common/vue.min.js"></script>
<script src="../../scripts-common/Chart.min.js"></script>
<script>
    function snack(message, length) {
        var data = {
            message: message,
            timeout: length
        };
        document.querySelector('#snackbar').MaterialSnackbar.showSnackbar(data);
    }
</script>
</html>