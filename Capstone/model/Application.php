<?php

include_once __DIR__.'/../../lib/Database.class.php';
include_once __DIR__.'/../../public_html/model/TA_ics_reader.php';
include_once __DIR__.'/../../public_html/model/Student.php';

if(!isset($_SESSION)) {
    session_start();
}

class Application {

    private $year;
    private $gender;
    private $program;
    private $address;
    private $city;
    private $postal;
    private $gta;
    private $ta;
    private $prefHours;
    private $maxHours;
    private $session;
    private $sessionYear;
    private $sID;
    private $aID;
    private $days;

    private $date;
    private $hired;

    private $formDataJson;
    private $transcriptJson;
    private $scheduleJson;

    public function getAID() {
        return $this->aID;
    }

    public function getSession() {
        return $this->session;
    }

    public function getSID() {
        return $this->sID;
    }

    public function getDate() {
        return $this->date;
    }

    public function getHired() {
        return $this->hired;
    }

    public function getFormDataJson() {
        return $this->formDataJson;
    }

    public function getTranscriptJson() {
        return $this->transcriptJson;
    }

    public function getScheduleJson() {
        return $this->scheduleJson;
    }

    public function getSessionYear() {
        return $this->sessionYear;
    }

    public function __construct() {
        if(isset($_POST['submitApp'])) {    // Check to avoid auto-building
            $_SESSION['appStatus'] = 'building';
            $this->buildApplication();
            $this->submitApplication();
        }
    }

    public function buildApplication() {
        $this->getFormData();
        $this->buildFormDataJson();
        $this->buildTranscriptJson();
        $this->buildScheduleJson();
    }

    protected function openConn() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    protected function closeConn() {
        $this->conn = null;
        $database = null;
    }

