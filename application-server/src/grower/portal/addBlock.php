<html>
<?php
include '../../config.php';
$adminauth = mysqli_query($mysqli, "SELECT isAdmin FROM grower_growerLogins WHERE GrowerCode='" . $_SERVER['PHP_AUTH_USER'] . "'");
$admin = mysqli_fetch_array($adminauth);
if (mysqli_connect_errno($mysqli)) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
echo "<title>Managing Block...</title>";

if ($_POST['VarDesc'] != "Choose Variety" and $_POST['Strain'] != "Choose Strain") {
    $user = ($admin[0] == 1 && ($_GET['pretend']) ? $_GET['pretend'] : (isset($_GET['alt_acc']) ? base64_decode($_GET['alt_acc']) : $_SERVER['PHP_AUTH_USER']));
    $stmt = mysqli_prepare($mysqli, "INSERT INTO `grower_crop-estimates` (`Comm Desc`, `Grower`, `VarDesc`, `FarmDesc`, `BlockDesc`, `Str Desc`, `" . date('Y') . "est`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssssssi', $_POST['CommDesc'], $user, $_POST['VarDesc'], $_POST['Farm'], $_POST['Block'], $_POST['Strain'], $_POST['newEst']);
    mysqli_stmt_execute($stmt);
    echo "<script>location.replace('index.php" . (($admin[0] == 1 && ($_GET['pretend'])) ? '?pretend=' . $_GET['pretend'] . "&" : (isset($_GET['alt_acc']) ? "?alt_acc=".$_GET['alt_acc'] : '')) . "#estimatestable')</script>";
} else {
    echo "Please choose a variety and a strain. <a href='javascript:history.back()'>Go Back</a>";
}
?>
</html>