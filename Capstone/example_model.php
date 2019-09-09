<?php

//  Include our needed files
include_once __DIR__.'/../../lib/Database.class.php';
include_once __DIR__.'/../../public_html/model/...php';

//  Check if our session is set
if(!isset($_SESSION)) {
    session_start();
}

//  Check if our functionCall and User are set
if(isset($_POST['functionCall']) && isset($_SESSION['User'])) {
    //  Get the POST functionCall variable that is to be called
    $functionCall = $_POST['functionCall'];

    //  Create a new AdminFunction object
    $ob = new example_model($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}  else if(!isset($_SESSION['User'])) {
    header('location: ../index.php');
}

class example_model {

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

    //  Use our functionCall variable to call a function
    public function callFunction() {
        switch ($this->functionCall) {
            case    'foo':
                $this->results = $this->foo();
                break;
            case    'bar':
                $this->results = $this->bar();
                break;
        }
    }
    
    //  Example oop function
    private function foo() {
        $student = new Student();
        $student->loadStudent("some_student");
        //  do some stuff with our object
        //  some data  $data =
        return $data;
    }
    
    //  Example functional function
    private function bar() {
        //  ex, do some sql and get some data or build some html, etc
        return $data;
    }
}
