<?php

include_once __DIR__.'/../../public_html/model/UserAccount.php';
include_once __DIR__.'/../../public_html/model/Student.php';
include_once __DIR__.'/../../public_html/model/Administrator.php';
include_once __DIR__.'/../../public_html/model/TA.php';

if(!isset($_SESSION)) {
    session_start();
}

/*
 * This handler file servers as a wrapper to the UserAccount Class Functions and Student/Administrator login methods for calling via AJAX in JS.
 *
 * $_POST values are replaced with local testing strings on Local Machine.
 */

/*
 * The code below is needed on the server to be run via the AJAX call
 */
//Prep handler for execution
if(isset($_POST['functionCall'])) {

    $ob = new UserHandler($_POST['functionCall']);
    //Call function
    $ob->callFunction();

    $ob->wasExecuted();
}

class UserHandler {
    private $user;

    private $executedCommand;
    private $wasExecuted = false;
    private $results;
    private $functionCall;


    public function __construct($functionCall) {
        $this->functionCall = $functionCall;
    }

    public function wasExecuted() {
        echo $this->results;
    }

    public function testExecuted() {
        return $this->results;
    }

    public function callFunction() {
        switch ($this->functionCall) {
            case 'loginAccount':
                $this->results = $this->callLoginAccount();
                $this->executedCommand = $this->functionCall;
                break;
            case 'createStudent':
                $this->results = $this->callCreateStudent();
                $this->executedCommand = $this->functionCall;
                break;
            case 'createAdministrator':
                $this->results = $this->callCreateAdministrator();
                $this->executedCommand = $this->functionCall;
                break;
            case 'isLoggedIn':
                $this->results = $this->isLoggedIn();
                $this->executedCommand = $this->functionCall;
                break;
            case 'getAccountType':
                $this->results = $this->getUserType();
                $this->executedCommand = $this->functionCall;
                break;
            case 'logout':
                $this->results = $this->logout();
                $this->executedCommand = $this->functionCall;
                break;
            case 'printInfo':
                $this->results = $this->callPrintInfo();
                $this->executedCommand = $this->functionCall;
                break;
            case 'deleteStudent':
                $this->results = $this->callDeleteStudent();
                $this->executedCommand = $this->functionCall;
                break;
            case 'deleteAdministrator':
                $this->results = $this->callDeleteAdministrator();
                $this->executedCommand = $this->functionCall;
                break;
            case 'editTaAccount':
                $this->results = $this->callEditTaAccount();
                $this->executedCommand = $this->functionCall;
                break;
            case 'loadTA':
                $this->results = $this->callLoadTA();
                $this->executedCommand = $this->functionCall;
                break;
            case 'adminLoadTA':
                $this->results = $this->callAdminLoadTA();
                $this->executedCommand = $this->functionCall;
                break;
        }
    }

    private function callCreateStudent () {
        $this->user = new Student();

        $userName = $_POST['userName'];
        $password = $_POST['password'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $sID = $_POST['sID'];
        $graduate = 0;

        return $this->user->createStudent($userName, $password, $firstName, $lastName, $email, $sID, $graduate);
    }

    private function callCreateAdministrator () {
        $this->user = new Administrator();

        $userName = $_POST['userName'];
        $password = $_POST['password'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];

        return $this->user->createAdministrator($userName, $password,$firstName, $lastName, $email);
    }

    private function callLoginAccount() {
        $this->user = new UserAccount();

        $userName = $_POST['userName'];
        $password = $_POST['password'];

        if($this->user->lookUpUser($userName) == 'administrator') {
            $this->user = new Administrator();
            $results = $this->user->loginAdministrator($userName, $password);
            return $results;
        }

        else if($this->user->lookUpUser($userName) == 'student') {
            $this->user = new Student();
            $results = $this->user->loginStudent($userName, $password);
            return $results;
        }
        else {
            return 'Invalid credentials';
        }
    }

    private function isLoggedIn() {
        if (isset($_SESSION['User']) ) {
            return 1;
        }
        else {
            return 0;
        }
    }

    private function getUserType() {
        if(isset($_SESSION['User'])) {
            return unserialize($_SESSION['User'])->getAccountType();
        }
        else
            return 'User';
    }

    private function logout() {
        //session_destroy();
        unset($_SESSION['User']);
        header("location: ../index.php");
        exit();
    }

    private function callLoadTA(){
        $this->user = new TA();
        $userName = $_SESSION['User'];
        return $this->user->loadTA($userName);
    }

    private function callPrintInfo() {
        $this->user = unserialize($_SESSION['User']);

        return $this->user->printInfo();
    }

    private function callDeleteStudent() {
        $this->user = $_SESSION['User'];    //  Used for testing

        return $this->user->deleteStudent();
    }

    private function callDeleteAdministrator() {
        $this->user = $_SESSION['User'];

        return $this->user->deleteAdministrator();
    }

    private function callEditTaAccount() {
        $this->user = unserialize($_SESSION['User']);
        return $this->user->editTaAccount($_POST['uID'],$_POST['userName'],$_POST['firstName'],$_POST['lastName'],$_POST['email'],$_POST['graduate']);
    }

    private function callAdminLoadTA() {//Hmm don't know what I was thinking but this is an Admin function and should be moved to the Administrator.php page (But that will a few things going to do this later) ---single-ta-info-controller is using this function.
        $_SESSION['Target'] = new TA();
        $_SESSION['Target']->loadTA($_POST['userName']);
        return $_SESSION['Target']->printInfo();
    }
}
