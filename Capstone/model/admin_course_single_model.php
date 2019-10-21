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
    $ob = new admin_course_single_model($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}

class admin_course_single_model
{

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
            case    'listCourse':
                $this->results = $this->listCourse();
                break;
            case    'numOfTAsInCourse':
                $this->results = $this->numOfTAsInCourse();
                break;
            case    'updateTAsHours':
                $this->results = $this->updateTAsHours();
                break;
            case    'populateCourseHours':
                $this->results = $this->populateCourseHours();
                break;
            case    'populateTotalHours':
                $this->results = $this->populateTotalHours();
                break;
        }
    }

    private function listCourse() {
        $result = '';
        $courseID = $_POST['course'];
        $session = $_POST['session'];

        //Create Session Year variable. E.g. 2019
        $sessionYear = substr($session,0,4);
        //Create Session Season variable will. It should either be "W" or "S"
        $sessionSeason = substr($session, -1);

        //retrieve course data
        $this->openConn();
        $sql = "SELECT SectionTA.sectionID AS section, json_extract(formData, '$.prefHours') AS prefHours, UserAccount.firstName, Student.graduate,  SectionTA.other2Hours, SectionTA.markingHours, SectionTA.otherHours, TA.maxhrs, SectionTA.labHours, UserAccount.email
        FROM Course, SectionTA, Section, TA, UserAccount, Student, Application
        WHERE Course.courseID = :courseID
        AND :courseID = Section.courseID
        AND SectionTA.sectionID = Section.sectionID
        AND SectionTA.taID = TA.taID
        AND TA.sID = Student.sID
        AND Student.uID = UserAccount.uID
        AND TA.sID = Application.sID
        AND Application.session = :sessionSeason
        AND Section.sessionCode = :sessionSeason
        AND Section.sessionYear = :sessionYear";
        $stmt = $this->conn->prepare($sql);//need to grab all sectionID with courseID
        $stmt->execute(array(':courseID'=> $courseID, ':courseID'=> $courseID, ':sessionSeason'=> $sessionSeason, ':sessionSeason'=> $sessionSeason, ':sessionYear'=> $sessionYear));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $result = $result . $row['firstName'] . ',' . $row['section'] . ',' . $row['graduate'] . ',' . trim($row['prefHours'], '"') . ',' . $row['other2Hours'] . ',' . $row['markingHours'] . ',' . $row['otherHours'] . ',' . $row['maxhrs'] . ',' . $row['email'] . ',';
        }

        if($stmt->rowCount() > 0) {
            $this->closeConn();
            return $result;
        }
        else{
            $this->closeConn();
            return false;
        }
    }

    private function numOfTAsInCourse() {//This is a pretty messy method, just retreiving how many times it finds a TA in the database for the given course
        $numOfTAs = 0;
        $result = '';
        $session = $_POST['session'];
        $session = substr($session, -1);

        $courseID = $_POST['course'];

        $this->openConn();
        $sql = "SELECT *
                FROM Course, SectionTA, Section, TA, UserAccount, Student
                WHERE Course.courseID = :courseID
                AND :courseID = Section.courseID
                AND SectionTA.sectionID = Section.sectionID
                AND SectionTA.taID = TA.taID
                AND TA.sID = Student.sID
                AND Student.uID = UserAccount.uID
                AND Section.sessionCode = :session";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':courseID'=> $courseID, ':courseID'=> $courseID, ':session'=> $session));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $numOfTAs++;//Here is when strings are being created when TA's are found, we just increase the variable numOfTAs here as we found one
        }

        $this->closeConn();

        $_SESSION['numOfTAs'] = $numOfTAs; //Setting the session variable so we can use the number for building the table in course-single.php

        if($numOfTAs > 0)
            return $numOfTAs;
        else
            return false;
    }

    private function updateTAsHours() {
        $prevTAHours = $_POST['prevTAHours'];
        $alteredTAHours = $_POST['alteredTAHours'];

        //First we clear out the empty strings inside our two arrays
        $prevTAHours = array_filter($prevTAHours, 'strlen');//The 'strlen' argument lets us keep the 0's in the array which we need
        $alteredTAHours = array_filter($alteredTAHours, 'strlen');

        //Reordering the indexes so our arrays are easy to work with (0,1,2,3,4,5) instead of 2,3,4,11,12,13)
        $prevTAHours = array_merge($prevTAHours);
        $alteredTAHours = array_merge($alteredTAHours);

        //Determine which rows have been changed and update them if they have
        $this->openConn();

        for($k = 0; $k < sizeof($alteredTAHours); $k = $k + 5){
            if($prevTAHours[$k] != $alteredTAHours[$k] || $prevTAHours[$k+1] != $alteredTAHours[$k+1] || $prevTAHours[$k+2] != $alteredTAHours[$k+2]){
                //We've found a row that has been altered lets update it
                $sql = "UPDATE SectionTA, Student, TA, UserAccount SET labHours = :labHours, markingHours = :markingHours, otherHours = :otherHours, other2Hours = :other2Hours
                WHERE UserAccount.email = :email
                AND UserAccount.uID = Student.uID
                AND Student.sID = TA.sID
                AND TA.taID = SectionTA.taID
                AND SectionTA.sectionID = :sectionID";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':labHours'=> ($alteredTAHours[$k]+$alteredTAHours[$k+1]+$alteredTAHours[$k+2]), ':markingHours'=> $alteredTAHours[$k+1], ':otherHours'=> $alteredTAHours[$k+2], ':other2Hours'=> ($alteredTAHours[$k] > 2 ? 2 : $alteredTAHours[$k]), ':email'=> $alteredTAHours[$k+3], ':sectionID'=> (int)$alteredTAHours[$k+4]));
            }
        }

        if($stmt->rowCount() > 0) {
            $this->closeConn();
            return true;
        }
        else{
            $this->closeConn();
            return false;
        }
    }

    private function populateCourseHours() {
        $courseInfo = $_POST['courseInfo'];
        $session = $_POST['session'];
        $courseID = $_POST['courseID'];
        $results = "";
        $sumHours = 0;

        //Create Session Year variable. E.g. 2019
        $sessionYear = substr($session,0,4);
        //Create Session Season variable will. It should either be "W" or "S"
        $sessionSeason = substr($session, -1);

        $this->openConn();

        for($k = 0; $k < sizeof($courseInfo)-1; $k = $k + 9){
            //We've found a row that has been altered lets update it
            $sql = "SELECT SectionTA.labHours AS totalHours
            FROM SectionTA, TA, Student, UserAccount, Section
            WHERE UserAccount.email = :email
            AND UserAccount.uID = Student.uID
            AND Student.sID = TA.sID
            AND TA.taID = SectionTA.taID
            AND SectionTA.sectionID = Section.sectionID
            AND Section.sessionYear = :sessionYear
            AND Section.sessionCode = :sessionSeason
            AND Section.courseID = :courseID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':email'=> $courseInfo[$k+8], ':sessionYear'=> $sessionYear, ':sessionSeason'=> $sessionSeason, 'courseID'=> $courseID));

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $sumHours += (int)$row['totalHours'];// Adding all the hours a TA has for sections in this specific course
            }

            $results .= $sumHours . ",";
            $sumHours = 0; //Reseting sumHours for the next row
        }

        if($stmt->rowCount() > 0) {
            $this->closeConn();
            return $results;
        }
        else{
            $this->closeConn();
            return false;
        }
    }

    private function populateTotalHours() {
        $courseInfo = $_POST['courseInfo'];
        $session = $_POST['session'];
        $results = "";
        $sumHours = 0;

        //Create Session Year variable. E.g. 2019
        $sessionYear = substr($session,0,4);
        //Create Session Season variable will. It should either be "W" or "S"
        $sessionSeason = substr($session, -1);

        $this->openConn();

        for($k = 0; $k < sizeof($courseInfo)-1; $k = $k + 9){
            //We've found a row that has been altered lets update it
            $sql = "SELECT SectionTA.labHours AS totalHours
            FROM SectionTA, TA, Student, UserAccount, Section
            WHERE UserAccount.email = :email
            AND UserAccount.uID = Student.uID
            AND Student.sID = TA.sID
            AND TA.taID = SectionTA.taID
            AND SectionTA.sectionID = Section.sectionID
            AND Section.sessionYear = :sessionYear
            AND Section.sessionCode = :sessionSeason";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':email'=> $courseInfo[$k+8], ':sessionYear'=> $sessionYear, ':sessionSeason'=> $sessionSeason));

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $sumHours += (int)$row['totalHours'];// Adding all the hours a TA has for every sections they currently have
            }

            $results .= $sumHours . ",";
            $sumHours = 0; //Reseting sumHours for the next row
        }

        if($stmt->rowCount() > 0) {
            $this->closeConn();
            return $results;
        }
        else{
            $this->closeConn();
            return false;
        }
    }
}
