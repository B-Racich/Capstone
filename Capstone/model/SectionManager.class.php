<?php

include_once __DIR__.'/../../lib/Database.class.php';

class SectionManager {

    /**
     * Used in merging courses for crosslisting purposes to check if two sections are the same.
     * A section is the same if they occur at the same time, in same classroom, and in same term
     * and session.
     *
     * @param Section $sec1
     * @param Section $sec2
     *
     * @return true if sections are the same
     */
    public static function checkDupl(Section $sec1, Section $sec2) {
        return ($sec1->getDays() == $sec2->getDays() && $sec1->getStartTime() == $sec2->getStartTime() && $sec1->getEndTime() == $sec2->getEndTime() && $sec1->getBuilding() == $sec2->getBuilding() && $sec1->getRoom() == $sec2->getRoom() && $sec1->getSessionYear() == $sec2->getSessionYear() && $sec1->getSessionCode() == $sec2->getSessionCode() && $sec1->getTerm() == $sec2->getTerm());
    }

    public static function getSectionConflicts($sCode, $sYear) {
        $database = new Database();
        $conn = $database->getConnection();
        $sql = "SELECT sectionID,daysMet,startTime,endTime,term FROM Section WHERE sessionYear = :sessionYear AND sessionCode = :sessionCode AND secType = :secType";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(':sessionYear' => $sYear, ':sessionCode' => $sCode, ':secType' => 'LAB'));
        $sects = array();
        $ret = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sects[] = $row;
        }
        for($i=0;$i<count($sects);$i++) {
            $conflicting = array();
            for($j=0;$j<count($sects);$j++) {
                if($i == $j) {
                    $conflicting[] = $sects[$j]['sectionID'];
                } else {
                    if (SectionManager::checkConflict($sects[$i], $sects[$j]) == 1)
                        $conflicting[] = $sects[$j]['sectionID'];
                }
            }
            $ret[$sects[$i]['sectionID']] = $conflicting;
        }
        return $ret;
    }

    public static function checkConflict(array $S1, array $S2) {
        if($S1['term'] != $S2['term'])
            return 0;
        $days = str_split($S1['daysMet']);
        foreach($days as $day) {
            if(strcmp($day, ' ') != 0) {
                foreach(str_split($S2['daysMet']) as $day2) {
                    if(strcmp($day2, ' ') != 0) {
                        if($day != $day2)
                            continue;
                        $sTime = explode(':', date('G:i', strtotime($S1['startTime'])));
                        $eTime = explode(':', date('G:i', strtotime($S1['endTime'])));
                        // Recreate times on set date to compare them
                        $sTime = mktime($sTime[0], $sTime[1], 00, 1, 1, 1990);
                        $eTime = mktime($eTime[0], $eTime[1], 00, 1, 1, 1990);
                        $sectStartTm = explode(':', $S2['startTime']);
                        $sectEndTm = explode(':', $S2['endTime']);
                        $sectionStime = mktime($sectStartTm[0], $sectStartTm[1], 00, 1, 1, 1990);
                        $sectionEtime = mktime($sectEndTm[0], $sectEndTm[1], 00, 1, 1, 1990);
                        if (($sTime >= $sectionStime && $sTime < $sectionEtime) || ($sectionStime >= $sTime && $sectionStime < $eTime))
                            return 1; // conflict
                    }
                }

            }
        }
        return 0;
    }
}

?>