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
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage Insights</title>

    <!-- Color the status bar on mobile devices -->
    <meta name="theme-color" content="#2F3BA2">

    <link rel="stylesheet" href="../styles-common/material.min.css">

    <!-- Material Design icons -->
    <link rel="stylesheet" href="../styles-common/materialIcons/material-icons.css">

    <!-- Your styles -->
    <link rel="stylesheet" href="../styles-common/styles.css">
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer
            mdl-layout--fixed-header">
    <header class="mdl-layout__header">
        <div class="mdl-layout__header-row">
            <span style="color:white" class="mdl-layout-title">Storage Insights</span>
            <div class="mdl-layout-spacer"></div>
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable
                  mdl-textfield--floating-label mdl-textfield--align-right">
                <label class="mdl-button mdl-js-button mdl-button--icon" for="fixed-header-drawer-exp">
                    <i class="material-icons">search</i>
                </label>
                <div class="mdl-textfield__expandable-holder">
                    <input class="mdl-textfield__input" type="text" name="sample" id="fixed-header-drawer-exp">
                </div>
            </div>
        </div>
    </header>
    <div class="mdl-layout__drawer">
        <span class="mdl-layout-title">Locations</span>

        <nav id="locationBar" class="mdl-navigation">
            <a v-for="room in rooms" class="mdl-navigation__link" href="">{{ room.roomname }}</a>
        </nav>
    </div>
    <main class="mdl-layout__content">
        <div class="page-content">
            <!-- Your content goes here -->

        </div>
    </main>
</div>
</body>
<script src="../scripts-common/material.min.js"></script>
<script src="../scripts-common/vue.min.js"></script>
<script src="../scripts-common/jquery.min.js"></script>
<script language="JavaScript">
    var rooms = new Vue({
        el: "#locationBar",
        data: {
            items: []
        },
        mounted: function(){
            $.get('API/getRooms.php', function(data) {
                self.items = data;
            });
        }
    });
</script>
</html>