<?php

    include_once __DIR__.'/../../public_html/model/Administrator.php';
    include_once __DIR__.'/../../public_html/model/Student.php';

    //  These arrays hold the pages that are allowed to be visited by the corresponding account

    $userPage = array("index.php", "login.php", "create-account.php", "forgot-password.php", "reset-password.php", "404.php");
    $studentPages = array("ta-application.php", "ta-single.php", "ta-portal.php", "index.php", "ta-edit.php", "account-edit.php", "404.php");

    $administratorPages = array("admin-course-list.php", "admin-course-single.php", "admin-create-admin.php",
    "admin-edit-TA.php", "admin-portal.php", "admin-set-restrictions.php", "admin-ta-list.php", "admin-upload-courses.php",
    "admin-view-applications.php", "autoSched.php", "reviewCourses.php","ta-single.php", "ta-edit.php", "account-edit.php",
    "admin-add-course-section.php", "admin-view-transcript.php", "admin-view-schedule.php", "admin-review-schedule.php", "404.php");

    if(isset($_SESSION['User'])) {
        $accType = unserialize($_SESSION['User'])->getAccountType();

        switch($accType) {
            case 'student':
                if(!in_array(basename($_SERVER['PHP_SELF']), $studentPages)) {
                    header('location: ../ta-portal.php');
                    exit();
                }
                break;
            case 'administrator':
                $parts = explode('/', $_SERVER["SCRIPT_NAME"]);
                $file = $parts[count($parts) - 1];
                if(!in_array(basename($_SERVER['PHP_SELF']), $administratorPages)) {
                    header('location: ../admin-portal.php');
                    exit();
                }
                else if(!in_array(basename($file), $administratorPages)) {
                    header('location: ../admin-portal.php');
                    exit();
                }
                break;
            default:
                if(!in_array(basename($_SERVER['PHP_SELF']), $userPage)) {
                    header('location: ../index.php');
                    exit();
                }
                break;
        }
    } else if(!isset($_SESSION['User'])) {
        if(!in_array(basename($_SERVER['PHP_SELF']), $userPage)) {
            header('location: ../index.php');
            exit();
        }
    }
