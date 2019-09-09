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
    $ob = new admin_view_applications_model($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}

class admin_view_applications_model {

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
            case    'listApplicants':
                $this->results = $this->listApplicants();
                break;
            case    'acceptStudent':
                $this->results = $this->acceptStudent();
                break;
            case    'declineStudent':
                $this->results = $this->declineStudent();
                break;
            case    'isAssigned':
                $this->results = $this->isAssigned();
                break;
            case    'removeAndDecline':
                $this->results = $this->declineAndRemoveAssignment();
                break;
        }
    }

    /** This method returns HTML markup of all applicants WHERE session = $_POST['sessionSelection']
     * @return HTML string
     */
    private function listApplicants() {
        //  SQL
        $this->openConn();
        $sql = "SELECT userName, aID, UserAccount.uID, email, transcript, courseSchedule, json_extract(formData, '$.year') AS year, date, hired, Student.sID FROM UserAccount, Student, Application WHERE UserAccount.uID = Student.uID AND Student.sID = Application.sID AND Application.session = :session";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':session'=> $_POST['sessionSelection']));

        $this->results = "";

        //  Loop and build applicants
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userName = $row['userName'];
            $uID = $row['uID'];
            $email = $row['email'];
            $year = $row['year'];
            $aID = $row['aID'];
            $hired = $row['hired'];
            $sID = $row['sID'];

            //  Used to set the color code of the application status
            $status = 'undecided';

            switch ($hired) {
                case -1:
                    $status = 'declined';
                    break;
                case 0:
                    $status = 'undecided';
                    break;
                case 1:
                    $status = 'hired';
                    break;
                default:
                    $status = 'undecided';
            }

            $this->results = $this->results . "<tr name='row-$uID' class='ta-info-row ".$status."'>
            <td><a href='ta-single.php?sID=$sID'>$userName - $uID</a></td>
            <td>" . substr($year, -2, 1) . "</td> <!--TODO MAKE REAL-->
            <td>COSC</td>
            <td><a href='mailto:$email'>$email</a></td>
            <td class='transcript-td align-center'>

                <a class='button button-default' href='admin-view-transcript.php?sID=$sID'>VIEW</a>
            </td>
            <td class='schedule-td align-center'>
                <a class='button button-default' href='admin-view-schedule.php?sID=$sID'>VIEW</a>

            </td>
            <td class='status-td'>
                <div class='row align-center'>";
            //  Accepted
            if($row['hired']==1) {
                $this->results = $this->results . "<div class='col-6'>
                        <a id='$aID-1' name='acceptBtn-$uID-$aID' class='button button-disabled'>Accept</a>
                    </div>
                    <div class='col-6'>
                        <a id='$aID-2' name='declineBtn-$uID-$aID' class='button button-default'>Decline</a>
                    </div>
                </div>
            </td>
            </tr>";
            }
            //  Declined
            else if($row['hired']==-1) {
                $this->results = $this->results . "<div class='col-6'>
                        <a id='$aID-1' name='acceptBtn-$uID-$aID' class='button button-default'>Accept</a>
                    </div>
                    <div class='col-6'>
                        <a id='$aID-2' name='declineBtn-$uID-$aID' class='button button-disabled'>Decline</a>
                    </div>
                </div>
            </td>
            </tr>";
            }
            else {
                //  Undecided
                $this->results = $this->results . "<div class='col-6'>
                        <a id='$aID-1' name='acceptBtn-$uID-$aID' class='button button-default'>Accept</a>
                    </div>
                    <div class='col-6'>
                        <a id='$aID-2' name='declineBtn-$uID-$aID' class='button button-default'>Decline</a>
                    </div>
                </div>
            </td>
            </tr>";
            }
        }
        $this->closeConn();
        return $this->results;
    }

    private function acceptStudent() {
        $id = $_POST['id'];
        $this->openConn();
        $sql = "SELECT sID FROM Student WHERE uID = :uID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':uID'=> $id));

        //  Find student
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sID = $row['sID'];
            $minhrs = 0;
            $maxhrs = 1;
            $comments = '';
            $sql = "INSERT INTO `TA` (sID, minhrs, maxhrs, comments) VALUES (:sID, :minhrs, :maxhrs, :comments)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':sID'=> $sID, ':minhrs'=>$minhrs, ':maxhrs'=>$maxhrs, ':comments'=>$comments));

            //  Inserted into TA
            if($stmt->rowCount() > 0) {
                $sql = "UPDATE Application SET hired = 1 WHERE aID = :aID";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':aID'=> $_POST['aID']));

                //  Application set
                if($stmt->rowCount() > 0) {
                    $this->closeConn();
                    return 'Student Accepted';
                }
                else {
                    return 'SQL error on setting application status';
                }
            }
            else {
                return 'SQL error on inserting TA';
            }
        }
        else {
            $this->closeConn();
            return 'SQL error finding student';
        }
    }

    private function declineStudent() {
        $id = $_POST['id'];
        $this->openConn();

        //  Check if in TA
        $sql = "SELECT * FROM TA, UserAccount, Student WHERE UserAccount.uID = :uID AND UserAccount.uID = Student.uID AND Student.sID = TA.sID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':uID'=> $id));

        if($stmt->rowCount() > 0) {
            $sql = "DELETE FROM TA WHERE sID IN (SELECT sID  FROM UserAccount, Student WHERE UserAccount.uID = :uID AND UserAccount.uID = Student.uID)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':uID' => $id));

            if ($stmt->rowCount() <= 0) {
                return 'SQL failed deleting TA';
            }
        }
        //  Update application status
        $sql = "UPDATE Application SET hired = -1 WHERE aID = :aID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':aID'=> $_POST['aID']));

        //  Application set
        if($stmt->rowCount() > 0) {
            $this->closeConn();
            return 'Student declined';
        }
        else {
            return 'SQL error declining application';
        }

    }

    private function isAssigned() {
        $this->openConn();

        $sql = "SELECT * FROM SectionTA, TA, Student WHERE SectionTA.taID = TA.taID AND TA.sID = Student.sID AND Student.uID = :uID";
        $stmt =  $this->conn->prepare($sql);
        $stmt->execute(array(":uID"=>$_POST['uID']));

        if($stmt->rowCount() > 0) {
            $this->closeConn();
            return true;
        }
        else {
            $this->closeConn();
            return false;
        }
    }

    private function declineAndRemoveAssignment() {
        $this->openConn();

        $id = $_POST['id'];
        $taID = "";

        $sqlGetTAID = "SELECT taID FROM TA, Student WHERE TA.sID = Student.sID AND Student.uID = :uID";
        $sqlDeleteFromSectionTA = "DELETE FROM SectionTA WHERE taID = :taID";

        $stmt = $this->conn->prepare($sqlGetTAID);
        $stmt->execute(array(":uID"=>$id));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $taID = $row['taID'];
            $stmt = $this->conn->prepare($sqlDeleteFromSectionTA);
            $stmt->execute(array(":taID"=>$taID));

            if($stmt->rowCount() > 0) {
                $this->closeConn();
                return $this->declineStudent();
            }
        }
        $this->closeConn();
        return false;

    }

}
