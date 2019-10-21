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
    $ob = new ta_single_model($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}

class ta_single_model {

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
      		case 	'grabStudentName':
                $this->results = $this->grabStudentName();
                break;
      		case 	'grabStudentPrevCourseHist':
      			$this->results = $this->grabStudentPrevCourseHist();
                break;
      		case 	'grabStudentHours':
      			$this->results = $this->grabStudentHours();
                break;
      		case 	'grabStudentCurrentTAassignments':
      			$this->results = $this->grabStudentCurrentTAassignments();
                break;
      		case 	'grabStudentPreviousTAassignments':
      			$this->results = $this->grabStudentPreviousTAassignments();
                break;
        }
    }

    private function grabStudentName(){
		$results = "";
		$studentID = $_POST['studentID'];

		$this->openConn();

        $sql = "SELECT userName, firstName, lastName, email, graduate FROM UserAccount, Student WHERE sID = :studentID AND UserAccount.uID = Student.uID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':studentID'=> $studentID));

		//Appending name, first name, last name, email, graduate
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results .= $row['userName'] . ",";
            $results .= $row['firstName'] . ",";
            $results .= $row['lastName'] . ",";
            $results .= $row['email'] . ",";
            $results .= $row['graduate'] . ",";
		}

		if($stmt->rowCount() > 0) {
			$this->closeConn();
            return $results;
        } else {
			$this->closeConn();
            return false;
        }
	}

	private function grabStudentPrevCourseHist() {
		$results = "";
		$studentID = $_POST['studentID'];
		$numOfPreviousCourseHistoryAndGrades = 0;

		$this->openConn();

		$sql = "SELECT json_extract(transcript, '$') AS transcript FROM Application WHERE Application.sID = :studentID";
		$stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':studentID'=> $studentID));

		//Appending previous course history and grades
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$transcript = $row['transcript'];
			// echo("debug" . $row['transcript']);

		}
		//Decode the previous course history and grades
		$json = json_decode($transcript);
		for($j = 1; $j < sizeof($json)-1; $j++) {
				$results .= ($json[$j][0] . ",");//Course
				$results .= ($json[$j][4] . ",");//Session
				$results .= ($json[$j][7] . ",");//Year
				$results .= ($json[$j][2] . ",");//Grade
				$results .= ($json[$j][3] . ",");//Letter
				$numOfPreviousCourseHistoryAndGrades++;
		}

		$results .= $numOfPreviousCourseHistoryAndGrades;//Last item, get the amount of rows we will need for our previous course history and grades

		if($stmt->rowCount() > 0) {
            $this->closeConn();
            return $results;
        } else {
			$this->closeConn();
            return false;
        }
    }

	private function grabStudentHours() {
		$results = "";
		$studentID = $_POST['studentID'];
        $year = $_POST['year'];
        $session = $_POST['session'];
		$sumOfLabHours = 0;

		$this->openConn();

		$sql = "SELECT labHours, markingHours, otherHours, other2Hours, minhrs, json_extract(formData, '$.prefHours') AS prefHours, maxhrs, (labHours + markingHours + otherHours + other2Hours) as totalHours
        FROM SectionTA, TA, Application
        WHERE SectionTA.taID = TA.taID
        AND TA.sID = :studentID
        AND :studentID = Application.sID
        AND Application.session = :session
        AND Application.sessionYear = :year";

		$stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':studentID'=> $studentID, ':studentID'=> $studentID, ':session'=> $session, ':year'=> $year));

		//Appending hours
		$weAppendedPrefandMaxHours = false;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			if($weAppendedPrefandMaxHours){//We only want to add the prefHours and maxHours once so we just sum the labHours the secondtime, thirdtime, and so on, it depends on how many courses the TA is teaching
				$sumOfLabHours += (int)$row['labHours'];
			}
			else{
				//The first time the loop is run we enter here and
				$sumOfLabHours += (int)$row['labHours'];
				$results .= trim($row['prefHours'], '"') . ",";//prefhours
				$results .= $row['minhrs'] . ",";//min hours
				$results .= trim($row['maxhrs'], '"') . ",";//maxHours
			}
			$weAppendedPrefandMaxHours = true;
		}

		$results .= $sumOfLabHours;

		if($stmt->rowCount() > 0) {
            $this->closeConn();
            return $results;
        } else {
			$this->closeConn();
            return false;
        }
    }

	private function grabStudentCurrentTAassignments() {
		$results = "";
		$studentID = $_POST['studentID'];
		$numOfCurrentlyAssignedCourses = 0;

		$this->openConn();

		$sql3 = "SELECT subject, courseNum, Section.secNo, Section.secEndDate FROM Course, SectionTA, Section, TA WHERE TA.sID = :studentID AND TA.taID = SectionTA.taID AND SectionTA.sectionID = Section.sectionID AND Section.courseID = Course.courseID";
		$stmt3 = $this->conn->prepare($sql3);
        $stmt3->execute(array(':studentID'=> $studentID));

		//Appending the currently assigned courses
		while($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
			$currentDate = date("Y-m-d");
			$sectionEndDate = $row['secEndDate'];
			//echo("DEBUG:currentdate=" . $currentDate . "sectionEndDate=" . $sectionEndDate);
			if($currentDate < $sectionEndDate){//If todays date is before the section End date, then its a current course
				$results .= $row['subject'] . ",";
				$results .= $row['courseNum'] . ",";
				$results .= $row['secNo'] . ",";
				$numOfCurrentlyAssignedCourses++;
			}
		}

		$results .= $numOfCurrentlyAssignedCourses;//This gets added last and will be used to run a loop x amount of times to print the currently assigned courses

		if($stmt3->rowCount() > 0) {
            $this->closeConn();
            return $results;
        } else {
			$this->closeConn();
            return false;
        }
    }

	private function grabStudentPreviousTAassignments() {
        $results = "";
        $studentID = $_POST['studentID'];
        $numOfPreviousAssignedCourses = 0;

        $this->openConn();

        $sql4 = "SELECT subject, courseNum, Section.secNo, Section.secEndDate
        FROM Course, SectionTA, Section, TA
        WHERE TA.sID = :studentID
        AND TA.taID = SectionTA.taID
        AND SectionTA.sectionID = Section.sectionID
        AND Section.courseID = Course.courseID";
        $stmt4 = $this->conn->prepare($sql4);
        $stmt4->execute(array(':studentID'=> $studentID));

		//Appending the previously assigned courses
		while($row = $stmt4->fetch(PDO::FETCH_ASSOC)) {
			$currentDate = date("Y-m-d");
			$sectionEndDate = $row['secEndDate'];
			//echo("DEBUG:PREVcurrentdate=" . $currentDate . "PREVsectionEndDate=" . $sectionEndDate);
			if($currentDate >= $sectionEndDate){//IF Current date is greater or equal to the Section End date, then its a previous course
				$results .= $row['subject'] . ",";
				$results .= $row['courseNum'] . ",";
				$results .= $row['secNo'] . ",";
				$numOfPreviousAssignedCourses++;
			}
		}

		$results .= $numOfPreviousAssignedCourses;//This gets added last and will be used to run a loop x amount of times to print the previous assigned courses

		if($stmt4->rowCount() > 0) {
            $this->closeConn();
            return $results;
        } else {
			$this->closeConn();
            return false;
        }
    }
}
