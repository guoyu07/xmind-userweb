<?php
session_start();
require_once './inc/setup.inc.php';

// Include Google API init file
require_once _APP_PATH . 'inc/gAuth.inc.php';

// How many days to list until today
$days = 7;

$UHID = filter_input(INPUT_GET, 'uh', FILTER_SANITIZE_NUMBER_INT);

//Set Access Token to make Request
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $gClient->setAccessToken($_SESSION['access_token']);
    $userData = $objOAuthService->userinfo->get();
} else {
    header('Location: ' . _WEB_ADDR . 'gauth.php');
}

try {
    if (!empty($userData)) {
        require_once _APP_PATH . 'classes/myPDOConn.Class.php';
        require_once _APP_PATH . 'classes/Authentication.Class.php';
        require_once _APP_PATH . 'classes/UserLog.Class.php';
        $pdoConn = \ninthday\niceToolbar\myPDOConn::getInstance('xmindPDOConnConfig.inc.php');
        $objUserAuth = new \ninthday\niceToolbar\Authentication($pdoConn);

        if (!$objUserAuth->isExistandActived($userData)) {
            header('Location: ' . _WEB_ADDR . 'gauth.php');
        }

        $userID = $objUserAuth->getUserID($userData);
        $objUserlog = new \ninthday\XMind\UserLog($pdoConn);

        $date_list = $objUserlog->getDateList($days);
        $model_list = $objUserlog->getUserModelByUID($userID);
    } else {
        header('Location: ' . _WEB_ADDR . 'gauth.php');
    }
} catch (Exception $exc) {
    echo $exc->getMessage();
}
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html lang="zh-Hant">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="Ninthday (jeffy@ninthday.info)">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo _WEB_NAME; ?></title>
        <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.0.4/material.cyan-pink.min.css"> 
        <script src="https://storage.googleapis.com/code.getmdl.io/1.0.4/material.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="style/xmind.css">
    </head>
    <body>
        <?php
        // put your code here
        ?>
        <!-- Accent-colored raised button with ripple -->
        <div class="xmind-layout-transparent mdl-layout mdl-js-layout">
            <header class="mdl-layout__header mdl-layout__header--transparent">
                <div class="mdl-layout__header-row">
                    <!-- Title -->
                    <span class="mdl-layout-title"><img class="xmind-logo-image" src="imgs/xmind-weblogo.png">XMind Userlog</span>
                    <!-- Add spacer, to align navigation to the right -->
                    <div class="mdl-layout-spacer"></div>
                    <!-- Navigation -->
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="">About</a>
                        <a class="mdl-navigation__link" href="gauth.php?logout">Logout</a>
                    </nav>
                </div>
            </header>
            <div class="mdl-layout__drawer xmind-drawer">
                <span class="mdl-layout-title"><img class="circle-image-small" src="<?php echo $userData->picture; ?>"></span>
                <nav class="mdl-navigation">
                    <?php
                    foreach ($model_list as $model) {
                        echo '<a class="mdl-navigation__link" href="?uh=' . $model['UHID'] . '"><i class="material-icons">smartphone</i> ' . $model['Model'] . '<br>' . str_replace('Android ', '', $model['AndroidVersion']) . '</a>';
                    }
                    ?>
                    <a class="mdl-navigation__link" href="">Link</a>
                    <a class="mdl-navigation__link" href="gauth.php?logout">Logout</a>
                </nav>
            </div>
            <main class="mdl-layout__content">
                <div class="xmind-content__posts mdl-grid">
                    <?php
                    foreach ($date_list as $assign_date) {
                        $i = 1;
                        $receive = 0;
                        $day_userlog = $objUserlog->getUserLogByDate($userID, $UHID, $assign_date);
                        ?>
                        <div class="mdl-card user-upload-log mdl-cell mdl-cell--12-col mdl-shadow--2dp">
                            <div class="mdl-card__title">
                                <h2 class="mdl-card__title-text"><?php echo $assign_date; ?></h2>
                            </div>
                            <table class="mdl-data-table mdl-js-data-table mdl-color-text--grey-600 xmind-full-width">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Log ID</th>
                                        <th class="mdl-data-table__cell--non-numeric">Model</th>
                                        <th class="mdl-data-table__cell--non-numeric">Android</th>
                                        <th class="mdl-data-table__cell--non-numeric">Upload Time</th>
                                        <th>Receive</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($day_userlog as $row) {
                                        echo '<tr>';
                                        echo '<td>' . $i . '</td>';
                                        echo '<td>' . $row['logID'] . '</td>';
                                        echo '<td class="mdl-data-table__cell--non-numeric">' . $row['Model'] . '</td>';
                                        echo '<td class="mdl-data-table__cell--non-numeric">' . $row['AndroidVersion'] . '</td>';
                                        echo '<td class="mdl-data-table__cell--non-numeric">' . $row['uploadTime'] . '</td>';
                                        echo '<td>' . $row['receiveCount'] . '</td>';
                                        echo '</tr>';
                                        $i++;
                                        $receive += $row['receiveCount'];
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div class="mdl-card__actions mdl-card--border">
                                <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                                    <?php echo 'Total times: ' . strval($i - 1) . ' , Receive probes: ' . number_format($receive); ?>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </main>
        </div>
    </body>
</html>
