<?php

include_once __DIR__.'/../../lib/Database.class.php';
include_once __DIR__.'/../../public_html/model/UserAccount.php';
include_once __DIR__.'/../../public_html/model/Student.php';
include_once __DIR__.'/../../public_html/model/Administrator.php';
include_once __DIR__.'/../../public_html/model/TA.php';
include_once __DIR__.'/../../public_html/model/Section.class.php';
include_once __DIR__.'/../../public_html/model/matrix.php';
include_once __DIR__.'/../../public_html/model/TA_ics_reader.php';

if(!isset($_SESSION)) {
    session_start();
}

if(isset($_POST['functionCall'])) {
    //  Get the POST functionCall variable that is to be called
    $functionCall = $_POST['functionCall'];

    //  Create a new AdminFunction object
    $ob = new admin_view_schedule($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}

class admin_view_schedule {

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
      		case 	'getSchedule':
      			$this->results = $this->getSchedule();
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

		//Statement 1: Appending name, first name, last name, email, graduate
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

	private function getSchedule() {
		$results = "";
        $studentID = $_POST['studentID'];
        
        $reader = new TA_ics_reader();
		$this->openConn();

        $sql = "SELECT json_extract(courseSchedule, '$') AS schedule FROM Application WHERE Application.sID = :studentID";
    
		$stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':studentID'=> $studentID));

		//Appending previous course history and grades
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$schedule = $row['schedule'];
			// echo("debug" . $row['transcript']);

		}
		//Decode the previous course history and grades
		$json = json_decode($schedule);
		// for($j = 1; $j < sizeof($json)-1; $j++) {
		// 		$results .= ($json[$j][0] . ",");//Course
		// 		$results .= ($json[$j][4] . ",");//Session
		// 		$results .= ($json[$j][7] . ",");//Year
		// 		$results .= ($json[$j][2] . ",");//Grade
		// 		$results .= ($json[$j][3] . ",");//Letter
		// 		$numOfPreviousCourseHistoryAndGrades++;
		// }

		// $results .= $numOfPreviousCourseHistoryAndGrades;//Last item, get the amount of rows we will need for our previous course history and grades
            // $results = $studentID;    
        $events  = $reader->getArrayFromJSON($schedule);
        // $results = "";
        // echo "<pre>";
            // print_r($results);
        $results = '<table class="format-table"><thead><th> Start Date </th><th> End Date </th><th> Dept </th><th>Code </th><th>Term</th><th>Day</th></thead>';
            foreach($events as $section){
                $results .="<tr>";
                foreach($section as $key => $value){
                    
                    $results .="<td>".$value."</td>";
                 
                }
                $results .="</tr>";
            }
            
            // echo $results;

            // $results = $reader->printEvents($results);
            // $results = $schedule;
            
		if($stmt->rowCount() > 0) {
            $this->closeConn();
            return $results;
        } else {
            $this->closeConn();

            return false;
        }
    }



}
