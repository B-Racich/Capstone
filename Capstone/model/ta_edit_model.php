<?php

include_once __DIR__.'/../../lib/Database.class.php';
include_once __DIR__.'/../../public_html/model/UserAccount.php';
include_once __DIR__.'/../../public_html/model/Student.php';
include_once __DIR__.'/../../public_html/model/Administrator.php';
include_once __DIR__.'/../../public_html/model/TA.php';
include_once __DIR__.'/../../public_html/model/Section.class.php';
include_once __DIR__.'/../../public_html/model/matrix.php';

if(!isset($_SESSION)) {
    session_start();
}

//  Get the POST functionCall variable that is to be called
$functionCall = $_POST['functionCall'];

//  Create a new AdminFunction object
$ob = new ta_edit_model($functionCall);

//  Run the function
$ob->callFunction();

//  Return to ajax
$ob->echoResults();

class ta_edit_model {

    private $functionCall;  //  function called
    private $results;   //  output from function

    public function __construct($functionCall) {
        $this->functionCall = $functionCall;
    }

    /**
     * Database connections for SQL queries
     */
    protected function openConn() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    protected function closeConn() {
        $this->conn = null;
        $database = null;
    }

    public function getFunctionCall() {
        return $this->functionCall;
    }

    public function echoResults() {
        echo $this->results;
    }

    public function callFunction() {
        switch ($this->functionCall) {
            case 'changeFirstName':
                $this->results = $this->callChangeFirstName();
                break;
            case 'changeLastName':
                $this->results = $this->callChangeLastName();
                break;
            case 'changeEmail':
                $this->results = $this->callChangeEmail();
                break;
            case 'changePassword':
                $this->results = $this->callChangePassword();
                break;
        }
    }

    private function callChangeFirstName() {
        $this->user = unserialize($_SESSION['User']);

        $string = $_POST['string'];

        return $this->user->changeFirstName($string);
    }

    private function callChangeLastName() {
        $this->user = unserialize($_SESSION['User']);

        $string = $_POST['string'];

        return $this->user->changeLastName($string);
    }

    private function callChangeEmail() {
        $this->user = unserialize($_SESSION['User']);

        $string = $_POST['string'];

        return $this->user->changeEmail($string);
    }

    private function callChangePassword() {
        $this->user = unserialize($_SESSION['User']);

        $password = $_POST['password'];
        $newPassword = $_POST['newPassword'];

        return $this->user->changePassword($password, $newPassword);
    }
}