    public function loadApplication($aID) {
        $this->openConn();
        $sql = "SELECT aID, session, sessionYear, sID, transcript, courseSchedule, formData, date, hired,
                json_extract(formData, '$.year') AS year,
                json_extract(formData, '$.gender') AS gender, 
                json_extract(formData, '$.program') AS program,
                json_extract(formData, '$.address') AS address,
                json_extract(formData, '$.city') AS city,
                json_extract(formData, '$.postal') AS postal,
                json_extract(formData, '$.gta') AS gta,
                json_extract(formData, '$.ta') AS ta,
                json_extract(formData, '$.prefHours') AS prefHours,
                json_extract(formData, '$.maxHours') AS maxHours
                FROM Application WHERE Application.aID = :aID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':aID'=>$aID));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->year = $row['year'];
            $this->gender = $row['gender'];
            $this->program = $row['program'];
            $this->address = $row['address'];
            $this->city = $row['city'];
            $this->postal = $row['postal'];
            $this->gta = $row['gta'];
            $this->ta = $row['ta'];
            $this->prefHours = $row['prefHours'];
            $this->maxHours = $row['maxHours'];
            $this->session = $row['session'];
            $this->sessionYear = $row['sessionYear'];

            $this->sID = $row['sID'];
            $this->aID = $aID;
            $this->date = $row['date'];
            $this->hired = $row['hired'];
            $this->scheduleJson = $row['courseSchedule'];
            $this->transcriptJson = $row['transcript'];
            $this->formDataJson = $row['formData'];
            $this->closeConn();

            return $this;
        }
        else {
            $this->closeConn();
        }
    }

    private function getFormData() {
        $this->year = $_POST['year'];
        $this->gender = $_POST['gender'];
        $this->program = $_POST['program'];
        $this->address = $_POST['address'];
        $this->city = $_POST['city'];
        $this->postal = $_POST['postal'];
        $this->gta = $_POST['gta'];
        $this->ta = $_POST['ta'];
        $this->prefHours = $_POST['prefHours'];
        $this->maxHours = $_POST['maxHours'];
        $this->days = json_decode($_POST['days']);
        $sessionSelect = $_POST['session'];
        $this->sessionYear = explode(" ", $sessionSelect)[0];
        $this->session = explode(" ", $sessionSelect)[1];
    }

    private function buildFormDataJson() {
        $form = array("year"=>$this->year,"gender"=>$this->gender,"program"=>$this->program,"address"=>$this->address,
            "city"=>$this->city,"postal"=>$this->postal,"gta"=>$this->gta,"ta"=>$this->ta, "session"=>$this->session,
            "prefHours"=>$this->prefHours,"maxHours"=>$this->maxHours, "days"=>$this->days);
        $this->formDataJson = json_encode($form);
    }

    private function buildScheduleJson() {
        $file = $_FILES["schedule"]["tmp_name"];
        $reader = new TA_ics_reader();
        $events = $reader->getIcsEventsAsArray($file);
        $this->scheduleJson = $reader->getJSONEvents($events);
    }

    private function buildTranscriptJson() {
        $file = $_FILES["transcript"]["tmp_name"];
        $csv = file_get_contents($file);
        $array =  array_map("str_getcsv", explode("\n", $csv));
        $this->transcriptJson = json_encode($array);
    }

    public function toString() {
        return $this->formDataJson . ", " . $this->transcriptJson . ", " . $this->scheduleJson;
    }

    public function submitApplication() {
        $this->openConn();
        $sqlSetStudentGraduateStatus = "UPDATE Student SET graduate = :graduate WHERE sID = :sID";
        //   Check for an exisitng application
        $sqlFindApp = "SELECT aID FROM Application WHERE sID = :sID AND session = :session AND sessionYear = :sessionYear";
        $stmtFindApp = $this->conn->prepare($sqlFindApp);
        $stmtFindApp->execute(array(':sID' => unserialize($_SESSION['User'])->getSID(), ':session' => $this->session, ':sessionYear' => $this->sessionYear));

        //  Found existing application
        if ($row = $stmtFindApp->fetch(PDO::FETCH_ASSOC)) {
            $sqlUpdate = "UPDATE Application SET session = :session, sessionYear = :sessionYear, transcript = :transcript, courseSchedule = :courseSchedule, formData = :formData, date = :date, hired = :hired WHERE aID = :aID";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->execute(array(':session' => $this->session, ':sessionYear'=> $this->sessionYear, ':transcript' => $this->transcriptJson, ':courseSchedule' => $this->scheduleJson, 'formData' => $this->formDataJson, ':date' => date('Y-m-d H:i:s'), ':hired' => 0, ':aID' => $row['aID']));

            if ($stmtUpdate->rowCount() > 0) {
                if($this->gta == 'yes'){$gtaSql = 1;}else{$gtaSql = 0;}
                $stmtSetStudentGraduateStatus = $this->conn->prepare($sqlSetStudentGraduateStatus);
                $stmtSetStudentGraduateStatus->execute(array(':graduate'=>$gtaSql, ':sID'=>unserialize($_SESSION['User'])->getSID()));
                $_SESSION['appStatus'] = 'Application submitted';
            } else {
                $_SESSION['appStatus'] = 'Failed to update application';
            }
            $this->closeConn();

        } else {
            $sqlInsert = "INSERT INTO Application(sID, session, sessionYear, transcript, courseSchedule, formData, date, hired)
            VALUES(:sID, :session, :sessionYear, :transcript, :courseSchedule, :formData, :date, :hired)";
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $stmtInsert = $this->conn->prepare($sqlInsert);
            $stmtInsert->execute(array(':sID' => unserialize($_SESSION['User'])->getSID(), ':session' => $this->session, ':sessionYear'=>$this->sessionYear, ':transcript' => $this->transcriptJson, ':courseSchedule' => $this->scheduleJson, 'formData' => $this->formDataJson, 'date' => date('Y-m-d H:i:s'), 'hired' => 0));

            if ($stmtInsert->rowCount() > 0) {
                if($this->gta == 'yes'){$gtaSql = 1;}else{$gtaSql = 0;}
                $stmtSetStudentGraduateStatus = $this->conn->prepare($sqlSetStudentGraduateStatus);
                $stmtSetStudentGraduateStatus->execute(array(':graduate'=>$gtaSql, ':sID'=>unserialize($_SESSION['User'])->getSID()));
                $_SESSION['appStatus'] = 'Application submitted';
            } else {
                $_SESSION['appStatus'] = 'Failed to insert application';
            }
            $this->closeConn();
        }
    }

}
