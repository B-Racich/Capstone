<?php

include_once __DIR__.'/../../public_html/model/Application.php';
include_once __DIR__.'/../../public_html/model/Schedule.class.php';
include_once __DIR__.'/../../public_html/model/Section.class.php';
include_once __DIR__.'/../../public_html/model/Transcript.class.php';
include_once __DIR__.'/../../public_html/model/UserAccount.php';




class Student extends UserAccount {

    protected $sID;
    protected $graduate;
    protected $isStudent = false;
    protected $applications = array();

    protected $type = "student";

    public function getApplications() {
        return $this->applications;
    }

    public function __construct() {
        parent::__construct();
    }

    public function getSID(){
        return $this->sID;
    }

    public function isStudent() {
        return $this->isStudent;
    }

    public function printInfo() {
        $results = parent::printInfo();
        $results = $results . ',' . $this->sID . ',' . $this->graduate;
        return $results;
    }

    public function createStudent($userName, $password, $firstName, $lastName, $email, $sID, $graduate) {
        $this->openConn();

        //  Check for existing sID
        $sqlCheckSID = "SELECT * FROM Student WHERE sID = :sID";
        $stmtCheckSID =  $this->conn->prepare($sqlCheckSID);
        $stmtCheckSID->execute(array(':sID' => $sID));

        //  If there is no existing student
        if(!$row = $stmtCheckSID->fetch(PDO::FETCH_ASSOC)) {
            $sqlCheckUserName = "SELECT * FROM UserAccount WHERE userName = :userName";
            $stmtCheckUserName = $this->conn->prepare($sqlCheckUserName);
            $stmtCheckUserName->execute(array(':userName' => $userName));

            //  If there is no existing user
            if(!$row = $stmtCheckUserName->fetch(PDO::FETCH_ASSOC)) {
                //  Create User
                $this->closeConn();
                $this->createUserAccount($userName, $password, $firstName, $lastName, $email);
                $this->openConn();
                //  Create Student
                $sql = "INSERT INTO Student VALUES(:sID, :uID, :graduate)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':sID' => $sID, ':uID' => $this->uID, ':graduate' => $graduate));

                if ($stmt->rowCount() > 0) {
                    $this->sID = $sID;
                    $this->graduate = $graduate;
                    $this->isStudent = true;
                    $results = true;
                    $this->closeConn();
                    $_SESSION['User'] = serialize($this);
                    success('Welcome ' . $this->userName);
                } else {
                    $this->closeConn();
                    $results = "Failed to create Student account";
                }
            } else {    // User exists
                $this->closeConn();
                $results = "Username already exists";
            }
        } else {    //  Student exists
            $this->closeConn();
            $results = "Student ID already exists";
        }
        return $results;
    }

