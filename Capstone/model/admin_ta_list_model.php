<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('../../lib/PHPMailer/src/Exception.php');
require('../../lib/PHPMailer/src/PHPMailer.php');
require('../../lib/PHPMailer/src/SMTP.php');

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
    $ob = new admin_ta_list_model($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}

class admin_ta_list_model {

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
            case    'listUTAs':
                $this->results = $this->listUTAs();
                break;
            case    'listGTAs':
                $this->results = $this->listGTAs();
                break;
            case    'updateHoursandComment':
                $this->results = $this->updateHoursandComment();
                break;
            case    'emailSelectedTAs':
                $this->results = $this->emailSelectedTAs();
                break;
        }
    }

    private function listUTAs() {
        $count = 0;

        $this->openConn();

        $sql = "SELECT DISTINCT SectionTA.taID, userName, graduate, T.sID, email, json_extract(formData, '$.year') AS year,
        json_extract(formData, '$.program') AS program, json_extract(formData, '$.prefHours') AS prefHours, minhrs, maxhrs, hired, comments
        FROM UserAccount U, Student S, Application A, TA T, SectionTA, Section, Course
        WHERE T.sID = S.sID
        AND T.taID = SectionTA.taID
        AND SectionTA.sectionID = Section.sectionID
        AND Section.courseID = Course.courseID
        AND S.uID = U.uID
        AND T.sID = A.sID
        AND A.session = :session";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':session'=> $_POST['sessionSelection']));

        $this->results = "";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $taID = $row['taID'];
            $userName = $row['userName'];
            $sID = $row['sID'];
            $email = $row['email'];
            $year = $row['year'];
            $program = $row['program'];
            $prefhours = $row['prefHours'];
            $minhours = $row['minhrs'];
            $maxhours = $row['maxhrs'];
            $comments = $row['comments'];

            // $this->results = "1";

            if($row['hired']==1 && $row['graduate']==0) {


              //get the courses and hours
              $sql2 = "SELECT subject, courseNum, (STA.labHours + STA.markingHours + STA.otherHours) as tothours FROM Section S, SectionTA STA, Course C WHERE C.courseID = S.courseID and S.sectionID = STA.sectionID and taID = :taID";
              $stmt2 = $this->conn->prepare($sql2);
              $stmt2->execute(array(':taID'=> $taID));

              $totalassignedhrs = 0;
              $assignedhours = 0;
              $coursesArray = "";

              while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $subject = $row2['subject'];
                $courseNum = $row2['courseNum'];

                $tothours = $row2['tothours'];

                $coursesArray = $coursesArray."".$subject ." ". $courseNum. " -- $tothours" . " hours<br>";

                $totalassignedhrs = $totalassignedhrs + $tothours;
              }



                //display the rows
                $this->results = $this->results .
                "<tr name='row-$sID' class='ta-info-row' id ='utarow'>
                <td>
                <input type='checkbox' id='TAcheckbox$count'>
                </td>
                <td name='$sID' id ='utaname'>
                  <b>Profile: <a href='ta-single.php?sID=$sID'>$userName - $sID</a> </b>
                  <br>
                  Email: <a href='mailto:$email' id='TAemail$count'>$email</a>
                </td>
                <td>" . substr($year, -2, 1) . "</td>
                <td>" . trim($program,'"'). "</td>
                <td>". $coursesArray ."</td>
                <td>". trim($prefhours, '"') ."</td>
                <td><textarea rows='1' cols='1' id='utamin'>". $minhours."</textarea></td>
                <td><textarea rows='1' cols='1' id='utamax'>". trim($maxhours, '"')."</textarea></td>
                <td>$totalassignedhrs</td>
                <td>
                    <div class='textarea-container' >
                        <textarea id='utacomment'>$comments</textarea>
                    </div>
                </td>
            </tr>";

            $count++;
            unset($coursesArray);
          }
            // $this->results = "2";
        }
        $this->closeConn();
        return $this->results;
    }

    private function listGTAs() {
        $count = 0;

        $this->openConn();

        $sql = "SELECT DISTINCT SectionTA.taID, userName, graduate, T.sID, email, json_extract(formData, '$.year') AS year,
        json_extract(formData, '$.program') AS program,
        json_extract(formData, '$.prefHours') AS prefHours,
        minhrs, maxhrs, hired, comments
        FROM UserAccount U, Student S, Application A, TA T, SectionTA, Section, Course
        WHERE T.sID = S.sID
        AND T.taID = SectionTA.taID
        AND SectionTA.sectionID = Section.sectionID
        AND Section.courseID = Course.courseID
        AND S.uID = U.uID
        AND T.sID = A.sID
        AND A.session = :session";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':session'=> $_POST['sessionSelection']));

        $this->results = "";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $taID = $row['taID'];
            $userName = $row['userName'];
            $sID = $row['sID'];
            $email = $row['email'];
            $year = $row['year'];
            $program = $row['program'];
            $prefhours = $row['prefHours'];
            $minhours = $row['minhrs'];
            $maxhours = $row['maxhrs'];
            $comments = $row['comments'];

            // echo($userName.$sID.$email.$year.$program.$prefhours.$minhours.$maxhours.$taID);

            // $this->results = "1";


            if($row['hired']==1 && $row['graduate']==1) {


              //get the courses and hours
              $sql2 = "SELECT subject, courseNum, (STA.labHours + STA.markingHours + STA.otherHours) as tothours FROM Section S, SectionTA STA, Course C WHERE C.courseID = S.courseID and S.sectionID = STA.sectionID and taID = :taID";
              $stmt2 = $this->conn->prepare($sql2);
              $stmt2->execute(array(':taID'=> $taID));

              $totalassignedhrs = 0;
              $assignedhours = 0;
              $coursesArray = "";

              while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $subject = $row2['subject'];
                $courseNum = $row2['courseNum'];

                $tothours = $row2['tothours'];

                $coursesArray = $coursesArray."".$subject ." ". $courseNum. " -- $tothours" . " hours<br>";

                $totalassignedhrs = $totalassignedhrs + $tothours;
              }



                //display the rows
                $this->results = $this->results .
                "<tr name='row-$sID' class='ta-info-row' id ='gtarow'>
                <td>
                <input type='checkbox' id='GTAcheckbox$count'>
                </td>
                <td name='$sID' id ='gtaname'>
                  <b>Profile: <a href='ta-single.php?sID=$sID'>$userName - $sID</a> </b>
                  <br>
                  Email: <a href='mailto:$email' id='GTAemail$count'>$email</a>
                </td>
                <td>" . substr($year, -2, 1) . "</td>
                <td>" . trim($program,'"'). "</td>
                <td>". $coursesArray ."</td>
                <td>". trim($prefhours, '"') ."</td>
                <td><textarea rows='1' cols='1' id='gtamin'>". $minhours."</textarea></td>
                <td><textarea rows='1' cols='1' id='gtamax'>". trim($maxhours, '"')."</textarea></td>
                <td>$totalassignedhrs</td>
                <td>
    				        <div class='textarea-container' id='gtacomment'>
    				            <textarea>$comments</textarea>
    				        </div>
                </td>
            </tr>";

            $count++;
            unset($coursesArray);

            }
            // $this->results = "2";
        }
        $this->closeConn();
        return $this->results;
    }

    private function updateHoursandComment() {
        $id = $_POST['id'];
        $minHours = $_POST['minHours'];
        $maxHours = $_POST['maxHours'];
        $comment = $_POST['comment'];

        $this->openConn();


        $sql = "UPDATE TA SET minhrs = :minHours, maxhrs = :maxHours, comments = :comments WHERE sID = :sID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':minHours'=> $minHours, ':maxHours'=> $maxHours, ':comments'=> $comment, ':sID'=> $id));

        if($stmt->rowCount() > 0) { 
            $this->closeConn();
            return true;
        } else {
            $this->closeConn();
            return false;
        }

    }

    private function emailSelectedTAs() {
        require('../../lib/fpdf/fpdf.php');

        $emailTAList = $_POST['emailTAList'];
        // echo("DEBUG:EmailList=" . $emailTAList[0].$emailTAList[1].$emailTAList[2].$emailTAList[3].$emailTAList[4]);

        $this->openConn();

        for ($i=0; $i < sizeof($emailTAList); $i++) {

            $sql = "SELECT SectionTA.labHours, SectionTA.markingHours, SectionTA.otherHours, SectionTA.other2Hours, Course.subject, Course.courseNum, Section.secNo, UserAccount.firstName
            FROM SectionTA, TA, Student, UserAccount, Section, Course
            WHERE SectionTA.taID = TA.taID
            AND TA.sID = Student.sID
            AND Student.uID = UserAccount.uID
            AND UserAccount.email = :email
            AND SectionTA.sectionID = Section.sectionID
            AND Section.sessionCode = :session
            AND Section.courseID = Course.courseID";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':email'=> $emailTAList[$i],':session'=> $_POST['sessionSelection']));

            //Create a new PDF for this student
            $pdf = new FPDF('p','mm','A4');
            $pdf ->AddPage();
            $pdf ->SetFont('Arial', '',12);

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $TAname = $row['firstName'];
                //Cell(width,height,text,border,end line, [align])
                $pdf ->Cell(130,5 ,"Your offer for " . $row['subject'] . "-" . $row['courseNum'] . "-" . $row['secNo'],1,1);
                $pdf ->Cell(110,5 ,"Prep Hours: " . $row['other2Hours'] ,1,1);
                $pdf ->Cell(110,5 ,"Marking Hours: " . $row['markingHours'],1,1);
                $pdf ->Cell(110,5 ,"Other Hours: " . $row['otherHours'],1,1);
                $pdf ->Cell(110,10 ,"Total Hours: " . $row['labHours'],1,1);
            }

            $pdf ->Output("F","../../Output/PDF.pdf");

            //Generating the Email
            $email = new PHPMailer();
            $email->SetFrom('postmaster.ta.scheduler@gmail.com', 'UBC TA Scheduler'); //Name is optional
            $email->Subject   = 'Your TA Offer';
            $email->Body      = "Hello " . $TAname . ", here is your TA offer!";
            $email->AddAddress($emailTAList[$i]);

            $file_to_attach = '../../Output/PDF.pdf';

            $email->AddAttachment( $file_to_attach , $TAname . '\'s Offer.pdf' );
            $email->Send();
        }

        $this->closeConn();
        return true;
    }
}
