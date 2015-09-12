<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
require './inc/setup.inc.php';

// Include Google API init file
require_once _APP_PATH . 'inc/gAuth.inc.php';



//Set Access Token to make Request
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $gClient->setAccessToken($_SESSION['access_token']);
    $userData = $objOAuthService->userinfo->get();
} else {
    header('Location: ' . _WEB_ADDR . 'gauth.php');
}

$cname = filter_input(INPUT_POST, 'cname', FILTER_SANITIZE_STRING);
$user_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

try {
    if (!empty($userData)) {
        require_once _APP_PATH . 'classes/myPDOConn.Class.php';
        require_once _APP_PATH . 'classes/Authentication.Class.php';
        $pdoConn = \ninthday\niceToolbar\myPDOConn::getInstance('xmindPDOConnConfig.inc.php');
        $objUserAuth = new \ninthday\niceToolbar\Authentication($pdoConn);

        $objUserAuth->updateUser($cname, $user_email, $userData);

        header('Location: ' . _WEB_ADDR . 'gauth.php');
    }
} catch (Exception $exc) {
    echo $exc->getMessage();
}