    public function loginStudent($userName, $password) {
        /*
         * Perform SQL query to build a new UserAccount object from $uID
         */
        $this->openConn();
        $sql = "SELECT * FROM UserAccount, Student WHERE userName = :userName AND UserAccount.uID = Student.uID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':userName'=> $userName));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //This function verifies the provided password against the stored hash returning true if matched
            if(password_verify($password, $row['passHash'])) {
                $this->userName = $row['userName'];
                $this->uID = $row['uID'];
                $this->firstName = $row['firstName'];
                $this->lastName = $row['lastName'];
                $this->email = $row['email'];
                $this->sID = $row['sID'];
                $this->graduate = $row['graduate'];
                $this->isStudent = true;
                $results = "Login succeeded";
                $this->closeConn();
                $_SESSION['User'] = serialize($this);
                success('Thank you for logging in, '.$this->userName);
            }
            else {
                $this->closeConn();
                $results = "Invalid credentials";
            }
        }
        else {
            $this->closeConn();
            $results = "SQL failed";
        }
        return $results;
    }

    /** This function serves to assist in testings the Class and can be used for Admin purposes to load an Account by name
     * @param $userName
     * @return bool
     */
    public function loadStudent($userName) {
        $this->openConn();
        $sql = "SELECT * FROM UserAccount, Student WHERE userName = :userName AND UserAccount.uID = Student.uID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':userName' => $userName));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->userName = $row['userName'];
            $this->uID = $row['uID'];
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->email = $row['email'];
            $this->sID = $row['sID'];
            $this->graduate = $row['graduate'];
            $this->isStudent = true;
            $results = true;
        }
        else {
            $results = false;
        }
        $this->closeConn();
        return $results;
    }

    public function getStudentNo() {
        return $this->sID;
    }

    public function loadApplications() {
        $this->openConn();
        $sql = "SELECT DISTINCT aID FROM Application, Student WHERE Application.sID = Student.sID AND Student.uID = :uID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':uID'=>$this->uID));

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $temp = new Application();
            $this->applications[] = $temp->loadApplication($row['aID']);
            $this->closeConn();
        }
        $this->closeConn();
    }

    private function deleteApplications() {
        $this->openConn();
        $sql = "DELETE FROM Application WHERE sID = :sID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':sID'=>$this->sID));
        $this->closeConn();
    }

    public function hasApplication($session) {
        $year = explode(" ",$session)[0];
        $sess = explode(" ",$session)[1];
        for($i = 0; $i < sizeof($this->applications); $i++) {
            if($this->applications[$i]->getSession() == $sess && $this->applications[$i]->getSessionYear() == $year) {
                return true;
            }
        }
        return false;
    }

    public function getApplication($year, $session) {
        for($i = 0; $i < sizeof($this->applications); $i++) {
            $appDate = $this->applications[$i]->getSessionYear();
            if($this->applications[$i]->getSession() == $session && $appDate == $year)
                return $this->applications[$i];
        }
        return false;
    }

    /** This function deletes both the Student and UserAccount from DB
     * @return bool
     */
    public function deleteStudent() {
        $this->deleteApplications();
        $this->openConn();
        $sql = "DELETE FROM Student WHERE uID = :uID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':uID'=>$this->uID));

        if($stmt->rowCount() > 0) {
            $this->isStudent = false;
            if($this->deleteUserAccount($this->userName)){
                $results = true;
            }
            else
                $results = false;
        }
        else {
            $results = false;
        }

        $this->closeConn();
        return $results;
    }

    public function canTA(Course $course, Application $app, Schedule $sched = null) {
        $sched = ($sched == null) ? new Schedule($app->getAID()) : $sched;
        $transcript = new Transcript($app->getAID());
        $appFormData = (array) json_decode($app->getFormDataJson());
        // is applicant registered as a student in this course?
        $termSched = $sched->getTermSched($course->getTerm());
        if($termSched === false)
            return 0;
        $termSchedCourses = array_keys($termSched);
        $possibleCourses = array($course->getSubject().$course->getNumber()); // Get course code of this course as well as any others it is crosslisted as
        if($course->getCrosslisted() != '') {
            $possibleCourses = array_merge($possibleCourses, explode(',', $course->getCrosslisted()));
        }
        foreach($possibleCourses as $s)
            if(in_array($s, $termSchedCourses))
                return 0;

        if($appFormData['gta'] == 'yes') { // grad student
            // if course can be taken by grad student, they must have completed it, or its equivalent if crosslisted
            $cYear = substr($course->getNumber(), 1, 1);
            if($cYear > 4) {
                $hasCompleted = false;
                foreach($transcript->getTranscript() as $item) {
                    $keys = array_keys($item);
                    $crs = str_replace(' ', '', $item[$keys[0]]);
                    if(in_array($item, $possibleCourses)) {
                        $hasCompleted = true;
                        break;
                    }
                }
                if(!$hasCompleted)
                    return 0;
            }
        } else { // undergraduate
            // has applicant taken the course?
            if($transcript->hasCompleted($course->getSubject(), $course->getNumber()) < 50) {
                return 0;
            }

            // course grade requirement
            if(!($transcript->hasCompleted($course->getSubject(), $course->getNumber()) >= $course->getUTAminGrade())) {
                return 0;
            }

            // credits requirement
            if($transcript->getTotalCredits() < $course->getMinCredits()) {
                return 0;
            }

            // average in course subject requirement
            if(!($transcript->getAvg($course->getSubject()) >= $course->getMinAvgInSubject())) {
                return 0;
            }

            // Check if student has prereq courses
            if(!empty($course->getPrereq())) {
                $prereqs = explode(',', $course->getPrereq());
                foreach($prereqs as $i => $val) {
                    $val = str_replace(' ', '', $val);
                    $prereqs[$i] = array(substr($course->getNumber(), 0, 4), substr($course->getNumber(), 4, 3));
                    if($transcript->hasCompleted($prereqs[$i][0], $prereqs[$i][1]) < 50) {
                        return 0;
                    }
                }
            }
        }
        return 1;
    }

}
