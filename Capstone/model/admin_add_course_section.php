<?php

include_once __DIR__.'/../../lib/Database.class.php';
include_once __DIR__.'/../../public_html/model/UserAccount.php';
include_once __DIR__.'/../../public_html/model/Student.php';
include_once __DIR__.'/../../public_html/model/Administrator.php';
include_once __DIR__.'/../../public_html/model/TA.php';
include_once __DIR__.'/../../public_html/model/Section.class.php';
error_reporting(E_ALL); ini_set('display_errors', 1);

if(!isset($_SESSION)) {
    session_start();
}

if(isset($_POST['functionCall']) && isset($_SESSION['User'])) {
    //  Get the POST functionCall variable that is to be called
    $functionCall = $_POST['functionCall'];

    //  Create a new AdminFunction object
    $ob = new admin_add_course_section($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}  else if(!isset($_SESSION['User'])) {
    header('location: ../index.php');
}

class admin_add_course_section {

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
            case    'getCoursesJson':
                $this->results = $this->getCoursesJson();
                break;
            case    'getCourseSection':
                $this->results = $this->getCourseSection();
                break;
            case    'deleteSection':
                $this->results = $this->deleteSection();
                break;
            case    'addSection':
                $this->results = $this->addSection();
            break;
        }
    }

    /** Retrieves courseID, subject, courseNum from all courses and returns as a JSON
     * @return JSON object of courses
     */
     private function getCoursesJson() {
           $this->openConn();
           $sql = "SELECT courseID, subject, courseNum FROM Course";
           $stmt = $this->conn->prepare($sql);
           $stmt->execute();

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


    /**
     * Displays the lab sections of the selected course
     */
    private function getCourseSection() {
        $this->openConn();

        $courseID = $_POST['courseID'];

        $sql = "SELECT Course.courseID, subject, courseNum,
        Section.sectionID, secNo, daysMet, startTime, endTime, totalEnrolment
        FROM Course, Section
        LEFT JOIN SectionTA ON Section.sectionID = SectionTA.sectionID
        LEFT JOIN TA ON SectionTA.taID = TA.taID
        LEFT JOIN Student ON TA.sID = Student.sID
        LEFT JOIN UserAccount ON Student.uID = UserAccount.uID
        WHERE Section.courseID = Course.courseID
        and Course.courseID = :courseID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':courseID'=>$courseID));

        $this->results = "";
        //  Loop through every row
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $subject = $row['subject'];
          $courseNum = $row['courseNum'];
          $sectionID = $row['sectionID'];
          $secNo = $row['secNo'];
          $daysMet = $row['daysMet'];
          $startTime = $row['startTime'];
          $endTime = $row['endTime'];
          $totalEnrolment = $row['totalEnrolment'];


          //display rows
          $this->results = $this->results .'
          <tr class="lab-info-row" name='.$sectionID.'>
            <td><input id="sectioncheckbox" type="checkbox"></td>
            <td id="lab-id" >'.$secNo.'</td>
            <td id="lab-time">'.$daysMet.' | '.$startTime.' - '.$endTime.'</td>
            <td id="lab-num">'.$totalEnrolment.'</td>
          </tr>';
        }

        $this->closeConn();
        return $this->results;

    }


    /**
     * Deletes lab section from selected course
     */
    private function deleteSection() {
      $courseID = $_POST['courseID'];
      $sectionID = $_POST['sectionID'];

      $this->openConn();

      $sql = "DELETE FROM Section WHERE courseID= :courseID and sectionID = :sectionID";
      $stmt = $this->conn->prepare($sql);
      $stmt->execute(array(':courseID'=> $courseID, ':sectionID'=> $sectionID));

      if($stmt === TRUE) {
          $this->closeConn();
          return true;
      }
      else {
          $this->closeConn();
          return false;
      }

    }

    /**
     * Deletes ENTIRE course
     */
    private function deleteCourse() {
      $courseID = $_POST['courseID'];

      $this->openConn();

      $sql = "DELETE FROM Course WHERE courseID= :courseID";
      $stmt = $this->conn->prepare($sql);
      $stmt->execute(array(':courseID'=> $courseID));

      if($stmt->rowCount() > 0) {
          $this->closeConn();
          return true;
      }
      else {
          $this->closeConn();
          return false;
      }

    }


    /**
     * ADD lab section to selected course
     */
    private function addSection() {
      // courseid, secNo, daysMet, startTime, endTime, totalReleasedSeats, building, room

      $courseID = $_POST['courseID'];
      $secNo = $_POST['secNo'];
      $daysMet = $_POST['daysMet'];
      $startTime = $_POST['startTime'];
      $endTime = $_POST['endTime'];
      $totalReleasedSeats = $_POST['totalReleasedSeats'];
      $building = $_POST['building'];
      $room = $_POST['room'];

      $this->openConn();

      $sql = "SELECT DISTINCT term, secStartDate, secEndDate, sessionYear, sessionCode FROM Section WHERE courseID = :courseID";
      $stmt = $this->conn->prepare($sql);
      $stmt->execute(array(':courseID'=> $courseID));

      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $term = $row['term'];
        $secStartDate = $row['secStartDate'];
        $secEndDate = $row['secEndDate'];
        $sessionYear = $row['sessionYear'];
        $sessionCode = $row['sessionCode'];
      }

      $totalEnrolment = 0;
      $isOptimized = 0;
      $secType = "LAB";

      // if($row){
      //
      // }

      $sql2 = "INSERT INTO Section (courseID, secNo, daysMet, startTime, endTime, totalReleasedSeats, totalEnrolment, building, room,
        term, isOptimized, secStartDate, secEndDate, sessionYear, sessionCode, secType)
        VALUES (:courseID, :secNo, :daysMet, :startTime, :endTime, :totalReleasedSeats, :totalEnrolment, :building, :room,
        :term, :isOptimized, :secStartDate, :secEndDate, :sessionYear, :sessionCode, :secType)";
      $stmt2 = $this->conn->prepare($sql2);
      $stmt2->execute(array(':courseID'=> $courseID, ':secNo'=> $secNo, ':daysMet'=> $daysMet, ':startTime'=> $startTime, ':endTime'=> $endTime, ':totalReleasedSeats'=> $totalReleasedSeats, ':totalEnrolment'=> $totalEnrolment, ':building'=> $building, ':room'=> $room,
      ':term'=> $term, ':isOptimized'=> $isOptimized, ':secStartDate'=> $secStartDate, ':secEndDate'=> $secEndDate, ':sessionYear'=> $sessionYear, ':sessionCode'=> $sessionCode, ':secType'=> $secType));

      $this->closeConn();

    }

    /**
     * Add NEW course
     */
    private function addCourse() {
      //course data (7 variables)
      $subject = $_POST['subject'];
      $courseNum = $_POST['courseNum'];
      $longTitle = $_POST['longTitle'];
      $labHours = $_POST['labHours'];
      $markingHours = $_POST['markingHours'];
      $prepHours = $_POST['prepHours'];
      $otherHours = $_POST['otherHours'];


      // lecture data (7 variables)
      $secNo = $_POST['secNo'];
      $daysMet = $_POST['daysMet'];
      $startTime = $_POST['startTime'];
      $endTime = $_POST['endTime'];
      $totalReleasedSeats = $_POST['totalReleasedSeats'];
      $building = $_POST['building'];
      $room = $_POST['room'];
      // (5 variables)
      $term = $_POST['term'];
      $secStartDate = $_POST['secStartDate'];
      $secEndDate = $_POST['secEndDate'];
      $sessionYear = $_POST['sessionYear'];
      $sessionCode = $_POST['sessionCode'];

      $this->openConn();

      //insert course into table
      $sql = "INSERT INTO Course ('subject', courseNum, longTitle, labHours, markingHours, prepHours, otherHours) VALUES (:subject, :courseNum, :longTitle, :labHours, :markingHours, :prepHours, :otherHours)";
      $stmt = $this->conn->prepare($sql);
      $stmt->execute(array(':subject'=> $subject, ':courseNum'=> $courseNum, ':longTitle'=> $longTitle, ':labHours'=> $labHours, 'markingHours' => $markingHours, 'prepHours'=> $prepHours, 'otherHours' => $otherHours));

      if ($stmt->fetch(PDO::FETCH_ASSOC)) { //$conn->query($sql) == TRUE

        $totalEnrolment = 0;
        $isOptimized = 0;
        $secType = "LEC";

        //find the course id
        $sql3 = "SELECT courseID FROM Course WHERE subject = :subject and courseNum = :courseNum";
        $stmt3 = $this->conn->prepare($sql3);
        $stmt3->execute(array(':subject'=> $subject, ':courseNum'=> $courseNum));

        while($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
          $courseID = $row['courseID'];
        }

        // insert into lecture into section
        $sql2 = "INSERT INTO Section (courseID, secNo, daysMet, startTime, endTime, totalReleasedSeats, totalEnrolment, building, room,
          term, isOptimized, secStartDate, secEndDate, sessionYear, sessionCode, secType)
          VALUES (:courseID, :secNo, :daysMet, :startTime, :endTime, :totalReleasedSeats, :totalEnrolment, :building, :room,
          :term, :isOptimized, :secStartDate, :secEndDate, :sessionYear, :sessionCode, :secType)";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute(array(':courseID'=> $courseID, ':secNo'=> $secNo, ':daysMet'=> $daysMet, ':startTime'=> $startTime, ':endTime'=> $endTime, ':totalReleasedSeats'=> $totalReleasedSeats, ':totalEnrolment'=> $totalEnrolment, ':building'=> $building, ':room'=> $room,
        ':term'=> $term, ':isOptimized'=> $isOptimized, ':secStartDate'=> $secStartDate, ':secEndDate'=> $secEndDate, ':sessionYear'=> $sessionYear, ':sessionCode'=> $sessionCode, ':secType'=> $secType));


        $this->closeConn();
        return true;

      } else {

        $this->closeConn();
        return false;
      }

    }


  }
