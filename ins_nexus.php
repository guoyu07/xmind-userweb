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
        <link rel="stylesheet" type="text/css" href="resources/featherlight/featherlight.min.css">
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
                    <a class="mdl-navigation__link" href="ins_intro.php">Install</a>
                    <a class="mdl-navigation__link" href="gauth.php?logout">Logout</a>
                </nav>
            </div>
            <main class="mdl-layout__content">
                <div class="xmind-content__posts mdl-grid">
                    <div class="mdl-card user-install-title mdl-cell mdl-cell--12-col mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">開啟安裝外部APK</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            安裝前準備工作
                        </div>
                    </div>
                </div>
                <div class="xmind-content__posts mdl-grid">
                    <div class="mdl-card user-install-step mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon">
                                <img class="step-img image-lightbox" src="imgs/Nexus_InstallSetting_01.png" data-featherlight="imgs/Nexus_InstallSetting_01.png">
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <p class="install_subject">開啟安裝外部APK</p>
                            1. 開啟設定中的 個人 / 安全性。
                        </div>
                    </div>
                    <div class="mdl-card user-install-step mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon">
                                <img class="step-img image-lightbox" src="imgs/Nexus_InstallSetting_02.png" data-featherlight="imgs/Nexus_InstallSetting_02.png">
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <p class="install_subject">開啟安裝外部APK</p>
                            2. 點擊開啟「允許安裝 Play 商店以外的來源提供的應用程式」。
                        </div>
                    </div>
                    <div class="mdl-card user-install-step mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon">
                                <img class="step-img image-lightbox" src="imgs/Nexus_InstallSetting_03.png" data-featherlight="imgs/Nexus_InstallSetting_03.png">
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <p class="install_subject">開啟安裝外部APK</p>
                            3. 開啓設定之後，會彈出提示內容。<br>
                            4. 選擇「確定」完成安裝外部APK的設定。
                        </div>
                    </div>
                </div>
                <div class="xmind-content__posts mdl-grid">
                    <div class="mdl-card user-install-title mdl-cell mdl-cell--12-col mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">下載與安裝 XMind 應用程式</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            下載、安裝、執行
                        </div>
                    </div>
                </div>
                <div class="xmind-content__posts mdl-grid">
                    <div class="mdl-card user-install-step mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon">
                                <img class="step-img image-lightbox" src="imgs/" data-featherlight="">
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <p class="install_subject">下載與安裝 XMind 應用程式</p>
                            1. 掃描上面的 QR Code 或直接點擊下面的網址下載。
                        </div>
                    </div>
                    <div class="mdl-card user-install-step mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon">
                                <img class="step-img image-lightbox" src="imgs/Nexus_Install_01.png" data-featherlight="imgs/Nexus_Install_01.png">
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <p class="install_subject">下載與安裝 XMind 應用程式</p>
                            2.  下載完成後開始進行安裝，出現授權頁面，點擊「下一步」繼續進行安裝。
                        </div>
                    </div>
                    <div class="mdl-card user-install-step mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon">
                                <img class="step-img image-lightbox" src="imgs/Nexus_Install_02.png" data-featherlight="imgs/Nexus_Install_02.png">
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <p class="install_subject">下載與安裝 XMind 應用程式</p>
                            3. 點擊「安裝」繼續進行安裝。
                        </div>
                    </div>
                    <div class="mdl-card user-install-step mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon">
                                <img class="step-img image-lightbox" src="imgs/Nexus_Install_04.png" data-featherlight="imgs/Nexus_Install_04.png">
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <p class="install_subject">下載與安裝 XMind 應用程式</p>
                            4. 安裝完成，點擊「完成」確認完成安裝。
                        </div>
                    </div>
                    <div class="mdl-card user-install-step mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <div class="mobile-main_icon">
                                <img class="step-img image-lightbox" src="imgs/Nexus_Install_05.png" data-featherlight="imgs/Nexus_Install_05.png">
                            </div>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <p class="install_subject">下載與安裝 XMind 應用程式</p>
                            5. 回到目錄中，找到「XMind」的圖示，點擊啟動程式。
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script type="text/javascript" src="resources/featherlight/featherlight.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('.image-lightbox').featherlight({type: 'image'});
            });
        </script>
    </body>
</html>
<?php
unset($pdoConn);
