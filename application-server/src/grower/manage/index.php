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
    <header class="mdl-layout__header mdl-color--amber-200">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title"><i style="vertical-align: text-bottom;" class="material-icons">public</i> Grower Management</span>
            <div class="mdl-layout-spacer"></div>
            <button id="refreshButton" class="mdl-button mdl-js-button mdl-button--icon">
                <i class="material-icons">sync</i>
            </button>
            <button class="mdl-button mdl-js-button mdl-button--icon">
                <a href="/" style="color:black; text-decoration: none" class="material-icons">close</a>
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
        <div id="growersListing" class="mdl-grid">
            <div v-for="grower in growerListing" class="mdl-cell mdl-cell--6-col-desktop mdl-cell--4-col mdl-card mdl-shadow--4dp">
                <div class="mdl-card__title mdl-color--amber-200">
                    <h2 class="mdl-card__title-text">
                        {{grower.growerName}}
                    </h2>
                </div>
                <div class="mdl-card__supporting-text">
                    <div style="padding:8px; width: fit-content; display: block; margin: auto; text-align: center" class="mdl-shadow--4dp">
<!--                        <b>At a Glance</b>-->
<!--                        <br><br>-->
                        <div style="padding:8px; width: fit-content; display: inline-block" class="mdl-shadow--6dp-">
                            <div class="mdl-typography--font-bold">Last Login</div>
                            <div class="">{{secondsToStr(grower.lastLogin)}}</div>
                        </div>
                        <div style="padding:8px; width: fit-content; display: inline-block" class="mdl-shadow--6dp-">
                            <div class="mdl-typography--font-bold">Percent of this year's deliveries</div>
                            <div class="mdl-textfield--align-right">{{grower.percentOfThisYear}}%</div>
                        </div>
                        <div style="padding:8px; width: fit-content; display: inline-block" class="mdl-shadow--6dp-">
                            <div class="mdl-typography--font-bold">Web Portal Username</div>
                            <div class="">{{grower.GrowerCode}}</div>
                        </div>
                        <div style="padding:8px; width: fit-content; display: inline-block" class="mdl-shadow--6dp-">
                            <div class="mdl-typography--font-bold">Contact Email</div>
                            <div class="">{{grower.login_email}}</div>
                        </div>
                    </div>
                    <Br>
                    <div class="mdl-grid">
<!--                        <div class="mdl-layout-spacer"></div>-->
                        <div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-phone mdl-cell--8-col-tablet"  style="min-height: 125px">
                            <canvas v-bind:id="grower.GrowerCode + 'growthChart'"></canvas>
                        </div>
                        <div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-phone mdl-cell--8-col-tablet" style="min-height: 125px;">
                            <canvas v-bind:id="grower.GrowerCode + 'pieChart'"></canvas>
                            {{initChart(grower.GrowerCode)}}
                        </div>
