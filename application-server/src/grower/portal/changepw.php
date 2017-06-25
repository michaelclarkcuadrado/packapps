<!DOCTYPE HTML>
<html>
<head>
    <?php
    include '../../config.php';
    $userinfo = packapps_authenticate_grower();
    echo "<title>Change Password for " . $userinfo['GrowerName'] . "</title>";
    ?>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="description" content="<?echo $companyName?> Grower Control Panel"/>
    <meta name="keywords" content=""/>
    <!--[if lte IE 8]>
    <script src="css/ie/html5shiv.js"></script><![endif]-->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.scrolly.min.js"></script>
    <script src="js/jquery.scrollzer.min.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
    <noscript>
        <link rel="stylesheet" href="css/skel.css"/>
        <link rel="stylesheet" href="css/style.css"/>
        <link rel="stylesheet" href="css/style-wide.css"/>
    </noscript>
    <!--[if lte IE 9]>
    <link rel="stylesheet" href="css/ie/v9.css"/><![endif]-->
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="css/ie/v8.css"/><![endif]-->
</head>
<body>
<script type="text/javascript">
    var _paq = _paq || [];
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    _paq.push(['trackVisibleContentImpressions']);
    (function () {
        var u = "<?php echo $piwikHost?>/";
        _paq.push(['setTrackerUrl', u + 'piwik.php']);
        _paq.push(['setSiteId', 1]);
        _paq.push(['setUserId', '<?echo addcslashes($growername, "'")?>']);
        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
        g.type = 'text/javascript';
        g.async = true;
        g.defer = true;
        g.src = u + 'piwik.js';
        s.parentNode.insertBefore(g, s);
    })();
</script>
<!-- Header -->
<div id="header" class="skel-layers-fixed">

    <div class="top">

        <!-- Logo -->
        <div id="logo">
            <span class="image"><img src="images/avatar.png" alt=""/></span>
            <h1 id="title"><? echo $userinfo['GrowerName'] ?></h1>
            <p><?echo $companyName?> Grower</p>
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
            <form class='12u' action="changepw2.php" method="POST"><input name="NewPassword" placeholder="New Password"
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
