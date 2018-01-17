<?
require '../config.php';
//authentication
if (!isset($_COOKIE['auth']) || !isset($_COOKIE['username'])) {
    die("<script>window.location.replace('/')</script>");
} else if (!hash_equals($_COOKIE['auth'], crypt($_COOKIE['username'], $securityKey))) {
    die("<script>window.location.replace('/')</script>");
} else {
    $SecuredUserName = mysqli_real_escape_string($mysqli, $_COOKIE['username']);
}
// end authentication
header("Content-Type: text/plain");
if(isset($_POST['message']))
{
    mysqli_query($mysqli, "INSERT INTO production_chat (`Line`, `User`, `Message`) VALUES ('".mysqli_real_escape_string($mysqli, $_POST['line'])."', '".mysqli_real_escape_string($mysqli, $SecuredUserName)."', '".mysqli_real_escape_string($mysqli, $_POST['message'])."')") or error_log(mysqli_error($mysqli));
}

$keepOnline = mysqli_query($mysqli, "UPDATE production_UserData SET `chatLastOnline_" . mysqli_real_escape_string($mysqli, $_POST['line']) . "`=CURRENT_TIMESTAMP() WHERE UserName='" . mysqli_real_escape_string($mysqli, $SecuredUserName) . "'");

$messages = mysqli_query($mysqli, "select * from (Select `ID`, `User`, Message FROM production_chat WHERE Line='".mysqli_real_escape_string($mysqli, $_POST['line'])."' ORDER BY ID DESC LIMIT 11 ) t1 order by ID ASC") or die(mysqli_error($mysqli));
$curOnline = mysqli_query($mysqli, "SELECT UserName from production_UserData where `chatLastOnline_" . mysqli_real_escape_string($mysqli, $_POST['line']) . "` >= NOW() - INTERVAL 6 SECOND");
echo "NOW ONLINE: ";
while($users = mysqli_fetch_array($curOnline))
{
        echo $users['UserName'] . ' | ';
}
echo "\n\n";
while($messageout=mysqli_fetch_array($messages))
{
    echo $messageout['User'].": ".$messageout['Message']."\n";
}