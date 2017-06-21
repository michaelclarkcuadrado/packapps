<!DOCTYPE HTML>
<html>
<head>
    <?php
    include '../config_grower.php';
    $adminauth = mysqli_query($mysqli, "SELECT isAdmin FROM GrowerData WHERE GrowerCode='" . $_SERVER['PHP_AUTH_USER'] . "'");
    $admin = mysqli_fetch_array($adminauth);
    echo "<title>Managing Block...</title>";
    if ($_GET['PK']) {
        //delete or undelete block
        mysqli_query($mysqli, "UPDATE `crop-estimates` SET `isDeleted` = NOT `isDeleted` WHERE Grower='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "' AND PK='" . $_GET['PK'] . "'");
    } elseif ($_GET['Done']) {
        //finish or unfinish block
        mysqli_query($mysqli, "UPDATE `crop-estimates` SET `isFinished`= NOT `isFinished` WHERE Grower='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "' AND PK='" . $_GET['Done'] . "'");
    } elseif ($_GET['sameEst']) {
        //check same as last year or uncheck
        mysqli_query($mysqli, "UPDATE `crop-estimates` SET isSameAsLastYear = NOT `isSameAsLastYear` WHERE Grower='" . ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER'])) . "' AND PK='" . $_GET['sameEst'] . "'");
    }
    ?>
    <script>location.replace("index.php#estimatestable")</script>
</head>
</html>