<?php
include_once __DIR__.'/../../lib/Database.class.php';

class Transcript {
    private $transcriptJSON;
    private $transcript = array();

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

    function __construct($aID) {
        $this->openConn();
        $sql = "SELECT * FROM Application WHERE aID = :aID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':aID' => $aID));
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->transcriptJSON = $row['transcript'];
            $this->transcript = json_decode($this->transcriptJSON, true);
            $this->formatTranscript();
        }
    }

    /*
     * Helper function to be called only once to format JSON object.
     */
    private function formatTranscript() {
        // get headers
        $headers = $this->transcript[0];
        $rm = array();
        $len = count($this->transcript);
        for($k=1; $k<$len;$k++) {
            // remove empty entries
            $keys = array_keys($this->transcript[$k]);
            $this->transcript[$k][$keys[0]] = substr($this->transcript[$k][$keys[0]], 0, 4).substr($this->transcript[$k][$keys[0]], strlen($this->transcript[$k][$keys[0]])-3, 3);

            if(empty($this->transcript[$k]) || count($this->transcript[$k]) != count($headers)) {
                unset($this->transcript[$k]);
                continue;
            }

            if(isset($this->transcript[$k]['Grade']))
                $this->transcript[$k]['Grade'] = (int) $this->transcript[$k]['Grade'];

            foreach($headers as $i => $head) {
                $this->transcript[$k][$head] = $this->transcript[$k][$i];
                unset($this->transcript[$k][$i]);
            }
        }
        // Remove headers
        unset($this->transcript[0]);
        $this->transcript = array_values($this->transcript);
    }

    /*
     * Get transcript array.
     * @return Array
     */
    public function getTranscript() {
        return $this->transcript;
    }

    private function computeAverage($grades) {
        $vals = array();
        foreach($grades as $item) {
            if(empty($item['Grade']) || $item['Grade'] > 100 || $item['Grade'] < 0)
                continue;
            $vals[] = $item['Grade'];
        }
        return (count($vals) > 0) ? round(array_sum($vals)/count($vals), 1) : 0;
    }

    /*
     * Get average over some subset of courses.
     * @param $csubject String/null string of 4 letter course code we want average in. Does not count currently enrolled courses.
     * @return Numeric Average in applicable courses.
     */
    public function getAvg($csubject = null) {
        if($csubject == null)
            return $this->computeAverage($this->getTranscript());

        // get only courses in this subject
        $build = array();
        foreach($this->getTranscript() as $item) {
            $keys = array_keys($item);
            $c = substr($item[$keys[0]], 0, 4);
            if($c == $csubject) {
                $build[] = $item;
            }
        }
        return $this->computeAverage($build);
    }

    /*
     * Return total credits accomplished according to student transcript.
     * @return int
     */
    public function getTotalCredits() {
        $res = 0;
        foreach($this->getTranscript() as $item) {
            $val = (is_numeric($item['Credits Earned'])) ? $item['Credits Earned'] : 0;
            $res += $val;
        }
        return $res;
    }

    /*
     * Determine whether student has completed course. Returns -1 if not, otherwise returns their grade (0-100).
     * @param $csubject Four letter course code
     * @param $cnumber Three digit course number
     * @return int
     */
    public function hasCompleted($csubject, $cnumber) {
        // try to find record of this course
        foreach($this->getTranscript() as $item) {
            $keys = array_keys($item);
            if(isset($item[$keys[0]]) && $item[$keys[0]] == ($csubject.$cnumber))
                return ($item['Grade'] >= 0 && $item['Grade'] <= 100) ? $item['Grade'] : -1;
        }
        return false;
    }
}
?>