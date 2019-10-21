<?php

include_once __DIR__.'/../../lib/Database.class.php';
include_once __DIR__.'/../../public_html/model/UserAccount.php';
include_once __DIR__.'/../../public_html/model/Student.php';
include_once __DIR__.'/../../public_html/model/Administrator.php';
include_once __DIR__.'/../../public_html/model/TA.php';
include_once __DIR__.'/../../public_html/model/StatusFile.php';
include_once __DIR__.'/../../public_html/model/Section.class.php';
include_once __DIR__.'/../../public_html/model/SectionManager.class.php';

include_once __DIR__.'/../../public_html/model/matrix.php';

if(!isset($_SESSION)) {
    session_start();
}

if(isset($_POST['functionCall']) && isset($_SESSION['User'])) {
    //  Get the POST functionCall variable that is to be called
    $functionCall = $_POST['functionCall'];

    //  Create a new AdminFunction object
    $ob = new admin_course_list_model($functionCall);

    //  Run the function
    $ob->callFunction();

    //  Return to ajax
    $ob->echoResults();
}  else if(!isset($_SESSION['User'])) {
    header('location: ../index.php');
}

class admin_course_list_model {

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
            case    'listCourses':
                $this->results = $this->listCourses();
                break;
            case    'listAllTAs':
                $this->results = $this->listAllTAs();
                break;
            case    'listMatrixTAs':
                $this->results = $this->listMatrixTAs();
                break;
            case    'removeTA':
                $this->results = $this->removeTA();
                break;
            case    'addTA':
                $this->results = $this->addTA();
                break;
            case 'runScheduler':
                $this->results = $this->runScheduler();
                break;
        }
    }

    private function removeTA() {
        $section = new Section($_POST['sectionID'],null);
        return $section->removeTA();
    }

    private function addTA() {
        $section = new Section($_POST['sectionID'],null);
        //  add in 0 values for hours as placeholder
        return $section->addTA($_POST['taID'],0,0,0,0);
    }

    /** This method returns a JSON object of all TAs regardless of active status
     * @return string
     */
    private function listAllTAs() {
        $taArr = array();

        $this->openConn();
        $sql = "SELECT taID, userName, firstName, lastName FROM TA, Student, UserAccount WHERE TA.sID = Student.sID AND Student.uID = UserAccount.uID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $taArr[] = array($row['taID'],$row['userName'],$row['firstName'],$row['lastName']);
        }

        $taJSON = json_encode($taArr);
        $this->closeConn();

        return $taJSON;
    }

    private function listMatrixTAs() {
        $matrix = new matrix();
        $tas = $matrix->getMatrix();
        return json_encode($tas);
    }

    private function buildSelectHTML($sectionApps) {
        $html = "<option> - Select -</option>";
        foreach($sectionApps as $app) {
            $row = explode(" ", $app);
            $html .= "<option value='$row[3]'>$row[1] $row[2]</option>";
        }
        return $html;
    }

    /** This method returns HTML Course Rows with their sections and TAs
     * @return HTML string
     */
    private function listCourses() {
        //  Variables
        $firstRun = true;
        $hidden = "";
        $results = "";
        $lastCourseNameAndNumber = "";

        //  Open our db conn
        $this->openConn();

        //  SQL every course + section + ta information
        $sql = "SELECT Course.courseID, subject, courseNum, crosslisted, Section.sectionID, secNo, daysMet, startTime, endTime, totalEnrolment, firstName, lastName, SectionTA.taID, secType, isOptimized
                FROM Course, Section
                LEFT JOIN SectionTA ON Section.sectionID = SectionTA.sectionID
                LEFT JOIN TA ON SectionTA.taID = TA.taID
                LEFT JOIN Student ON TA.sID = Student.sID
                LEFT JOIN UserAccount ON Student.uID = UserAccount.uID
                WHERE Section.courseID = Course.courseID AND Section.sessionCode = :session AND sessionYear = :year AND Section.secType = 'LAB' AND term = :term ORDER BY Course.courseID, Section.secNo";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':session'=>$_POST['session'], ':year'=>$_POST['year'], ':term'=>$_POST['term']));


        //  Build a matrix to populate the selects
        $matrix = new matrix();
        $tas = $matrix->getMatrix();
        $conflicts = SectionManager::getSectionConflicts($_POST['session'], $_POST['year']);

        //  Loop through every row
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $courseNameAndNumber = $row['subject'] . " " . $row['courseNum'];   //  "subject courseNum" as key

            //  Variables used to set HTML id's
            $selectID = 'select-' . $row['sectionID'];
            $declineID = "decline-" . $row['sectionID'];
            $addID = "add-" . $row['sectionID'];
            $ta = $row['firstName'] . " " . $row['lastName'];
            $secID = $row['sectionID'];
            $conflictsClass = "c".$secID."-";
            $conflictsClass .= implode("-",$conflicts[$secID]);
            $checked = ($row['isOptimized'] == 0) ? ' checked' : '';
            //  This sets the state of the remove button based on if there is a TA assigned
            $trimmedTA = trim($ta," ");
            if(empty($trimmedTA) || is_null($trimmedTA) || strcmp($trimmedTA, ' ') == 0)
                $hidden = "hidden";
            else
                $hidden = "";

            //  Set the class of assigned TA's for JS parsing
            if(strcmp($hidden, "hidden")==0) {
                $taClass = "";
                $taClassID = "";
            }
            else {
                $taClass = "Assigned-TA";
                $taClassID = $row['sectionID'] . "-" . $row['taID'];
            }

            $sqlCrosslisted = $row['crosslisted'];

            //  Check crosslisted
            if(empty($sqlCrosslisted) || is_null($sqlCrosslisted) || strcmp($sqlCrosslisted, ' ') == 0) {
                $crosslisted = "";
            }
            else {
                $crosslisted = ", ".$sqlCrosslisted;
            }


            $level = " ";
            if($row['courseNum'] > 499 ){
                $level = "graduate";
            }    
            else if($row['courseNum'] > 99 && $row['courseNum'] < 200){
                $level= "year1";
            }
            else if($row['courseNum'] > 199 && $row['courseNum'] < 300){
                $level= "year2";
            }
            else if($row['courseNum'] >299 && $row['courseNum'] < 400){
                $level= "year3";
            }
            else if($row['courseNum'] > 399 && $row['courseNum'] < 500){
                $level= "year4";
            }

            if(isset($tas[$row['sectionID']])) {
                $options = $this->buildSelectHTML($tas[$row['sectionID']]);
            } else {
                $options = "";
            }

            //  Check to see if this new section contains a new course, if so begin a new row and table
            if(strcmp($courseNameAndNumber, $lastCourseNameAndNumber) != 0) {
                //  Append the end of the table to the previous sections
                if(!$firstRun) {
                    $results = $results . '</table>
                                        </div>
                                    </div>
                                </div>';
                }
                //  Begin the HTML
                $results = $results .'
                
                <div class="row course-row '.$row['subject'].' '.$row['courseNum'].' '.$level.'" id=course"'.$row['courseID'].'">
			        <div class="col-4">
				        <h3>'.$row['subject'].' '.$row['courseNum'].' '.$crosslisted.'</h3>
				        <button class="button button-primary more" type="button" onclick="goToSingleTAwithCurrentSelectedSession(\'admin-course-single.php?subject=' . $row['subject'] . '&courseNum=' . $row['courseNum'] . '&courseID=' . $row['courseID'] . '&session=\')">More Detailed Info</button>
			            <br><a href="../admin-set-restrictions.php?course='.$row['courseID'].'">Set Restrictions</a>
			        </div>
                    <div class="col-8">
                    <a href="#" class="button expand">+</a>
				        <div class="  course-lab ">
					        <table class="lab-info-table course-table">
						        <thead>
							        <tr>
								        <th>BLOCK</th>
                                        <th>ID</th>
                                        <th>TIME</th>
                                        <th># STUDENTS</th>
                                        <th>TA INFO</th>
                                        <th>Scheduled TA</th>
                                    </tr>
						        </thead>
						        <tr class="lab-info-row '.$conflictsClass.'">
                                    <td><input type="checkbox" name="blocked['.$row['sectionID'].']" value="1"'.$checked.'></td>
                                    <td class="lab-id">'.$row['secNo'].'</td>
                                    <td class="lab-time">'.$row['daysMet'].' '.$row['startTime'].' - '.$row['endTime'].'</td>
                                    <td class="lab-num">'.$row['totalEnrolment'].'</td>
                                    <td class="ta-info-box">
                                    <div class="">
                                    
                                        <select class="ta-list select-wrapper" id="'.$selectID.'">
                                        '.$options.'
                                        </select>
                                       </div>
                                        <button type="button" class="add-button" id="'.$addID.'">Add</button>
                                    </td>
                                    <td>
                                        <span class="'.$taClass.'" id="'.$taClassID.'">'.$trimmedTA.'</span>
                                        <button type="button" class="remove-button '.$hidden.'" id="'.$declineID.'">Remove</button>
                                    </td>
                                </tr>';
            }

            //  If this section does not contain a new course, append the data to the existing table
            else if(strcmp($courseNameAndNumber, $lastCourseNameAndNumber) == 0) {
                $results = $results .   ' <tr class="lab-info-row '.$conflictsClass.'">
                                            <td><input type="checkbox" name="blocked['.$row['sectionID'].']" value="1"'.$checked.'></td>
                                            <td class="lab-id">'.$row['secNo'].'</td>
                                            <td class="lab-time">'.$row['daysMet'].' '.$row['startTime'].' - '.$row['endTime'].'</td>
                                            <td class="lab-num">'.$row['totalEnrolment'].'</td>
                                            <td class="ta-info-box">
                                            <div class="">

                                                <select class="ta-list select-wrapper" id="'.$selectID.'" name="ta['.$row['sectionID'].']">
                                                '.$options.'
                                                </select>
                                                </div>
                                                <button type="button" class="add-button" id="'.$addID.'">Add</button>
                                            </td>
                                            <td class="" id="">
                                                <span class="'.$taClass.'" id="'.$taClassID.'">'.$trimmedTA.'</span>
                                                <button type="button" class="remove-button '.$hidden.'" id="'.$declineID.'">Remove</button>
                                            </td>
                                        </tr>';

            }

            //  Update
            $firstRun = false;
            $lastCourseNameAndNumber = $courseNameAndNumber;

        }

        //  Append the ending table
        $results = $results . '</table>
                                        </div>
                                    </div>
                                </div>';

        //  Return our HTML
        $this->closeConn();
        return $results;
    }

    private function runScheduler() {
        $sectionData = json_decode($_POST['sectionData'], true);
        $session = $_POST['session'];
        $year = $_POST['year'];
        $term = $_POST['term'];

        $timeLim = is_numeric($_POST['tmLim']) ? intval($_POST['tmLim']) : 45;
        $UTAhrLim = is_numeric($_POST['utahr']) ? intval($_POST['utahr']): 135;
        $GTAhrLim = is_numeric($_POST['gtahr']) ? intval($_POST['gtahr']) : 100;

        $err = false;
        if(!is_array($sectionData)) {
            error('Invalid arguments passed');
            $err = true;
        }

        $Status = new StatusFile();
        if($Status->getStatus() != 100) {
            $err = true;
            return $Status->getStatus();
        }


        if(!$err) {
            // Delete previous files that may exist
            unlink('../../lib/SectionsToSched.csv');
            unlink('../../lib/CoursesToSched.csv');
            unlink('../../lib/Applicants.csv');
            unlink('../../lib/Assigned.csv');
            unlink('../../lib/assignmentResults.csv');
            unlink('../../lib/markingResults.csv');

            // Get all section IDS
            $sections = array_keys($sectionData);

            $database = new Database();
            $conn = $database->getConnection();

            // get student IDs of all hired applicants
            $sql = "SELECT U.userName, TA.taID From Application AS A, Student AS S, UserAccount AS U, TA WHERE A.session = :session AND A.sessionYear = :year AND A.hired = :hired AND S.sID = A.sID AND S.uID = U.uID AND S.sID = TA.sID";
            $stmt = $conn->prepare($sql);
            $stmt->execute(array(':session' => $session, ':year' => $year, ':hired' => 1));
            $sUsers = array();
            $usernameToTAid = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $sUsers[] = $row['userName'];
                $usernameToTAid[$row['userName']] = $row['taID'];
            }

            // Build sections to schedule

            // Get conflicting sections
            $conflicts = SectionManager::getSectionConflicts($session, $year);

            $uniqueCourses = array();
            $F = fopen('../../lib/SectionsToSched.csv', 'w');
            foreach($sections as $sectID) {
                $sql = "SELECT * From Section AS S, Course AS C WHERE C.courseID=S.courseID AND S.sectionID = :sectionID";
                $stmt = $conn->prepare($sql);
                $stmt->execute(array(':sectionID' => $sectID));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $conf = '';
                if(isset($conflicts[$row['sectionID']]) && is_array($conflicts[$row['sectionID']]) && count($conflicts[$row['sectionID']]) > 0)
                    $conf = implode(';', $conflicts[$row['sectionID']]);

                fputcsv($F, array($row['sectionID'], $row['subject'], $row['courseNum'], $row['secNo'], $row['daysMet'], $row['startTime'], $row['endTime'], $row['term'], $row['courseID'], $conf));
                $uniqueCourses[] = $row['courseID'];
            }
            $uniqueCourses = array_values(array_unique($uniqueCourses));
            if(!fclose($F))
                error('Could not save Sections Data file.');

            // Build courses file
            $F = fopen('../../lib/CoursesToSched.csv', 'w');
            $in  = str_repeat('?,', count($uniqueCourses) - 1) . '?';
            $sql = "SELECT courseID,subject,courseNum,labHours,markingHours,prepHours FROM Course AS C WHERE C.courseID IN ($in) ORDER BY C.courseID";
            $stmt = $conn->prepare($sql);
            $stmt->execute($uniqueCourses);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as $row) {
                fputcsv($F, array($row['courseID'], $row['subject'], $row['courseNum'], $row['labHours'], $row['markingHours'], $row['prepHours']));
            }
            if(!fclose($F))
                error('Could not save Courses Data file.');

            // Build applicant file
            $F = fopen('../../lib/Applicants.csv', 'w');
            foreach($sUsers as $UN) {
                $Stu = new Student();
                $Stu->loadStudent($UN);
                $Stu->loadApplications();
                $app = $Stu->getApplication($year, $session);
                if(!$app)
                    continue;
                $Sched = new Schedule($app->getAID());
                $appFormData = (array) json_decode($app->getFormDataJson());
                $prevTA = ($appFormData['ta'] == 'no') ? 0 : 1;
                $fullTime = 1;
                $yr = substr($appFormData['year'], 4, 1);
                $gradStudent = ($appFormData['gta'] == 'no') ? 0 : 1;
                $minHrs = 0;
                $prefHrs = $appFormData['prefHours'];
                $maxHrs = $appFormData['maxHours'];

                $availAndTeachVec = array();
                $canTAcourse = array();
                foreach($sections as $sectID) {
                    $Sect = new Section($sectID);
                    // available?

                    // Save result on TAing course, use if saved
                    $canTA = 0;
                    if(isset($canTAcourse[$Sect->getCourseID()])) {
                        $canTA = $canTAcourse[$Sect->getCourseID()];
                    } else {
                        $C = new Course($Sect->getCourseID(), array('year' => $Sect->getSessionYear(), 'scode' => $Sect->getSessionCode(), 'term' => $Sect->getTerm()));
                        $canTA = $Stu->canTA($C, $app, $Sched);
                        $canTAcourse[$Sect->getCourseID()] = $canTA;
                    }

                    if($Sched->isConflict($Sect) == 1) {
                        $availAndTeachVec[] = 0;
                    }
                    else if($canTA > 0) {
                        $availAndTeachVec[] = 1;
                    } else {
                        $availAndTeachVec[] = 0;
                    }
                }

                // Vector for courses the applicant is qualified to teach
                $courseTeachVec = array();
                foreach($uniqueCourses as $courseID) {
                    $courseTeachVec[] = $canTAcourse[$courseID];
                }

                fputcsv($F, array($usernameToTAid[$Stu->getUserName()], $Stu->getStudentNo(), $prevTA, $fullTime, $yr, $gradStudent, $minHrs, $prefHrs, $maxHrs, implode(';', $availAndTeachVec), implode(';', $courseTeachVec)));
            }
            if(!fclose($F))
                error('Could not save Applicants Data file.');

            // Build TAs already assigned file
            $F = fopen('../../lib/Assigned.csv', 'w');
            foreach($sectionData as $sectID => $val) {
                if(!is_array($val))
                    continue;

                // Check if TA assigned
                if(!empty($val[count($val)-1])) { // TA is assigned
                    fputcsv($F, array($sectID, trim($val[count($val)-1])));
                }
            }
            if(!fclose($F))
                error('Could not save Assigned TA Data file.');

            exec('python3 '.__DIR__.'/../../lib/IP.py '.$timeLim.' '.$UTAhrLim.' '.$GTAhrLim.' > /dev/null &');
            $Status->setStatus(300);
            $conn = null;
            return $Status->getStatus();
        }

    }

}
