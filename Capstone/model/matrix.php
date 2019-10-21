<?php

include_once __DIR__.'/../../lib/Database.class.php';
include_once __DIR__.'/../../public_html/model/TA_ics_reader.php';
include_once __DIR__.'/../../public_html/model/Student.php';
include_once( __DIR__.'/../../public_html/model/Course.class.php');
include_once( __DIR__.'/../../public_html/model/Section.class.php');
include_once __DIR__.'/../../public_html/model/Schedule.class.php';
include_once( __DIR__.'/../../public_html/model/Professor.class.php');
include_once __DIR__.'/../../public_html/model/Application.php';

if(!isset($_SESSION)) {
    session_start();
}

class matrix {

    // Applicant Constraints
    private $applicantList =array();
    private $sectionList = array();
    private $decisionMatrix = array();

    private $courseCache = array();
    private $applicantsCache = array();
    private $canTAcache = array();

    private $taInfoList = array();

    private $appScheduleArr = array();
    private $sectionArr = array();
    private $courseScheduleArr = array();
    private $taInfoArr = array();

    public function getSectionArr() {
        return $this->sectionArr;
    }

    public function __construct() {
        $this->loadApplicants();
        $this->loadSections();
        $this->loadMatrix();
    }

    protected function openConn() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    protected function closeConn() {
        $this->conn = null;
        $database = null;
    }

    public function getPossibleTAs($sectionID){
        $section = $this->decisionMatrix[$sectionID];
        $talist = array();

        foreach($this->consider as $key => $value){
            if ($this->consider[$key] > 0 )
                $talist[] = $key;
        }
    }

    public function loadMatrix(){
        $canTA = null;
        $size = sizeof($this->sectionArr);
        $sizeApps = sizeof($this->applicantList);
        $conflicts = 0;
        for($secIndex = 0; $secIndex < $size; $secIndex++)  {   //  For each section we have
            // Create cache array if does not exist
            if(!isset($this->canTAcache[$this->sectionArr[$secIndex]->getCourseID()]))
                $this->canTAcache[$this->sectionArr[$secIndex]->getCourseID()] = array();
            foreach($this->applicantList as $appKey => $appValue) { //  For each applicant we have
                $appSchedule = $this->appScheduleArr[$appKey];  //  Get the stored Schedule object
                $S = new Student();
                $C = null;
                $A = null;
                // Cache Course
                if(isset($this->courseCache[$this->sectionArr[$secIndex]->getCourseID()])) {
                    $C = $this->courseCache[$this->sectionArr[$secIndex]->getCourseID()];
                } else {
                    $C = new Course($this->sectionArr[$secIndex]->getCourseID(), array('year' => $this->sectionArr[$secIndex]->getSessionYear(), 'scode' => $this->sectionArr[$secIndex]->getSessionCode(), 'term' => $this->sectionArr[$secIndex]->getTerm()));
                    $this->courseCache[$this->sectionArr[$secIndex]->getCourseID()] = $C;
                }
                // Cache applicantion object
                if(isset($this->applicantsCache[$appKey])) {
                    $A = $this->applicantsCache[$appKey];
                } else {
                    $A = new Application();
                    $A->loadApplication($appKey);
                    $this->applicantsCache[$appKey] = $A;
                }
                // Cache canTA (course qualified) result
                $courseQualified = 0;
                if(isset($this->canTAcache[$this->sectionArr[$secIndex]->getCourseID()][$appKey])) {
                    $courseQualified = $this->canTAcache[$this->sectionArr[$secIndex]->getCourseID()][$appKey];
                } else {
                    $courseQualified = $S->canTA($C, $A, $appSchedule);
                    $this->canTAcache[$this->sectionArr[$secIndex]->getCourseID()][$appKey] = $courseQualified;
                }
                $canTA = 0;
                $canTA = ($appSchedule->isConflict($this->sectionArr[$secIndex]) == 1 || $courseQualified <= 0) ? -1 : 1;   //  check if there is a conflict for the given applicant to the given section
                if($canTA == 1) {
                    $section = $this->sectionArr[$secIndex]->getSectionID();
                    $this->decisionMatrix[$section][$appKey] = $this->taInfoArr[$appKey];   //  Add ta to the matrix
                }else {
                    $conflicts++;
                }
            }
        }
        // echo "<pre>";
        // print_r($this->decisionMatrix);
    }

