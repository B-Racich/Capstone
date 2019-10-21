<?php

    include_once __DIR__.'/../../public_html/model/UserAccount.php';
    include_once __DIR__.'/../../public_html/model/Student.php';
    include_once __DIR__.'/../../public_html/model/Administrator.php';
    include_once __DIR__.'/../../public_html/model/TA.php';

    if(!isset($_SESSION)) {
        session_start();
    }
    else if(isset($_SESSION['User'])) {
        $accountType =  unserialize($_SESSION['User'])->getAccountType() ;

        switch($accountType) {
            case 'student':
                include("header-student.html");
                break;
            case 'administrator':
                include("header-administrator.html");
                break;
            default:
                include("header-basic.html");
                break;
        }

    }
    else {
        include("header-basic.html");
    }

    if(isset($_SESSION['msg']['error']) && count($_SESSION['msg']['error']) > 0) {
        echo '<div class="error">';
        echo '<div class="message-wrapper">';
        echo '<ul>';
        foreach($_SESSION['msg']['error'] as $msg) {
            echo '<li><h4>'.htmlspecialchars($msg).'</h4></li>';
        }
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        $_SESSION['msg']['error'] = array();
    }

    if(isset($_SESSION['msg']['success']) && count($_SESSION['msg']['success']) > 0) {
        echo '<div class="success">';
        echo '<div class="message-wrapper">';
        echo '<ul>';
        foreach($_SESSION['msg']['success'] as $msg) {
            echo '<li><h4>'.htmlspecialchars($msg).'</h4></li>';
        }
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        $_SESSION['msg']['success'] = array();
    }

    include("../model/redirect.php");
