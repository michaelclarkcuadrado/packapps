<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 7/26/17
 * Time: 1:29 PM
 */
require '../config.php';
packapps_authenticate_user('storage');
?>
<!doctype html>
<html lang="en" xmlns:v-bind="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage Insights</title>

    <!-- Color the status bar on mobile devices -->
    <!--    <meta name="theme-color" content="rgb(0,188,212)">-->
    <link rel="stylesheet" href="../styles-common/material.min.css">
    <!-- Material Design icons -->
    <link rel="stylesheet" href="../styles-common/materialIcons/material-icons.css">
    <!-- Your styles -->
    <link rel="stylesheet" href="../styles-common/styles.css">
    <!-- Sunburst styling -->
    <link rel="stylesheet" href="../styles-common/sunburst/sunburst.css">
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer
            mdl-layout--fixed-header">
    <header class="mdl-layout__header">
        <div class="mdl-layout__header-row">
            <span style="color:white" class="mdl-layout-title"><i style="vertical-align: text-bottom;" class="material-icons">track_changes</i> Storage Insights</span>
            <div class="mdl-layout-spacer"></div>
            <i style="color:white; margin-right:10px" class="material-icons">settings</i>
            <button class="mdl-button mdl-js-button mdl-button--icon">
                <a style="color: white; text-decoration: none" href="/" class="material-icons">close</a>
            </button>
        </div>
    </header>
    <div id="locationsBar" class="mdl-layout__drawer">
        <template v-for="building in buildings">
            <span class="mdl-layout-title">{{ building.building_name }}</span>
            <nav class="mdl-navigation">
                <div style="position:relative;overflow: hidden" v-for="(room, index) in building.rooms" class="mdl-js-ripple-effect mdl-navigation__link">
                    <span class="mdl-ripple"></span>
                    <div v-bind:class="{ availabilityDotGreen: (room.isAvailable > 0), availabilityDotRed: (room.isAvailable == 0) }"></div>
                    {{ room.room_name }}
                    <br>
                    <div style="font-size: x-small" class="dateSubtitle">
                        <span v-if="(room.isAvailable > 0)">Opened: </span>
                        <span v-else>Closed: </span>
                        {{ room.lastAvailabilityChange }} days ago
                    </div>
                </div>
            </nav>
        </template>
    </div>
    <main class="mdl-layout__content">
        <div class="page-content">
            <!-- Your content goes here -->
            <div id="sunburst_wrapper" style="text-align: center;width: 100%">
                <div id="sunburst" style="display:inline-block">
                    <div id="sequence"></div>
                    <div id="chart">
                        <div id="explanation" style="visibility: hidden;">
                            <span id="percentage"></span><br/>
                            of the bushels in this room
                        </div>
                    </div>
                    <!--                <input type="checkbox" id="togglelegend"> Legend<br/>-->
                    <!--                <div id="legend" style="visibility: hidden;"></div>-->
                </div>
            </div>
        </div>
    </main>
</div>
</body>
<script src="../scripts-common/material.min.js"></script>
<script src="../scripts-common/vue.min.js"></script>
<script src="../scripts-common/jquery.min.js"></script>
<script src="../scripts-common/sunburst/d3.js"></script>
<script src="sequences.js"></script>
<script language="JavaScript">
    var isInAllRoomView = true;
    var currentRoomInView = 0;
    var locations = new Vue({
        el: "#locationsBar",
        data: {
            buildings: []
        },
        mounted: function(){
            var self = this;
            $.getJSON('API/getRooms.php', function(data) {
                self.buildings = data;
            });
        },
        updated: function(){
            componentHandler.upgradeDom();
        }
    });
</script>
</html>