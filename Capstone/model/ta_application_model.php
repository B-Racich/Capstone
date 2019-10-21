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

if(isset($_POST['functionCall'])) {
    //  Get the POST functionCall variable that is to be called
    $functionCall = $_POST['functionCall'];

    //  Create a new AdminFunction object
    $ob = new ta_application_model($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}

class ta_application_model {

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
            case 'getLastApplication':
                $this->results = $this->callLastApplication();
                break;
            case 'hasApplication':
                $this->results = $this->callHasApplication();
                break;
            case 'submitApplication':
                $this->results = $this->submitApplication();
                break;
        }
    }

    private function callLastApplication() {
        if(isset($_SESSION['appStatus']))
            return $_SESSION['appStatus'];
        else
            return null;
    }

    private function callHasApplication() {
        $this->user = unserialize($_SESSION['User']);
        $this->user->loadApplications();
        return $this->user->hasApplication($_POST['session']);
    }

    private function submitApplication() {
        //  Builds a new application, if $_POST['submitApp'] is set will be submitted
        $application = new Application();

        if(isset($_SESSION['appStatus']))
            return $_SESSION['appStatus'];
    }
}