    public function loadApplicants() {
        $this->openConn();

        $sql = "SELECT aID, courseSchedule, userName, firstName, lastName, taID, json_extract(formData,'$.days') as days From Application, UserAccount, Student, TA WHERE Application.session = :session AND Application.sessionYear = :year AND UserAccount.uID = Student.uID AND Application.sID = Student.sID AND TA.sID = Student.sID";
        $stmt = $this->conn->prepare($sql);
        $session = $_POST['session'];
        $year = $_POST['year'];
        $stmt->execute(array(':session'=>$session, ':year'=>$year));

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->addApplicationId($row['aID']);
                $this->setTAInfo($row['aID'], $row['userName'],$row['firstName'],$row['lastName'],$row['taID']);
                $this->courseScheduleArr[$row['aID']] = $row['courseSchedule'];
                $this->appScheduleArr[$row['aID']] = new Schedule($row['courseSchedule']); //  Create a new schedule from applicants info
                if(!empty($row['days'])) {
                    $days = $row['days'];
                    $this->appScheduleArr[$row['aID']]->setRestrictedDays($days);
                }
        }
            $this->closeConn();
    }

    private function loadSections() {
        $this->openConn();
        $sql = "SELECT * FROM Section WHERE sessionCode = :session AND sessionYear = :year AND term = :term";
        $session = $_POST['session'];
        $year = $_POST['year'];
        $term = $_POST['term'];
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':session'=>$session, ':year'=>$year, ':term'=>$term));

        $sectionHeaders = array_flip(array('Sec No', 'Days Met', 'Start Time', 'End time', 'Building', 'Room', 'Term', 'Total Enrolment', 'Total Released Seats', 'Meeting Start Date', 'Meeting End Date', 'Act Type', 'Session Year', 'Session Code'));
        $sectionData = array(); //  Array to hold section params
        $sec = null;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sectionData = array();
            $sectionData[] = $row['secNo'];
            $sectionData[] = $row['daysMet'];
            $sectionData[] = $row['startTime'];
            $sectionData[] = $row['endTime'];
            $sectionData[] = $row['building'];
            $sectionData[] = $row['room'];
            $sectionData[] = $row['term'];
            $sectionData[] = $row['totalEnrolment'];
            $sectionData[] = $row['totalReleasedSeats'];
            $sectionData[] = $row['secStartDate'];
            $sectionData[] = $row['secEndDate'];
            $sectionData[] = $row['secType'];
            $sectionData[] = $row['sessionYear'];
            $sectionData[] = $row['sessionCode'];
            $sec = new Section($sectionData, $sectionHeaders);  //  create a new section with data
            $sec->setSectionId($row['sectionID']);
            $sec->setCourseID($row['courseID']);
            $this->sectionArr[] = $sec; //  Add to array of sections
            $this->addSectionId($row['sectionID']);
        }
        $this->closeConn();
    }

    public function getApplicantList(){
        return $this->applicantList;
    }

    public function addApplicationId($aid) {
        $this->applicantList[$aid] = 0;
    }

    public function addSectionId( $id) {
        $this->sectionList[$id] = 0;
    }

    public function getSectionList() {
        return  $this->sectionList;
    }

    public function getMatrix(){
       return $this->decisionMatrix;
    }

    private function setTAInfo($aid, $userName, $firstName, $lastName, $taID) {
        $this->taInfoArr[$aid] = "$userName $firstName $lastName $taID";
    }
}
