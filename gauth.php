<?php
session_start();
require_once './inc/setup.inc.php';

// Include Google API init file
require_once _APP_PATH . 'inc/gAuth.inc.php';

//Logout
if (isset($_REQUEST['logout'])) {
    unset($_SESSION['access_token']);
    $gClient->revokeToken();
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL)); //redirect user back to page
}

//Authenticate code from Google OAuth Flow
//Add Access Token to Session
if (isset($_GET['code'])) {
    $gClient->authenticate($_GET['code']);
    $_SESSION['access_token'] = $gClient->getAccessToken();
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

//Set Access Token to make Request
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $gClient->setAccessToken($_SESSION['access_token']);
}

//Get User Data from Google Plus
//If New, Insert to Database
if ($gClient->getAccessToken()) {
    $userData = $objOAuthService->userinfo->get();
    try {
        if (!empty($userData)) {
            require_once _APP_PATH . 'classes/myPDOConn.Class.php';
            require_once _APP_PATH . 'classes/Authentication.Class.php';
            $pdoConn = \ninthday\niceToolbar\myPDOConn::getInstance('xmindPDOConnConfig.inc.php');
            $objUserAuth = new \ninthday\niceToolbar\Authentication($pdoConn);

            if ($objUserAuth->isExistandActived($userData)) {
                $_SESSION['access_token'] = $gClient->getAccessToken();
                header('Location: index.php');
            } else {
                $strMesg = "Your Account is not Active, Please contact adminstrator, thx!";
            }
        }
    } catch (Exception $exc) {
        echo $exc->getMessage();
    }
} else {
    $authUrl = $gClient->createAuthUrl();
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
        <title><?php _WEB_NAME ?></title>
        <!--        <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.0.4/material.indigo-pink.min.css">-->
        <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.0.4/material.cyan-pink.min.css" /> 
        <script src="https://storage.googleapis.com/code.getmdl.io/1.0.4/material.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="style/xmind.css">
    </head>
    <body>
        <!-- Accent-colored raised button with ripple -->
        <div class="xmind-layout-transparent mdl-layout mdl-js-layout">
            <header class="mdl-layout__header mdl-layout__header--transparent">
                <div class="mdl-layout__header-row">
                    <!-- Title -->
                    <span class="mdl-layout-title"><?php _WEB_NAME ?></span>
                    <!-- Add spacer, to align navigation to the right -->
                    <div class="mdl-layout-spacer"></div>
                    <!-- Navigation -->
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="">Link</a>
                    </nav>
                </div>
            </header>
            <div class="mdl-layout__drawer">
                <span class="mdl-layout-title">Title</span>
                <nav class="mdl-navigation">
                    <a class="mdl-navigation__link" href="">About</a>
                    <a class="mdl-navigation__link" href="">Link</a>
                    <a class="mdl-navigation__link" href="">Link</a>
                </nav>
            </div>
            <main class="mdl-layout__content" style="height: 100%;">
                <div class="xmind-login">
                    <div class="wrapper">
                        <div class="xmind-card-square mdl-card mdl-shadow--2dp">
                            <div class="mdl-card__title mdl-card--expand">
                            </div>
                            <?php if (isset($authUrl)) { ?>
                                <div class="mdl-card__supporting-text">
                                    <h5>XMind User Sign-in</h5>
                                    <img class="circle-image" src="imgs/user_circle.png">
                                    <p style="text-align: left;">Welcome to XMind User Website!<br>
                                        Please Sign-in XMind with your Google Account.</p>
                                </div>
                                <div class="mdl-card__actions mdl-card--border">
                                    <a class="mdl-button mdl-button--google mdl-js-button mdl-js-ripple-effect" href="<?php echo $authUrl; ?>">
                                        <i class="material-icons">face</i> Sign-in With Google
                                    </a>
                                </div>
                                <?php
                            } else {
                                $check_profile = $objUserAuth->isDoneProfile($userData);
                                if ($check_profile) {
                                    ?>
                                    <div class="mdl-card__supporting-text">
                                        <h5>XMind: Not Active!</h5>
                                        <img class="circle-image" src="<?php echo $userData->picture; ?>" />
                                        <p style="text-align: left;">Welcome <a href="<?php echo $userData->link; ?>" /><?php echo $userData->name; ?></a> !<br>
                                            <?php
                                            if (isset($strMesg)) {
                                                echo '<strong>' . $strMesg . '</strong><br>';
                                            }
                                            
                                            echo $userData->email; ?>
                                        </p>
                                    </div>
                                    <div class="mdl-card__actions mdl-card--border">
                                        <a class="mdl-button mdl-button--google mdl-js-button mdl-js-ripple-effect" href="?logout">
                                            <i class="material-icons">exit_to_app</i> Logout!
                                        </a>
                                    </div>
                                <?php } else { ?>
                                    <form action="saveProfile.php" method="post">
                                        <div class="mdl-card__supporting-text">
                                            <h5>XMind: Your profile!</h5>
                                            <img class="circle-image" src="<?php echo $userData->picture; ?>" >
                                            <p style="text-align: left;">Welcome <a href="<?php echo $userData->link; ?>"><?php echo $userData->name; ?></a> !
                                            </p>
                                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                                <input class="mdl-textfield__input" type="text" name="cname" id="cname" pattern="^[\u4e00-\u9fa5]+$">
                                                <label class="mdl-textfield__label" for="cname">中文姓名...</label>
                                                <span class="mdl-textfield__error">請輸入您的中文姓名</span>
                                            </div>
                                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                                <input class="mdl-textfield__input" type="email" name="email" id="email">
                                                <label class="mdl-textfield__label" for="email">電子郵件...</label>
                                                <span class="mdl-textfield__error">Wrong type.</span>
                                            </div>
                                        </div>
                                        <div class="mdl-card__actions mdl-card--border">
                                            <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--colored" type="submit">
                                                <i class="material-icons">save</i> Save Profile
                                            </button>
                                        </div>
                                    </form>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </body>
</html>
