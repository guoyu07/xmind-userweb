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
        <meta name="author" content="ninithday">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Xmind Userlog Web</title>
        <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.0.4/material.indigo-pink.min.css">
        <script src="https://storage.googleapis.com/code.getmdl.io/1.0.4/material.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet" type="text/css">
        <style>
            .xmind-layout-transparent {
                background: url('imgs/mobile-desk.jpg') center / cover;
            }
            .xmind-layout-transparent .mdl-layout__header,
            .xmind-layout-transparent .mdl-layout__drawer-button {
                /* This background is dark, so we set text to white. Use 87% black instead if
                   your background is light. */
                color: white;
            }
            .xmind-login {
                width: 90%;
                height: 90%;
                /*border: 1px solid #FF6600;*/
                text-align: center;
                margin: 0 auto;
            }

            .xmind-login:before {
                content: '';
                display: inline-block;
                vertical-align: middle ;
                height: 100%;
            }

            .wrapper {
                display: inline-block;
                vertical-align: middle;
                width: 320px;
            }

            .xmind-card-square.mdl-card {
                width: 320px;
            }
            .xmind-card-square > .mdl-card__title {
                color: #fff;
                background: url('imgs/xmind-weblogo.png') center top -20px no-repeat #03c0c6;
                height: 150px;
            }
            .mdl-button--google {
                color: #dd4b39;
            }
            .circle-image{
                width: 100px;
                height: 100px;
                -webkit-border-radius: 50%;
                border-radius: 50%;
                margin: 10px auto;
            }
            .circle-image-small{
                width: 70px;
                height: 70px;
                -webkit-border-radius: 50%;
                border-radius: 50%;
                margin: 10px auto;
            }
        </style>
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
                    <span class="mdl-layout-title">XMind</span>
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
                                ?>
                                <div class="mdl-card__supporting-text">
                                    <h5>XMind User Sign-in</h5>
                                    <img class="circle-image" src="<?php echo $userData->picture; ?>" />
                                    <p style="text-align: left;">Welcome <a href="<?php echo $userData->link; ?>" /><?php echo $userData->name; ?></a> !<br>
                                        <?php
                                    if (isset($strMesg)) {
                                        echo '<strong>' . $strMesg . '</strong><br>';
                                    }
                                    ?>
                                    <?php echo $userData->email; ?>
                                    </p>
                                </div>
                                <div class="mdl-card__actions mdl-card--border">
                                    <a class="mdl-button mdl-button--google mdl-js-button mdl-js-ripple-effect" href="?logout">
                                        <i class="material-icons">exit_to_app</i> Logout!
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </body>
</html>
