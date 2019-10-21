<?php
include_once('Course.class.php');

class Section {
    private $sectionID;
    private $courseID;

    private $sectionNumber;
    private $days;
    private $startTime;
    private $endTime;
    private $building;
    private $room;
    private $enrolled;
    private $maxEnrolled;
    private $term;
    private $isOptimized = 1;
    private $secStartDate;
    private $secEndDate;
    private $sessionYear;
    private $sessionCode;
    private $secType;

    private $conn;

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

    /**
     * Close database connection upon object destruction.
     */
    function __destruct() {
        $this->closeConn();
    }

    function __construct($line, $headers = null) {
        if(is_array($line) && is_array($headers)) {
            $this->sectionNumber = $line[$headers['Sec No']];
            $this->days = $line[$headers['Days Met']];
            $this->startTime = $line[$headers['Start Time']];
            $this->endTime = $line[$headers['End time']];
            $this->building = $line[$headers['Building']];
            $this->room = $line[$headers['Room']];
            $this->term = $line[$headers['Term']];
            $this->enrolled = $line[$headers['Total Enrolment']];
            $this->maxEnrolled = $line[$headers['Total Released Seats']];
            $this->secStartDate = date('Y-m-d', strtotime($line[$headers['Meeting Start Date']]));
            $this->secEndDate = date('Y-m-d', strtotime($line[$headers['Meeting End Date']]));
            $this->sessionYear = $line[$headers['Session Year']];
            $this->sessionCode = $line[$headers['Session Code']];
            $this->secType = $line[$headers['Act Type']];
        } else if(is_numeric($line) && $headers == null) {
            $sectionID = $line;

            $this->openConn();
            $sql = "SELECT * FROM Section WHERE sectionID = :sectionID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':sectionID' => $sectionID));
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->sectionID = $row['sectionID'];
                $this->courseID = $row['courseID'];
                $this->sectionNumber = $row['secNo'];
                $this->days = $row['daysMet'];
                $this->startTime = $row['startTime'];
                $this->endTime = $row['endTime'];
                $this->building = $row['building'];
                $this->room = $row['room'];
                $this->term = $row['term'];
                $this->enrolled = $row['totalEnrolment'];
                $this->maxEnrolled = $row['totalReleasedSeats'];
                $this->secStartDate = $row['secStartDate'];
                $this->secEndDate = $row['secEndDate'];
                $this->sessionYear = $row['sessionYear'];
                $this->sessionCode = $row['sessionCode'];
                $this->secType = $row['secType'];
            }
            $this->closeConn();
        }
    }

    public function save() {
        if($this->conn == null)
            $this->openConn();

        if($this->sectionID != null) {
            $this->updateDB();
        } else {
            $sql = "SELECT * FROM Section WHERE courseID = :courseID AND secNo = :secNo AND term = :term AND sessionYear = :sessionYear AND sessionCode = :sessionCode";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':courseID' => $this->courseID, ':secNo' => $this->sectionNumber, ':term' => $this->term, ':sessionYear' => $this->sessionYear, ':sessionCode' => $this->sessionCode));
            if($stmt->rowCount() > 0) {
                // update existing section record
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->sectionID = $row['sectionID'];
                $this->updateDB();
            } else {
                $sql = "INSERT INTO
                Section (courseID, secNo, daysMet, startTime, endTime, totalReleasedSeats, totalEnrolment,
                building, room, term, isOptimized, secStartDate, secEndDate, sessionYear, sessionCode, secType)
                VALUES (:courseID, :secNo, :daysMet, :startTime, :endTime, :totalReleasedSeats, :totalEnrolment,
                :building, :room, :term, :isOptimized, :secStartDate, :secEndDate, :sessionYear, :sessionCode, :secType)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':courseID' => $this->courseID, ':secNo' => $this->sectionNumber, ':daysMet' => $this->days, ':startTime' => date("H:i:00", strtotime($this->startTime)), ':endTime' => date("H:i:00", strtotime($this->endTime)), ':totalReleasedSeats' => $this->maxEnrolled, ':totalEnrolment' => $this->enrolled, ':building' => $this->building, ':room' => $this->room, ':term' => intval($this->term), ':isOptimized' => $this->isOptimized, ':secStartDate' => $this->secStartDate, ':secEndDate' => $this->secEndDate, ':sessionYear' => $this->sessionYear, ':sessionCode' => $this->sessionCode, ':secType' => $this->secType));
                $this->sectionID = $this->conn->lastInsertId();
            }
        }
        $this->closeConn();
    }

    private function updateDB() {
        $this->openConn();
        $sqlu = "UPDATE Section SET courseID = :courseID, secNo = :secNo, daysMet = :daysMet, startTime = :startTime, endTime = :endTime, totalReleasedSeats = :totalReleasedSeats, totalEnrolment = :totalEnrolment, building = :building, room = :room, term = :term, isOptimized = :isOptimized, secStartDate = :secStartDate, secEndDate = :secEndDate, sessionYear = :sessionYear, sessionCode = :sessionCode, secType = :secType WHERE sectionID = :sectionID";
        $stmtu = $this->conn->prepare($sqlu);
        $stmtu->execute(array(':courseID' => $this->courseID, ':secNo' => $this->sectionNumber, ':daysMet' => $this->days, ':startTime' => date("H:i:00", strtotime($this->startTime)), ':endTime' => date("H:i:00", strtotime($this->endTime)), ':totalReleasedSeats' => $this->maxEnrolled, ':totalEnrolment' => $this->enrolled, ':building' => $this->building, ':room' => $this->room, ':term' => intval($this->term), ':isOptimized' => $this->isOptimized, ':secStartDate' => $this->secStartDate, ':secEndDate' => $this->secEndDate, ':sessionYear' => $this->sessionYear, ':sessionCode' => $this->sessionCode, ':secType' => $this->secType, ':sectionID' => $this->sectionID));
        $this->closeConn();
    }

    public function setCourseID($courseID) {
        $this->courseID = $courseID;
    }

    public function setSectionID($sectionID) {
        $this->sectionID = $sectionID;
    }

    public function getAssignedTA() {
        $this->openConn();
        $sql = "SELECT * FROM SectionTA WHERE sectionID = :sectionID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':sectionID'=>$this->sectionID));
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sql2 = "SELECT userName FROM TA,Student,UserAccount WHERE TA.taID = :taID AND TA.sID = Student.sID AND UserAccount.uID = Student.uID";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->execute(array(':taID'=>$row['taID']));
            if($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                return $row['userName'];
            }
            else {
                return '-';
            }

        }
        $this->closeConn();
    }

    /** Remove the current TA from this section
     *
     */
    public function removeTA() {
        $this->openConn();
        $sql = "DELETE FROM SectionTA WHERE sectionID = :sectionID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':sectionID'=>$this->sectionID));
        $this->closeConn();
        if($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    /** Adds the TA by ID to this section
     *
     */
    public function addTA($taID, $labHours, $markingHours, $otherHours, $other2Hours) {
        $this->openConn();
        $sqlCheck = "SELECT * FROM SectionTA WHERE sectionID = :sectionID";
        $stmt = $this->conn->prepare($sqlCheck);
        $stmt->execute(array(':sectionID'=>$this->sectionID));

        if($stmt->rowCount() == 0) {
            $sql = "INSERT INTO SectionTA VALUES (:sectionID, :taID, :labHours, :markingHours, :otherHours, :other2Hours)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':sectionID'=>$this->sectionID, ':taID'=>$taID, ':labHours'=>$labHours, ':markingHours'=>$markingHours, ':otherHours'=>$otherHours, ':other2Hours'=>$other2Hours));

            if($stmt->rowCount() > 0) {
                $this->closeConn();
                return true;
            }
            else {
                $this->closeConn();
                return false;
            }
        }
        else
            return false;
    }

    /**
     * @return mixed
     */
    public function getSectionNumber()
    {
        return str_pad($this->sectionNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return mixed
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @return mixed
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @return mixed
     */
    public function getEnrolled()
    {
        return $this->enrolled;
    }

    /**
     * @return mixed
     */
    public function getMaxEnrolled()
    {
        return $this->maxEnrolled;
    }

    /**
     * @return mixed
     */
    public function getIsOptimized()
    {
        return $this->isOptimized;
    }

    /**
     * @param value for isOptimized
     */
    public function setIsOptimized($var)
    {
        $this->isOptimized = intval($var);
    }

    /**
     * @return mixed
     */
    public function getSecStartDate()
    {
        return $this->secStartDate;
    }

    /**
     * @return mixed
     */
    public function getSecEndDate()
    {
        return $this->secEndDate;
    }

    /**
     * @return mixed
     */
    public function getSessionYear()
    {
        return $this->sessionYear;
    }

    /**
     * @return mixed
     */
    public function getSessionCode()
    {
        return $this->sessionCode;
    }

    /**
     * @return mixed
     */
    public function getSecType()
    {
        return $this->secType;
    }

    /**
     * @return mixed
     */
    public function getTerm()
    {
        return intval($this->term);
    }

    /**
     * @return mixed
     */
    public function getCourseID()
    {
        return $this->courseID;
    }

    public function getSectionID(){
        return $this->sectionID;
    }
}

?>