<?php
ini_set('display_errors', 1);
include_once('TA_ics_reader.php');
$TA_ics_reader = new TA_ics_reader();
$file = "ical2.ics";
echo "<pre>";
$events = $TA_ics_reader->getIcsEventsAsArray( $file );
$TA_ics_reader->printEvents($events);
// $sectionJSON = $TA_ics_reader->getJSONEvents($events);
// $sectionDecode = json_decode(  $sectionJSON);
?>