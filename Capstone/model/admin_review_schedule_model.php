<?php

include_once __DIR__.'/../../lib/Database.class.php';
include_once __DIR__.'/../../public_html/model/UserAccount.php';
include_once __DIR__.'/../../public_html/model/Student.php';
include_once __DIR__.'/../../public_html/model/Administrator.php';
include_once __DIR__.'/../../public_html/model/TA.php';
include_once __DIR__.'/../../public_html/model/StatusFile.php';
include_once __DIR__.'/../../public_html/model/Section.class.php';
error_reporting(E_ALL); ini_set('display_errors', 1);

if(!isset($_SESSION)) {
    session_start();
}

if(isset($_POST['functionCall']) && isset($_SESSION['User'])) {
    //  Get the POST functionCall variable that is to be called
    $functionCall = $_POST['functionCall'];

    //  Create a new AdminFunction object
    $ob = new admin_review_schedule_model($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}  else if(!isset($_SESSION['User'])) {
    header('location: ../index.php');
}

class admin_review_schedule_model
{

    private $functionCall;  //  function called
    private $results;   //  output from function

    public function __construct($functionCall)
    {
        $this->functionCall = $functionCall;
    }

    /**
     * Database connections for SQL queries
     */
    protected function openConn()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    protected function closeConn()
    {
        $this->conn = null;
        $database = null;
    }

    public function getFunctionCall()
    {
        return $this->functionCall;
    }

    public function echoResults()
    {
        echo $this->results;
    }

    public function callFunction()
    {
        switch ($this->functionCall) {
            case    'checkStatus':
                $this->results = $this->checkStatus();
                break;
            case    'getTableData':
                $this->results = $this->getTableData();
                break;
            case    'assignTAs':
                $this->results = $this->assignTAs();
                break;
        }
    }

    /** TODO
     *  NOTE: Im not sure on the best way to save persistant data since this class is recreated on every call, $_SESSION
     *  should work to store the data from the files if we want to do that initially on checkStatus validating.
     */

    private function checkStatus() {
        $SF = new StatusFile();
        /** TODO
         *  Could try to read in the data and save it into session here to be used later not sure tho
         */
        $status_before = $SF->getStatus();

        if(strcmp($status_before, "200") == 0) {
             // Our files are ready for processing
            $SF->setStatus(100);
        }
        else if(strcmp($status_before, "301") == 0) {
            $SF->setStatus(100);
        }
        else if(strcmp($status_before, "302") == 0) {
            $SF->setStatus(100);
        }

        $status_after = $SF->getStatus();
        return "$status_before, $status_after"; //  Return our status code to ajax
    }

    private function getTableData() {
        /** TODO
         *  Here is where we would loop through our data and format a string of table rows to return
         */

        $assignmentPath = __DIR__.'/../../lib/assignmentResults.csv';
        $markingPath = __DIR__.'/../../lib/markingResults.csv';

        if(file_exists($assignmentPath) && file_exists($markingPath)) {
            $assignmentFile = fopen($assignmentPath, 'r') or die("Unable to open file!");
            $markingFile = fopen($markingPath, 'r') or die("Unable to open file!");

            $html = array();
            $html_assign = array();
            $html_mark = array();
            $sqlInfo = array();

            $assignSectionIDs = array();
            $assignTaIDs = array();

            $this->openConn();

            $lineNum = 0;
            //  Get our IDS from our CSV
            while(!feof($assignmentFile)) {
                $line = fgetcsv($assignmentFile);
                $assignSectionIDs[$lineNum] = $line[0];
                $assignTaIDs[$lineNum] = $line[1];
                $lineNum++;
            }

            $markSectionIDs = array();
            $markTaIDs = array();

            $lineNum = 0;
            //  Get our IDS from our CSV
            while(!feof($markingFile)) {
                $line = fgetcsv($markingFile);
                $markSectionIDs[$lineNum] = $line[0];
                $markTaIDs[$lineNum] = $line[1];
                $lineNum++;
            }

            for($i = 0; $i < sizeof($assignSectionIDs); $i++) {
                $sql = "SELECT UserName, TA.taID, longTitle, courseNum, Course.courseID, Section.sectionID, secType, secNo FROM UserAccount,Student,TA, Course, Section WHERE UserAccount.uID = Student.uID AND Student.sID = TA.sID AND Course.courseID = Section.courseID AND secType = 'LAB' AND Section.sectionID = :sectionID AND TA.taID = :taID";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':sectionID' => $assignSectionIDs[$i], ':taID'=>$assignTaIDs[$i]));
                if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $sqlInfo[$assignSectionIDs[$i]][$assignTaIDs[$i]] = "$assignSectionIDs[$i], $assignTaIDs[$i]";
                    $html_assign[$assignSectionIDs[$i]][$assignTaIDs[$i]] = "<tr><td>" . $row['longTitle'] . " " . $row['secNo'] .  "</td><td>" . $row['UserName'] . "</td>";
                }
            }
            for($i = 0; $i < sizeof($markSectionIDs); $i++) {
                $sql = "SELECT UserName, TA.taID, longTitle, courseNum, Course.courseID, Section.sectionID, secType, secNo FROM UserAccount,Student,TA, Course, Section WHERE UserAccount.uID = Student.uID AND Student.sID = TA.sID AND Course.courseID = Section.courseID AND secType = 'LAB' AND Section.sectionID = :sectionID AND TA.taID = :taID";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':sectionID' => $markSectionIDs[$i], ':taID'=>$markTaIDs[$i]));
                if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $sqlInfo[$markSectionIDs[$i]][$markTaIDs[$i]] = "$markSectionIDs[$i], $markTaIDs[$i]";
                    $html_mark[$markSectionIDs[$i]][$markTaIDs[$i]] = "<tr><td>" . $row['longTitle'] . " " . $row['secNo'] .  "</td><td>" . $row['UserName'] . "</td>";
                }

            }

            $keys_assign = array_keys($html_assign);
            $keys_mark = array_keys($html_mark);

            $count = max(sizeof($keys_assign),sizeof($keys_mark));

            for($i = 0; $i < $count; $i++) {
                $sql = "SELECT labHours, markingHours, prepHours, otherHours FROM Course, Section WHERE Course.courseID = Section.courseID AND Section.sectionID = :sectionID";
                //  Same TA teaching and marking
                if(isset($html_assign[$assignSectionIDs[$i]][$assignTaIDs[$i]]) && isset($html_mark[$markSectionIDs[$i]][$markTaIDs[$i]])) {
                    if (strcmp($assignSectionIDs[$i],$markSectionIDs[$i]) == 0 && strcmp($assignTaIDs[$i],$markTaIDs[$i]) == 0) {
                        $stmt = $this->conn->prepare($sql);
                        $stmt->execute(array(':sectionID'=>$assignSectionIDs[$i]));
                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $sqlInfo[$assignSectionIDs[$i]][$assignTaIDs[$i]] .= ", ".$row['labHours'].", ".$row['markingHours'].", ".$row['prepHours'].", ".$row['otherHours'];
                            $html[$assignSectionIDs[$i]][$assignTaIDs[$i]] = $html_assign[$assignSectionIDs[$i]][$assignTaIDs[$i]];
                            $html[$assignSectionIDs[$i]][$assignTaIDs[$i]] .= "<td>" . $row['labHours'] . "</td><td>" . $row['markingHours'] . "</td>
                            <td>" . $row['prepHours'] . "</td><td>" . $row['otherHours'] . "</td></tr>";
                        }
                    } else {
                        $stmt = $this->conn->prepare($sql);
                        $stmt->execute(array(':sectionID'=>$assignSectionIDs[$i]));
                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $sqlInfo[$assignSectionIDs[$i]][$assignTaIDs[$i]] .= ", ".$row['labHours'].", ". 0 .", ".$row['prepHours'].", ".$row['otherHours'];
                            $html[$assignSectionIDs[$i]][$assignTaIDs[$i]] = $html_assign[$assignSectionIDs[$i]][$assignTaIDs[$i]];
                            $html[$assignSectionIDs[$i]][$assignTaIDs[$i]] .= "<td>" . $row['labHours'] . "</td><td>0</td>
                            <td>" . $row['prepHours'] . "</td><td>" . $row['otherHours'] . "</td></tr>";
                        }
                        $stmt = $this->conn->prepare($sql);
                        $stmt->execute(array(':sectionID'=>$markSectionIDs[$i]));
                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $sqlInfo[$markSectionIDs[$i]][$markTaIDs[$i]] .= ", ". 0 .", ".$row['markingHours'].", ".$row['prepHours'].", ".$row['otherHours'];
                            $html[$markSectionIDs[$i]][$markTaIDs[$i]] = $html_mark[$markSectionIDs[$i]][$markTaIDs[$i]];
                            $html[$markSectionIDs[$i]][$markTaIDs[$i]] .= "<td>0</td><td>" . $row['markingHours'] . "</td>
                            <td>" . $row['prepHours'] . "</td><td>" . $row['otherHours'] . "</td></tr>";
                        }
                    }
                } else if(isset($html_assign[$assignSectionIDs[$i]][$assignTaIDs[$i]])) {
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute(array(':sectionID'=>$assignSectionIDs[$i]));
                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $sqlInfo[$assignSectionIDs[$i]][$assignTaIDs[$i]] .= ", ".$row['labHours'].", ". 0 .", ".$row['prepHours'].", ".$row['otherHours'];
                        $html[$assignSectionIDs[$i]][$assignTaIDs[$i]] = $html_assign[$assignSectionIDs[$i]][$assignTaIDs[$i]];
                        $html[$assignSectionIDs[$i]][$assignTaIDs[$i]] .= "<td>" . $row['labHours'] . "</td><td>0</td>
                            <td>" . $row['prepHours'] . "</td><td>" . $row['otherHours'] . "</td></tr>";
                    }
                } else if(isset($html_mark[$markSectionIDs[$i]][$markTaIDs[$i]])) {
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute(array(':sectionID'=>$markSectionIDs[$i]));
                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $sqlInfo[$markSectionIDs[$i]][$markTaIDs[$i]] .= ", ". 0 .", ".$row['markingHours'].", ".$row['prepHours'].", ".$row['otherHours'];
                        $html[$markSectionIDs[$i]][$markTaIDs[$i]] = $html_mark[$markSectionIDs[$i]][$markTaIDs[$i]];
                        $html[$markSectionIDs[$i]][$markTaIDs[$i]] .= "<td>0</td><td>" . $row['markingHours'] . "</td>
                            <td>" . $row['prepHours'] . "</td><td>" . $row['otherHours'] . "</td></tr>";
                    }
                }
            }

            fclose($assignmentFile);
            fclose($markingFile);
            $this->closeConn();
            $html_string = "";
            foreach($html as $row) {
                foreach($row as $item) {
                    $html_string .= $item;
                }
            }

            $_SESSION['sqlInfo'] = $sqlInfo;
            $_SESSION['assignSectionIDs'] = $assignSectionIDs;
            $_SESSION['assignTaIDs'] = $assignTaIDs;
            $_SESSION['markSectionIDs'] = $markSectionIDs;
            $_SESSION['markTaIDs'] = $markTaIDs;

            return $html_string;
        }
        return "";
    }

    private function assignTAs() {
        /** TODO
         *  Here is where the SQL and logic would go to process the file data into the database
         */
        if(isset($_SESSION['assignSectionIDs']) && isset($_SESSION['assignTaIDs']) && isset($_SESSION['markSectionIDs'])
            && isset($_SESSION['markTaIDs']) && isset($_SESSION['sqlInfo'])) {
            $this->openConn();
            $errorCount = 0;

            $sqlInfo = $_SESSION['sqlInfo'];
            foreach($sqlInfo as $sectionID => $taIDs) {
                foreach($taIDs as $taID => $sqlData) {
                    $sqlDataArr = explode(", ", $sqlData);
                    $sql = "INSERT INTO SectionTA VALUES ";
                    $sql .= "($sqlDataArr[0], $sqlDataArr[1], $sqlDataArr[2], $sqlDataArr[3], $sqlDataArr[4], $sqlDataArr[5])";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute();
                    if($stmt->rowCount() == 0) {
                        $errorCount++;
                    }
                }
            }

            $this->closeConn();
            if($errorCount != 0 )
                return $errorCount;
            return 0;
        }
        return -1;
    }

}