<?php 
// include_once __DIR__.'/TA_ics.php';

include_once __DIR__.'/../../public_html/model/Student.php';

class TA_ics_reader {

    /* Function is to get all the contents from ics and explode all the datas according to the events and its sections */
    function getIcsEventsAsArray($file) {
        $ical = file_get_contents ( $file );
        $icsDates = array ();
        $icsData = explode ( "BEGIN:", $ical );
        foreach ( $icsData as $key => $value ) {
            $icsDatesMeta [$key] = explode ( "\n", $value );
        }
        foreach ( $icsDatesMeta as $key => $value ) {
            foreach ( $value as $subKey => $subValue ) {
                $icsDates = $this->getICSDates ( $key, $subKey, $subValue, $icsDates );
            }
        }
        return $icsDates;
    }

    function getICSDates($key, $subKey, $subValue, $icsDates) {
        if ($key != 0 && $subKey == 0) {
            $icsDates [$key] ["BEGIN"] = $subValue;
        } else {
            $subValueArr = explode ( ":", $subValue, 2 );
            if (isset ( $subValueArr [1] )) {
                $icsDates [$key] [$subValueArr [0]] = $subValueArr [1];
            }
        }
        return $icsDates;
    }

    function getJSONEvents($events){

        $counter = 0;
        $section =array();
        $JSONEvents= array() ;

        // $JSONEvents['id'] = $_SESSION['User']->getSID();

        $timezone = $this->getTimezone($events);
        $events = array_slice($events,10);
        foreach($events as $icsEvent){
            $secEvent = array();
            $secTitle = "section".$counter;
            $startTimeAndDate = $this->getStartTimeAndDate($icsEvent,$timezone);
            $endTimeAndDate = $this->getEndTimeAndDate($icsEvent,$timezone);

            $sectionDesc = $this->getCourseDescInfo($icsEvent);


            $dept = $this->getCourseDept($sectionDesc);
            $code = $this->getCourseCode($sectionDesc);
            $sess = $this->getSectionSession($startTimeAndDate);

            $day = $this->getDaysMet($icsEvent);
            
            // $desc = $dept.$code. " ".substr($sess, 0,1);

            $section['start'] = $startTimeAndDate;
            $section['end'] = $endTimeAndDate;
            $section['dept'] = $dept;
            $section['code'] = $code;
            $section['session'] = $sess;
            $section['day'] = $day;
            
            $sectionJSON = json_encode($section);

            $JSONEvents[$secTitle] = $sectionJSON;

            $counter++;
            
        }
        $JSONEvents = json_encode($JSONEvents,JSON_FORCE_OBJECT);

        return $JSONEvents;

    }

    /*
     * Given JSON from getJSONEvents(), return array of events.
     * @param json string
     * @return Array of events.
     */
    function getArrayFromJSON($json){
        $res = (array)json_decode($json);
        foreach($res as $key => $row) {
            $r = (array)json_decode($row);
            if(isset($r['session']))
                $r['session'] = substr($r['session'], 0, 1);
            if(isset($r['day']))
                $r['day'] = trim($r['day']);
            $res[$key] = $r;
        }

        return $res;
    }


    function printEvents($events){
        $timezone = $this->getTimezone($events);
        // print_r($events);
        $events = array_slice($events,10);
        // print_r($events);
        
        $html = '<table><tr><td> Event </td><td> Start at </td><td> End at </td><td>Session</td></tr>';
   
        
        foreach( $events as $icsEvent){
   
            $startTimeAndDate = $this->getStartTimeAndDate($icsEvent,$timezone);
            $endTimeAndDate = $this->getEndTimeAndDate($icsEvent,$timezone);

            $sectionDesc = $this->getCourseDescInfo($icsEvent);
            
            $dept = $this->getCourseDept($sectionDesc);
            $code = $this->getCourseCode($sectionDesc);
            $sess = $this->getSectionSession($startTimeAndDate);
            $day = $this ->getDaysMet($icsEvent);
            $desc = $dept.$code. " ".$sess;
            // $dept = "a";
        
        $html .= '<tr><td>'.$dept." ".$code.'</td><td>'.$startTimeAndDate.'</td><td>'.$endTimeAndDate.' '.$day.'</td><td>'.$sess.'</td></tr>';
     
       
        }
        return $html.'</table>';

    }

    function display($json){
        foreach($event as $e){
            print_r($e);
        }
    }



    function getStartTimeAndDate($event, $timezone){
        $startTime = substr($event['DTSTART;TZID='.$timezone], strpos($event['DTSTART;TZID='.$timezone], "T") +1 );
        $startDate = substr($event['DTSTART;TZID='.$timezone], 0, strpos($event['DTSTART;TZID='.$timezone], "T"));
        $chunks = str_split($startTime, 2);
        $startTime = substr(implode(':', $chunks),0,-2);
        
        $hms = $startTime;
        $ymd = substr($startDate, 0,4)."-".substr($startDate, 4,2)."-".substr($startDate, 6);
        
        $date = date_create($ymd." ".$hms);
        $startString =  date_format($date,'Y-m-d H:i:s');

        return trim($startString);
    }

    function getEndTimeAndDate($event, $timezone){
        
        $endTime = substr( $event['DTEND;TZID='.$timezone], strpos($event['DTEND;TZID='.$timezone], "T") +1 );
        $endDate = substr($event['DTEND;TZID='.$timezone], 0, strpos($event['DTEND;TZID='.$timezone], "T"));
        $chunks = str_split($endTime, 2);
        $endTime = substr(implode(':', $chunks),0,-2);
        
        $hms = $endTime;
        $ymd = substr($endDate, 0,4)."-".substr($endDate, 4,2)."-".substr($endDate, 6);
        
        $date = date_create($ymd." ".$hms);
        $endString =  date_format($date,'Y-m-d H:i:s');

        return trim($endString);
    }

    function getTimezone($event){
        return trim ( $event[2]["TZID"] );
    }

    function getCourseDescInfo($event){

        $sectionDesc = explode( ' ', $event['SUMMARY']);

        
        return $sectionDesc;
    }

    function getCourseDept($sectionDesc){
        /*  ical file used is only indexable at [0][0] */
      
    
        $dept = $sectionDesc[0];
        return  $dept;
    }

    function getCourseCode($sectionDesc){
        /* ical file used is only indexable at [0][0] */
        $code = $sectionDesc[1];
        
        // $code = substr($sectionDesc[0][0],5,3);

        return $code;
    }

    function getSectionSession($timeAndDate) {
        /* ical file used is only indexable at [0][0]
        $sess = substr($sectionDesc[3][1],0,1);
        */
        $sess = 'W';
       $month = $this->getSectionMonth($timeAndDate);
        switch($month){
            case 1: $sess = 'W';
                break;
            case 5: $sess = 'S';
                break;
            case 9: $sess = 'W';
                break;
        }
        return $sess;

    }

    function getDaysMet($icsEvent){
        $pos = strpos($icsEvent['RRULE'],"BYDAY=");
        return substr($icsEvent['RRULE'], $pos+ 6);
    }

    function getSectionMonth($timeandDate){
        return substr($timeandDate,5,2);

    }
}

?>