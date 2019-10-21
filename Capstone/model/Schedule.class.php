<?php
include_once('TA_ics_reader.php');
include_once('Section.class.php');

class Schedule {
    private $schedJSON;
    private $schedArr;
    private $termScheds;
    private $termSchedByDay;

    private $restrictedDays;  //    Used to store the applicants restricted Days

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

    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    function __construct($param) {
        if (is_numeric($param)) {
            $this->termScheds = array();
            $this->getTermSchedByDay = array();
            $this->openConn();
            $sql = "SELECT * FROM Application WHERE aID = :aID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':aID' => $param));
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->schedJSON = $row['courseSchedule'];
                $this->loadSchedule();
            }
        }
        //  If we get passed a Json, we have sched
        else if($this->isJson($param)) {
            $this->termScheds = array();
            $this->termSchedByDay = array();
            $this->schedJSON = $param;
            $this->loadSchedule();
        }
    }

    public function setRestrictedDays($param) {
        $this->restrictedDays = $param;
    }

    private function loadSchedule() {
        $reader = new TA_ics_reader();
        $schedArr = $reader->getArrayFromJSON($this->schedJSON);
        // Iterate through and add each to assigned terms.
        unset($schedArr['id']); // unneeded data
        foreach($schedArr as $sect) {
            // determine term
            $sdate = date_parse($sect['start']);
            $edate = date_parse($sect['end']);
            if(in_array($sdate['month'], array(9, 5))) {
                // September or May
                $sect['term'] = 1;
            } else if(in_array($sdate['month'], array(1, 7))) {
                // January or July
                $sect['term'] = 2;
            } else {
                $sect['term'] = 0;
            }
            $sect['startTime'] = $sdate['hour'].':'.$sdate['minute'];
            $sect['endTime'] = $edate['hour'].':'.$edate['minute'];

            // Add to appropriate term
            $i = $sect['term'];
            $j = $sect['dept'].$sect['code'];
            $this->termScheds[$i][$j][] = $sect;
        }
    }

    /**
     * @param null
     * @return Array of registered courses in particular session.
     */
    public function getEnrolledCourses() {
        return array_merge($this->getTermSched(1), $this->getTermSched(2));
    }

    /**
     * @param int $term
     * @return Array of registered courses in a term.
     */
    public function getTermSched($term) {
        if(isset($this->termScheds[$term]))
            return $this->termScheds[$term];
        else
            return false;
    }

    /**
     * @param int $term
     * @param string $day Single character denoting weekday
     * @return Array of registered courses in a term.
     */
    public function getTermSchedByDay($term, $day) {
        if(isset($this->termSchedByDay[$term])) {
            return (isset($this->termSchedByDay[$term][$day])) ? $this->termSchedByDay[$term][$day] : false;
        } else { // Build schedule by day
            $sched = $this->getTermSched($term);
            if($sched == false)
                return false;
            $this->termSchedByDay[$term] = array('M' => array(), 'T' => array(), 'W' => array(), 'R' => array(), 'F' => array());
            foreach($sched as $course) {
                foreach($course as $item) {
                    switch ($item['day']) {
                        case 'MO':
                            $this->termSchedByDay[$term]['M'][] = $item;
                            break;
                        case 'TU':
                            $this->termSchedByDay[$term]['T'][] = $item;
                            break;
                        case 'WE':
                            $this->termSchedByDay[$term]['W'][] = $item;
                            break;
                        case 'TH':
                            $this->termSchedByDay[$term]['R'][] = $item;
                            break;
                        case 'FR':
                            $this->termSchedByDay[$term]['F'][] = $item;
                            break;
                    }
                }
            }
        }
        return $this->termSchedByDay[$term][$day];
    }

    /**
     * Takes a section the applicant could potentially TA, checks if compatible in their schedule.
     * This function assumes the section passed in corresponds to the same year and session as the Schedule.
     * @param Section $s
     * @return boolean True if there is a conflict
     */
    public function isConflict(Section $s) {
        $days = str_split($s->getDays());
        $json = json_decode($this->restrictedDays, true);
        foreach($days as $day) {
            if(strcmp($day, ' ') != 0) {
                $sched = $this->getTermSchedByDay($s->getTerm(), $day);
                if (is_array($sched) && sizeof($sched) > 0) {
                    foreach ($sched as $item) {
                        $sTime = explode(':', date('G:i', strtotime($item['start'])));
                        $eTime = explode(':', date('G:i', strtotime($item['end'])));
                        // Recreate times on set date to compare them
                        $sTime = mktime($sTime[0], $sTime[1], 00, 1, 1, 1990);
                        $eTime = mktime($eTime[0], $eTime[1], 00, 1, 1, 1990);
                        $sectStartTm = explode(':', $s->getStartTime());
                        $sectEndTm = explode(':', $s->getEndTime());
                        $sectionStime = mktime($sectStartTm[0], $sectStartTm[1], 00, 1, 1, 1990);
                        $sectionEtime = mktime($sectEndTm[0], $sectEndTm[1], 00, 1, 1, 1990);
                        if (($sTime >= $sectionStime && $sTime < $sectionEtime) || ($sectionStime >= $sTime && $sectionStime < $eTime)) {
                            return 1; // conflict
                        }
                    }
                }
                if(isset($json[$day])) {
                    if(strcmp($json[$day],'AllDay') == 0) {
                        return 1;
                    }
                    else {
                        $timeArr = explode(" | ", $json[$day]);
                        $sTimeR = explode(":", $timeArr[0]);
                        $sTimeR = mktime($sTimeR[0], $sTimeR[1] , 00, 1, 1, 1990);

                        $eTimeR = explode(":", $timeArr[1]);
                        $eTimeR = mktime($eTimeR[0], $eTimeR[1] , 00, 1, 1, 1990);

                        $sectStartTm = explode(':', $s->getStartTime());
                        $sectEndTm = explode(':', $s->getEndTime());
                        $sectionStime = mktime($sectStartTm[0], $sectStartTm[1], 00, 1, 1, 1990);
                        $sectionEtime = mktime($sectEndTm[0], $sectEndTm[1], 00, 1, 1, 1990);

                        if (($sTimeR >= $sectionStime && $sTimeR < $sectionEtime) || ($sectionStime >= $sTimeR && $sectionStime < $eTimeR)) {
                            return 1; // conflict
                        }
                    }
                }
            }
        }
        return 0;
    }
}
?>