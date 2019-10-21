<?php
include_once('Course.class.php');

class CourseManager {

    private $year;
    private $code;
    private $term;
    private $head;

    private $filters;

    private $conn;

    private $validFilters = array('courseID', 'subject', 'courseNum', 'isOptimized');

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

    /**
     * @param mixed $year Year of courses we want.
     * @param mixed $code Session code.
     * @param mixed $term Term course is offered in.
     * @return void
     */
    function __construct($year, $code, $term) {
        $this->openConn();
        $this->filters = array();
        $this->year = $year;
        $this->code = $code;
        $this->term = $term;

        // Build array to pass to Course objects on creation.
        $this->head = array('year' => $this->year, 'scode' => $this->code, 'term' => $this->term);
    }

    public function addFilter($name, $value) {
        if(in_array($name, $this->validFilters)) {
            $this->filters[$name] = $value;
            return true;
        }
        return false;
    }

    public function rmFilter($name) {
        unset($this->filters[$name]);
    }

    /**
     * Remove all filters applied to course search.
     * @param null
     * @return null
     */
    public function resetFilters() {
        $this->filters = array();
    }

    /**
     * Remove all filters applied to course search.
     * @param null
     * @return Array of Courses
     */
    public function getByCriteria() {
        $ret = array();
        $sql = "SELECT DISTINCT C.courseID FROM Course C INNER JOIN Section S ON C.courseID=S.courseID WHERE S.sessionYear = :sessionYear AND S.sessionCode = :sessionCode AND S.term = :term";
        $params = array(':sessionCode' => $this->code, ':sessionYear' => $this->year, ':term' => $this->term);
        foreach($this->filters as $name => $val) {
            $sql .= ' AND '.$name.'=:'.$name;
            $params[':'.$name] = $val;
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ret[] = new Course($row['courseID'], $this->head);
            echo $row['courseID'];
        }
        return $ret;
        return (!empty($ret)) ? $ret : false;
    }
}
?>
