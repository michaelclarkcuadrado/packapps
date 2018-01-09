<!DOCTYPE HTML>
<html>
<head>
    <?php
    include '../../config.php';
    $userinfo = packapps_authenticate_grower();
    $BlockQA = mysqli_query($mysqli, "SELECT CommDesc AS Commodity, CASE WHEN trim(Farm)<>'' THEN Farm ELSE 'No Farm Listed' END AS Farm, CASE WHEN trim(Block)<>'' THEN Block ELSE 'No Block Listed' END AS Block, VarDesc AS Variety, Strain, Pressure1, Pressure2, Brix, DA, DA2, `Count`, Weight, CASE WHEN isnull(Starch) THEN 'Not Tested' ELSE Starch END AS Starch FROM quality_Block_Receiving WHERE Grower='" . $userinfo['GrowerCode'] . "' ORDER BY `Commodity` ASC, Variety ASC, `Count` DESC");
    echo "<title>Receiving Quality Info: " . $userinfo['GrowerCode'] . "</title>";
    ?>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="description" content="<?echo $companyName?> Grower Control Panel"/>
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
            <h1 id="title"><? echo $userinfo['GrowerCode'] ?></h1>
            <p><?echo $companyName?> Grower</p>
        </div>

        <!-- Nav -->
        <nav id="nav">
            <ul>
                <li><a href="#top" id="top-link" class="skel-layers-ignoreHref"><span class="icon fa-area-chart">Block-by-Block QA</span></a>
                </li>
                <li><a href="index.php" id="top-link"><span class="icon fa-arrow-left">Back</span></a></li>
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
                <h2 class="alt"><strong>Block-by-Block QA</strong><br/></h2>
                <p>Information and testing provided by the <?echo $companyName?> QA Lab.</p>
            </header>

            <footer>
            </footer>

        </div>
    </section>

    <section id="receiving" class="receiving three">
        <div class="container">
            <h2>Your Fruit Quality Data</h2>
            <p>Each time you deliver fruit, our inspectors carefully sample a small number of fruit for our lab to
                assess. The quality of each truckload is then determined based on a variety of factors. <br>Some of
                those metrics are shown here, compiled by block, as an average at the time of delivery.</p>
            <?
            if (mysqli_num_rows($BlockQA) == 0) {
                echo "<hr><h2>No testing has been performed yet.</h2><p>After you deliver fruit, the testing results will appear here as soon as we complete our analysis.</p>";
            } else {
                while ($BlockQAarray = mysqli_fetch_assoc($BlockQA)) {
                    echo "<span class='icon fa-eyedropper'> " . $BlockQAarray['Variety'] . "</span>";
                    echo "<table border='1px'>";
                    echo "<tr><td><b>Farm</td><td><b>Block</td><td><b>Variety</td><td><b>Strain</td></tr>";
                    echo "<tr><td>" . $BlockQAarray['Farm'] . "</td><td>" . $BlockQAarray['Block'] . "</td><td><img src='images/" . $BlockQAarray['Commodity'] . ".png'> " . $BlockQAarray['Variety'] . "</td><td>" . $BlockQAarray['Strain'] . "</td></tr>";
                    echo "<tr><td><b>Avg. Starch</td><td><b>Avg. Brix</td><td><b>Avg. DA</td><td><b>Avg. Pressure (lb)</td></tr>";
                    echo "<tr><td>" . $BlockQAarray['Starch'] . "</td><td>" . $BlockQAarray['Brix'] . "</td><td>" . round((($BlockQAarray['DA'] + $BlockQAarray['DA2']) / 2), 2) . "</td><td>" . round((($BlockQAarray['Pressure1'] + $BlockQAarray['Pressure2']) / 2), 2) . "</td></tr>";
                    echo "<tr><td colspan='2'><b>Avg. Weight (lb)</td><td colspan='2'><b>Total Samples Taken from Block</td></tr>";
                    echo "<tr><td colspan='2'>" . $BlockQAarray['Weight'] . "</td><td colspan='2'>" . $BlockQAarray['Count'] . "</td></tr>";
                    echo "</table><br>";
                }
            }
            ?>
        </div>
    </section>


</div>
<!-- Footer -->
<div id="footer">

    <!-- Copyright -->
    <ul class="copyright">
        <li>&copy;</li>
    </ul>

</div>

</body>
</html>
