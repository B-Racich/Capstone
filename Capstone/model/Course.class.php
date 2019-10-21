<?php
include_once('Section.class.php');
include_once('Professor.class.php');

class Course {
    private $courseID;
    private $subject;
    private $number;
    private $title;
    private $crosslisted;
    private $labHours;
    private $markingHours;
    private $prepHours;
    private $otherHours;
    private $minAvgOverall = 85;
    private $minAvgInSubject = 85;
    private $minCredits = 30;
    private $UTAminGrade = 85;
    private $prereq = '';

    private $year;
    private $code;
    private $term;

    private $conn;

    private $sections = array();
    private $professors = array();

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
            $this->professors[] = new Professor($line[$headers['Instructor Names']]); // @TODO: Handle multiple professors for one course?
            $this->subject = $line[$headers['Subject']];
            $this->number = $line[$headers['Course']];
            $this->title = $line[$headers['Long Title']];
            $this->crosslisted = trim($line[$headers['Cross Listed']]);
            $this->year = $line[$headers['Session Year']];
            $this->code = $line[$headers['Session Code']];
            $this->term = $line[$headers['Term']];

            // Add section information.
            $this->addSection(new Section($line, $headers));
        } else if(is_numeric($line) && is_array($headers)) { // get course by internal ID, and array of session, year, term
            $courseID = $line;

            $this->openConn();
            $sql = "SELECT * FROM Course WHERE courseID = :courseID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':courseID' => $courseID));
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->courseID = $row['courseID'];
                $this->subject = $row['subject'];
                $this->number = $row['courseNum'];
                $this->title = $row['longTitle'];
                $this->crosslisted = $row['crosslisted'];
                $this->labHours = $row['labHours'];
                $this->markingHours = $row['markingHours'];
                $this->prepHours = $row['prepHours'];
                $this->otherHours = $row['otherHours'];
                $this->minAvgOverall = $row['minAvgOverall'];
                $this->minAvgInSubject = $row['minAvgInSubject'];
                $this->minCredits = $row['minCredits'];
                $this->UTAminGrade = $row['UTAminGrade'];
                $this->prereq = $row['prereq'];
                $this->year = $headers['year'];
                $this->code = $headers['scode'];
                $this->term = $headers['term'];

                // Construct array of sections
                $sql2 = "SELECT sectionID FROM Section WHERE courseID = :courseID AND sessionCode = :sessionCode AND sessionYear = :sessionYear AND term = :term";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->execute(array(':courseID' => $courseID, ':sessionCode' => $this->getCode(), ':sessionYear' => $this->getYear(), ':term' => $this->getTerm()));
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $this->addSection(new Section($row2['sectionID']));
                }
            }
        }
    }

    public function save() {
        if($this->conn == null)
            $this->openConn();

        if($this->courseID != null) {
            $this->updateDB();
        } else {
            $sql = "SELECT * FROM Course WHERE subject = :subject AND courseNum = :courseNum";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':subject' => $this->subject, ':courseNum' => $this->number));
            if($stmt->rowCount() > 0) {
                // update existing course record
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->courseID = $row['courseID'];
                $this->updateDB();
            } else {
                $sql = "INSERT INTO Course (subject, courseNum, longTitle, crosslisted, labHours, markingHours, prepHours, otherHours) VALUES (:subject, :courseNum, :longTitle, :crosslisted, :labHours, :markingHours, :prepHours, :otherHours)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':subject' => $this->subject, ':courseNum' => $this->number, ':longTitle' => $this->title, ':crosslisted' => $this->crosslisted, ':labHours' => $this->labHours, ':markingHours' => $this->markingHours, ':prepHours' => $this->prepHours, ':otherHours' => $this->otherHours));
                $this->courseID = $this->conn->lastInsertId();
            }
        }

        // Professors
        foreach($this->getProfessors() as $prof) {
            $prof->save();
            $sql = "SELECT * FROM CourseInstructor WHERE `pID` = :pID AND `courseID` = :courseID AND `year` = :year AND `term` = :term"; // @TODO: session code?
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':pID' => $prof->getProfessorID(), ':courseID' => $this->courseID, ':year' => $this->year, ':term' => intval($this->term)));
            if($stmt->rowCount() == 0) {
                $sql = "INSERT INTO CourseInstructor (`pID`, `courseID`, `year`, `term`) VALUES (:pID, :courseID, :year, :term)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':pID' => $prof->getProfessorID(), ':courseID' => $this->courseID, ':year' => $this->year, ':term' => intval($this->term)));
            }
        }

        // Sections
        foreach($this->getSections() as $sec) {
            $sec->setCourseID($this->courseID);
            $sec->save();
        }

        $this->closeConn();
    }

    /*
     * Helper method for updating Course object data in database.
     */
    private function updateDB() {
        $sqlu = "UPDATE Course SET subject = :subject, courseNum = :courseNum, longTitle = :longTitle, crosslisted = :crosslisted, labHours = :labHours, markingHours = :markingHours, prepHours = :prepHours, otherHours = :otherHours WHERE courseID = :courseID";
        $stmtu = $this->conn->prepare($sqlu);
        $stmtu->execute(array(':subject' => $this->subject, ':courseNum' => $this->number, ':longTitle' => $this->title, ':crosslisted' => $this->crosslisted, ':labHours' => $this->labHours, ':markingHours' => $this->markingHours, ':prepHours' => $this->prepHours, ':otherHours' => $this->otherHours, ':courseID' => $this->courseID));
    }

    /**
     * Mutator for whether course and its sections should be optimized.
     * @param Boolean. TRUE = optimized
     */
    public function setOptimized($bool) {
        if($bool)
            $val = 1;
        else
            $val = 0;
        foreach($this->getSections() as $sec) {
            $sec->setIsOptimized($val);
        }
    }

    /**
     * @param Section $sec Section object which pertains to course of specific type.
     * @return void
     */
    public function addSection(Section $sec) {
        $this->sections[] = $sec;
    }

    /**
     * Getter for Section array.
     * @return Section array. $this->sections
     */
    public function getSections() {
        return $this->sections;
    }

    /**
     * @return array
     */
    public function getProfessors(): array
    {
        return $this->professors;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $labHours
     */
    public function setLabHours($labHours)
    {
        $this->labHours = $labHours;
    }

    /**
     * @param mixed $markingHours
     */
    public function setMarkingHours($markingHours)
    {
        $this->markingHours = $markingHours;
    }

    /**
     * @param mixed $otherHours
     */
    public function setPrepHours($prepHours)
    {
        $this->prepHours = $prepHours;
    }

    /**
     * @param mixed $miscOtherHours
     */
    public function setOtherHours($otherHours)
    {
        $this->otherHours = $otherHours;
    }

    /**
     * @return mixed
     */
    public function getLabHours()
    {
        return $this->labHours;
    }

    /**
     * @return mixed
     */
    public function getCrosslisted()
    {
        return trim($this->crosslisted);
    }

    /**
     * @param string $crosslisted
     */
    public function setCrosslisted(string $crosslisted): void
    {
        $this->crosslisted = trim($crosslisted);
    }

    /**
     * @return mixed
     */
    public function getMarkingHours()
    {
        return $this->markingHours;
    }

    /**
     * @return mixed
     */
    public function getPrepHours()
    {
        return $this->prepHours;
    }

    /**
     * @return mixed
     */
    public function getOtherHours()
    {
        return $this->otherHours;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @return mixed
     */
    public function getCourseID()
    {
        return $this->courseID;
    }

    /**
     * @return int
     */
    public function getMinAvgOverall(): int
    {
        return $this->minAvgOverall;
    }

    /**
     * @return int
     */
    public function getMinAvgInSubject(): int
    {
        return $this->minAvgInSubject;
    }

    /**
     * @return int
     */
    public function getMinCredits(): int
    {
        return $this->minCredits;
    }

    /**
     * @return int
     */
    public function getUTAminGrade(): int
    {
        return $this->UTAminGrade;
    }

    /**
     * @return string
     */
    public function getPrereq(): string
    {
        return $this->prereq;
    }
}
?>