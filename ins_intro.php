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
        <link rel="shortcut icon" type="image/png" href="imgs/xmind-v1.png">
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
                        echo '<a class="mdl-navigation__link" href="index.php?uh=' . $model['UHID'] . '"><i class="material-icons">smartphone</i> ' . $model['Model'] . '<br>' . str_replace('Android ', '', $model['AndroidVersion']) . '</a>';
                    }
                    ?>
                    <a class="mdl-navigation__link" href="#">Install</a>
                    <a class="mdl-navigation__link" href="gauth.php?logout">Logout</a>
                </nav>
            </div>
            <main class="mdl-layout__content">
                <div class="xmind-content__posts mdl-grid">
                    <div class="mdl-card user-install mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon"><i class="material-icons md-168">smartphone</i>
                                <h4 class="mobile_title">Nexus</h4>
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Android Version：5.1.1
                        </div>
                        <div class="mdl-card__actions mdl-card--border">
                            <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="ins_nexus.php">
                                安裝說明
                            </a>
                        </div>
                    </div>
                    <div class="mdl-card user-install mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon"><i class="material-icons md-168">smartphone</i>
                                <h4 class="mobile_title">SONY</h4>
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Android Version：5.1.1
                        </div>
                        <div class="mdl-card__actions mdl-card--border">
                            <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect"  href="ins_sony.php">
                                安裝說明
                            </a>
                        </div>
                    </div>
                    <div class="mdl-card user-install mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon"><i class="material-icons md-168">smartphone</i>
                                <h4 class="mobile_title">hTC</h4>
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Android Version：5.0.2
                        </div>
                        <div class="mdl-card__actions mdl-card--border">
                            <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect"  href="ins_htc.php">
                                安裝說明
                            </a>
                        </div>
                    </div>
                    <div class="mdl-card user-install mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon"><i class="material-icons md-168">smartphone</i>
                                <h4 class="mobile_title">Samsung</h4>
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Android Version：5.1.1
                        </div>
                        <div class="mdl-card__actions mdl-card--border">
                            <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="ins_samsung.php">
                                安裝說明
                            </a>
                        </div>
                    </div>
                    <div class="mdl-card user-install mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon"><i class="material-icons md-168">smartphone</i>
                                <h4 class="mobile_title">LG</h4>
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Android Version：5.1.1
                        </div>
                        <div class="mdl-card__actions mdl-card--border">
                            <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="ins_lg.php">
                                安裝說明
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
<?php
unset($pdoConn);
