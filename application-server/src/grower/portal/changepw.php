<!DOCTYPE HTML>
<html>
<head>
    <?php
    include '../../config.php';
    $userinfo = packapps_authenticate_grower();
    echo "<title>Change Password for " . $userinfo['GrowerName'] . "</title>";

    $passwordChangeMessage = "";
    if (isset($_POST['NewPassword']) && isset($_POST['NewPassword2'])) {
        $passwordChangeMessage = "Passwords did not match!";
        if ($_POST['NewPassword'] == $_POST['NewPassword2']) {
            $passHash = \WhiteHat101\Crypt\APR1_MD5::hash($_POST['NewPassword']);
            mysqli_query($mysqli, "UPDATE grower_growerLogins SET `Password`='$passHash' WHERE GrowerCode='" . $userinfo['GrowerCode'] . "'");
            $passwordChangeMessage = "Password Change Successful!";
        }
    }
    ?>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="description" content="<? echo $companyName ?> Grower Control Panel"/>
    <meta name="keywords" content=""/>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.scrolly.min.js"></script>
    <script src="js/init.js"></script>
</head>
<body>
<!-- Header -->
<div id="header" class="skel-layers-fixed">

    <div class="top">

        <!-- Logo -->
        <div id="logo">
            <span class="image"><img src="images/avatar.png" alt=""/></span>
            <h1 id="title"><? echo $userinfo['GrowerName'] ?></h1>
            <p><? echo $companyName ?> Grower</p>
        </div>

        <!-- Nav -->
        <nav id="nav">
            <ul>
                <li><a href="#top" id="top-link" class="skel-layers-ignoreHref"><span class="icon fa-key">Change Password</span></a>
                </li>
                <li><a href="index.php"><span class="icon fa-arrow-left">Back</span></a></li>
            </ul>
        </nav>

    </div>

</div>

<!-- Main -->
<div id="main">

    <!-- Intro -->
    <section id="top" class="one dark cover">
        <div class="container">

            <header>
                <h2 class="alt">Password change for account <strong><? echo $userinfo['GrowerName'] ?></strong><br/></h2>
            </header>
            <?echo "<mark>".$passwordChangeMessage."</mark>"?>
            <form class='12u' action="changepw.php" method="POST"><input name="NewPassword" placeholder="New Password"
                                                                         type="password" size="15" required autofocus>
                <br>
                <input class='6u' name="NewPassword2" placeholder="Confirm New Password" type="password" size="15"
                       required>

                <footer>
                    <input type="submit" class="button scrolly" value="Change Password">
            </form>

        </div>
    </section>


</div>
<!-- Footer -->
<div id="footer">

    <!-- Copyright -->
    <ul class="copyright">
        <li></li>
    </ul>

</div>

</body>
</html>
