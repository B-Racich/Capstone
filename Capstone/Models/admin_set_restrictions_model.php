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
    $ob = new admin_set_restrictions_model($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}

class admin_set_restrictions_model {

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

    public function callFunction()
    {
        switch ($this->functionCall) {
            case    'getCourseRestrictions':
                $this->results = $this->getCourseRestrictions();
                break;
            case    'updateCourseRestrictions':
                $this->results = $this->updateCourseRestrictions();
                break;
            case    'getCourseInfo':
                $this->results = $this->getCourseInfo();
                break;
            case    'getPreReqs':
                $this->results = $this->getPreReqs();
                break;
        }
    }

    /** Gets the selected courses restrictions if present
     * @return JSON object of restrictions
     */
    private function getCourseRestrictions() {
        $this->openConn();
        $sql = "SELECT labHours, markingHours, prepHours, otherHours, minAvgOverall, minAvgInSubject, minCredits, UTAminGrade, prereq FROM Course WHERE courseID = :courseID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':courseID'=>$_POST['courseID']));

        $json = array();

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $json['labHours'] = $row['labHours'];
            $json['markingHours'] = $row['markingHours'];
            $json['prepHours'] = $row['prepHours'];
            $json['otherHours'] = $row['otherHours'];
            $json['minAvgOverall'] = $row['minAvgOverall'];
            $json['minAvgInSubject'] = $row['minAvgInSubject'];
            $json['minCredits'] = $row['minCredits'];
            $json['UTAminGrade'] = $row['UTAminGrade'];
            $json['prereq'] = $row['prereq'];
            $json = json_encode($json);
        }
        $this->closeConn();
        return $json;
    }

    /** Update the course in the db with provided restrictions
     * @return bool
     */
    private function updateCourseRestrictions() {
        $this->openConn();

        $json = json_decode(stripslashes($_POST['json']), true);

        $courseID = $_POST['courseID'];
        //  Hard coded indexing of fields since these are based on sql table columns within 'Course'
        $labHours = $json['labHours'];
        $markingHours = $json['markingHours'];
        $prepHours = $json['prepHours'];
        $otherHours = $json['otherHours'];
        $minAvgOverall = $json['minAvgOverall'];
        $minAvgInSubject = $json['minAvgInSubject'];
        $minCredits = $json['minCredits'];
        $UTAminGrade = $json['UTAminGrade'];
        $prereq = $json['prereq'];

        $sqlUpdate = "UPDATE Course SET labHours = :labHours, markingHours = :markingHours, prepHours = :prepHours, otherHours = :otherHours, minAvgOverall = :minAvgOverall, minAvgInSubject = :minAvgInSubject, minCredits = :minCredits, UTAminGrade = :UTAminGrade, prereq = :prereq WHERE courseID = :courseID";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);
        $stmtUpdate->execute(array(':labHours'=>$labHours,':markingHours'=>$markingHours,':prepHours'=>$prepHours,':otherHours'=>$otherHours,':courseID'=>$courseID, ':minAvgOverall'=>$minAvgOverall, ':minAvgInSubject'=>$minAvgInSubject, ':minCredits'=>$minCredits, ':UTAminGrade'=>$UTAminGrade, ':prereq'=>$prereq));

        if($stmtUpdate->rowCount() > 0) {
            $this->closeConn();
            return true;
        }
        else {
            $this->closeConn();
            return false;
        }
    }

    private function getCourseInfo() {
        $this->openConn();
        $sql = "SELECT subject, courseNum FROM Course WHERE courseID = :courseID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(":courseID"=>$_POST['courseID']));

        $coursesArr= array();

        $rowArr = array();;

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rowArr['subject'] = $row['subject'];
            $rowArr['courseNum'] = $row['courseNum'];
            $coursesArr[] = $rowArr;
            $rowArr = array();
        }

        $this->closeConn();
        return json_encode($coursesArr);
    }

    private function getPreReqs() {
        $this->openConn();
        $sql = "SELECT courseID, subject, courseNum FROM Course WHERE courseID != :courseID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(":courseID"=>$_POST['courseID']));

        $coursesArr= array();

        $rowArr = array();;

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rowArr['courseID'] = $row['courseID'];
            $rowArr['subject'] = $row['subject'];
            $rowArr['courseNum'] = $row['courseNum'];
            $coursesArr[] = $rowArr;
            $rowArr = array();
        }

        $this->closeConn();
        return json_encode($coursesArr);
    }

}