<!--                        <div class="mdl-layout-spacer"></div>-->
                    </div>
                </div>
                <div class="mdl-card__actions mdl-card--border">
                    <a v-on:click="snack('The user has been emailed a new password.', 2000)" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                        Reset Password
                    </a>
                    <a v-on:click="snack('Logins for this account are now blocked.', 2000)" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                        Suspend Client Login
                    </a>
                    <a v-on:click="snack('The user will change their password on next log in.', 2000)" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                        Require Password Change
                    </a>
                </div>
                <div class="mdl-card__menu">
                    <button class="mdl-button mdl-button--icon mdl-color-text--white mdl-js-button mdl-js-ripple-effect">
                        <a v-bind:href="'growerDrill.php?growerID=' + grower.GrowerCode"><i style="color:black" class="material-icons">open_in_new</i></a>
                    </button>
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
<script src="../../scripts-common/Chart.min.js"></script>
<script>
    var growerListingVue = new Vue({
        el: '#growersListing',
        data: {
            growerListing: []
        },
        methods: {
            secondsToStr: function (inSeconds) {
                function numberEnding(number) {
                    return (number > 1) ? 's' : '';
                }

                if (inSeconds == 0) {
                    return 'Never Logged In';
                }

                var curTimestamp = Math.round((new Date()).getTime() / 1000);
                var temp = Math.floor(inSeconds);
                temp = curTimestamp - temp;

                var years = Math.floor(temp / 31536000);
                if (years) {
                    return years + ' year' + numberEnding(years) + ' ago';
                }
                var days = Math.floor((temp %= 31536000) / 86400);
                if (days) {
                    return days + ' day' + numberEnding(days) + ' ago';
                }
                var hours = Math.floor((temp %= 86400) / 3600);
                if (hours) {
                    return hours + ' hour' + numberEnding(hours) + ' ago';
                }
                var minutes = Math.floor((temp %= 3600) / 60);
                if (minutes) {
                    return minutes + ' minute' + numberEnding(minutes) + ' ago';
                }
                var seconds = temp % 60;
                if (seconds) {
                    return seconds + ' second' + numberEnding(seconds) + ' ago';
                }
                return 'Never Logged In';
            },
            //dragons be here. Nexttick is to ready the dom, but who knows how it really works
            initChart: function (growerCode) {
                var self = this;
                this.$nextTick(function () {
                    var growthElemContext = document.getElementById(growerCode + 'growthChart').getContext("2d");
                    var pieElemContext = document.getElementById(growerCode + 'pieChart').getContext("2d");
                    var growthChartConfig = {
                        type: 'bar',
                        data: {
                            datasets: [{
                                data: function () {
                                    var array = [];
                                    for (var listing in self.growerListing[growerCode]['growthHistory']) {
                                        array.push(self.growerListing[growerCode]['growthHistory'][listing]);
                                    }
                                    return array;
                                }(),
                                backgroundColor: function () {
                                    var array = [];
                                    for (var i = 0; i < Object.keys(self.growerListing[growerCode]['growthHistory']).length; i++) {
                                        //        red: 'rgb(255, 99, 132)'
                                        //        orange: 'rgb(255, 159, 64)',
                                        //        yellow: 'rgb(255, 205, 86)',
                                        //        green: 'rgb(75, 192, 192)',
                                        //        blue: 'rgb(54, 162, 235)',
                                        //        purple: 'rgb(153, 102, 255)',
                                        //        grey: 'rgb(201, 203, 207)'
                                        array.push('rgb(255, 205, 86)');
                                    }
                                    return array;
                                }(),
                                label: 'Deliveries'
                            }],
                            labels: function () {
                                var array = [];
                                for (var listing in self.growerListing[growerCode]['growthHistory']) {
                                    array.push(listing);
                                }
                                return array;
                            }()
                        },
                        options: {
                            legend: {
                                display: false
                            },
                            responsive: true,
                            title: {
                                display: true,
                                text: 'YoY Deliveries'
                            },
                            animation: false
                        }
                    };
                    var pieChartConfig = {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: function () {
                                    var array = [];
                                    for (var listing in self.growerListing[growerCode]['bushelEstimates']) {
                                        array.push(self.growerListing[growerCode]['bushelEstimates'][listing]['value']);
                                    }
                                    return array;
                                }(),
                                backgroundColor: function () {
                                    var array = [];
                                    for (var listing in self.growerListing[growerCode]['bushelEstimates']) {
                                        array.push(self.growerListing[growerCode]['bushelEstimates'][listing]['color']);
                                    }
                                    return array;
                                }(),
                                label: 'Varieties'
                            }],
                            labels: function () {
                                var array = [];
                                for (var listing in self.growerListing[growerCode]['bushelEstimates']) {
                                    array.push(listing);
                                }
                                return array;
                            }()
                        },
                        options: {
                            legend: {
                                display: false
                            },
                            responsive: true,
                            title: {
                                display: true,
                                text: 'Expected Varieties ' + new Date().getFullYear()
                            },
                            animation: false
                        }
                    };
                    var growthChart = new Chart(growthElemContext, growthChartConfig);
                    var pieChart = new Chart(pieElemContext, pieChartConfig);
                })
            }
        },
        mounted: function () {
            var self = this;
            $.getJSON('API/getGrowerListing.php', function (data) {
                self.growerListing = data;
            });
        }
    });

    function snack(message, length) {
        var data = {
            message: message,
            timeout: length
        };
        document.querySelector('#snackbar').MaterialSnackbar.showSnackbar(data);
    }
</script>
</html>