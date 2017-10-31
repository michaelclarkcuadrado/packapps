<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/10/17
 * Time: 6:06 AM
 */
require '../../config.php';
$userinfo = packapps_authenticate_user();
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
    <header class="mdl-layout__header">
        <div class="mdl-layout__header-row">
            <span style="color:white" class="mdl-layout-title"><i style="vertical-align: text-bottom;" class="material-icons">public</i> Grower Management</span>
            <div class="mdl-layout-spacer"></div>
            <button id="refreshButton" style="color:white" class="mdl-button mdl-js-button mdl-button--icon">
                <i class="material-icons">sync</i>
            </button>
            <button class="mdl-button mdl-js-button mdl-button--icon">
                <a style="color: white; text-decoration: none" href="/" class="material-icons">close</a>
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
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col-desktop mdl-cell--4-col-phone mdl-card mdl-shadow--4dp">
            <div class="mdl-card__title mdl-color--blue">
                <h2 class="mdl-card__title-text mdl-color-text--white">
                    R & L Orchard Co
                </h2>
            </div>
            <div class="mdl-card__supporting-text">
                <div style="padding:8px; width: fit-content; display: inline-block" class="mdl-shadow--4dp">
                    <div class="mdl-typography--font-bold">Last Login</div>
                    <div class="">2 Years Ago</div>
                </div>
                <div style="padding:8px; width: fit-content; display: inline-block" class="mdl-shadow--4dp">
                    <div class="mdl-typography--font-bold">Percent of your estimated deliveries</div>
                    <div class="mdl-textfield--align-right">12%</div>
                </div>
                <div style="padding:8px; width: fit-content; display: inline-block" class="mdl-shadow--4dp">
                    <div class="mdl-typography--font-bold">Largest Varieties</div>
                    <div class="mdl-textfield--align-right">Red Delicious, Golden Delicious, Fuji</div>
                </div>
                <div style="padding:8px; width: fit-content; display: inline-block" class="mdl-shadow--4dp">
                    <div class="mdl-typography--font-bold">Web Portal Username</div>
                    <div class="mdl-textfield--align-right">RL</div>
                </div>
                <div style="padding:8px; width: fit-content; display: inline-block" class="mdl-shadow--4dp">
                    <div class="mdl-typography--font-bold">Contact Email</div>
                    <div class="mdl-textfield--align-right">Not Yet Set</div>
                </div>
                <Br>
                <div style="width: 405px; height: 205px; margin: 15px; display:inline-block" class="mdl-color-text--white mdl-color--blue">
                    Year-On-Year Chart
                </div>
                <div style="width: 405px; height: 205px; margin: 15px; display:inline-block" class="mdl-color-text--white mdl-color--blue">
                    Pie Chart: Commodity->Varieties Delivered This Year
                </div>
            </div>
            <div class="mdl-card__actions mdl-card--border">
                <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                    Reset Password
                </a>
                <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                    Suspend Client Login
                </a>
                <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                    Require Password Change
                </a>
            </div>
        </div>
    </div>
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
<script>
    var growerListingVue = new Vue({
        elem: '',
        data: {

        },
        mounted: {

        }
    });

    function millisecondsToStr (inSeconds) {
        function numberEnding (number) {
            return (number > 1) ? 's' : '';
        }
        var temp = Math.floor(inSeconds);
        var years = Math.floor(temp / 31536000);
        if (years) {
            return years + ' year' + numberEnding(years);
        }
        var days = Math.floor((temp %= 31536000) / 86400);
        if (days) {
            return days + ' day' + numberEnding(days);
        }
        var hours = Math.floor((temp %= 86400) / 3600);
        if (hours) {
            return hours + ' hour' + numberEnding(hours);
        }
        var minutes = Math.floor((temp %= 3600) / 60);
        if (minutes) {
            return minutes + ' minute' + numberEnding(minutes);
        }
        var seconds = temp % 60;
        if (seconds) {
            return seconds + ' second' + numberEnding(seconds);
        }
        return 'Never Logged In'; //'just now' //or other string you like;
    }
</script>
</html>