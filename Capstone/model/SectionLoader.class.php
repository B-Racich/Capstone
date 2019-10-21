<?php
include_once('Course.class.php');
include_once('Section.class.php');
include_once('Professor.class.php');
include_once('SectionManager.class.php');

class SectionLoader {
    private $filePath;
    private $header;
    private $courses;
    private $reqed_headers = array('Subject', 'Course', 'Long Title', 'Cross Listed', 'Sec No', 'Days Met', 'Start Time', 'End time', 'Term', 'Session Year', 'Session Code', 'Act Type');

    public function __construct(string $path) {
        $this->filePath = $path;
    }

    /**
     * Reads courses in CSV format, result is an array of courses.
     * @param null
     * @return null
     */
    public function validateCourses() {
        $data = array();
        if (($f = fopen($this->filePath, "r")) !== FALSE) {
            while (($row = fgetcsv($f, 3000, ",")) !== FALSE) {
                $data[] = $row;
            }
            fclose($f);
        }
        $i = 0;
        while($data[$i][0] != 'Instructor Names' && $i < sizeof($data)) {
            $i++;
        }
        $this->header = $data[$i];

        // Create (hash)map with header names pointing to respective IDs
        $headIds = array();
        for($i=0;$i<count($this->header);$i++) {
            $headIds[$this->header[$i]] = $i;
        }

        foreach($this->reqed_headers as $title) {
            if(!isset($headIds[$title])) {
                error('One of more required headers missing from CSV file.');
                return false;
            }
        }

        $typeIdx = array_search('Sec Type', $this->header);
        $k = 0;
        $arrSize = sizeof($data);
        while($k < $arrSize) {
            if(trim($data[$k][$typeIdx]) !== 'REGU')
                unset($data[$k]); // Remove waitlisted sections and header row
            $k++;
        }
        $arr_courses = array_values($data);

        // For each course with a distinct professor, create a course object, add a section for each section within the course, this is all a multidimensional array,
        // output to user to enter course info
        $obj_courses = array(); // Indexed by course subject and number ie. COSC111
        $subjectIdx = array_search('Subject', $this->header);
        $numberIdx = array_search('Course', $this->header);
        $yearIdx = array_search('Session Year', $this->header);
        $codeIdx = array_search('Session Code', $this->header);
        $termIdx = array_search('Term', $this->header);

        foreach($arr_courses as $row) {
            // Eliminate whitespace around values
            foreach($row as $i => $val) {
                $row[$i] = trim($val);
            }

            // Check if we have record for this course in given year/session.
            $key = $row[$subjectIdx] . $row[$numberIdx];
            if (!isset($obj_courses[$row[$yearIdx]][$row[$codeIdx]][$row[$termIdx]][$key])) {
                $obj_courses[$row[$yearIdx]][$row[$codeIdx]][$row[$termIdx]][$key] = new Course($row, $headIds);
            } else {
                // Directly create a section and add it to the corresponding course.
                $nSec = new Section($row, $headIds);
                $obj_courses[$row[$yearIdx]][$row[$codeIdx]][$row[$termIdx]][$key]->addSection($nSec);
            }
        }
        $this->courses = $obj_courses;
        $this->handleCrosslisting();
        return true;
    }

    /**
     * Helper method to condense crosslisted courses to be represented by one course (the course mentioned earliest in CSV file).
     * Any unique labs between the lab sections will be condensed to one course.
     * @param null
     * @return null
     */
    private function handleCrosslisting() {
        if(empty($this->courses))
            return;

        $toRemove = array();
        foreach ($this->courses as $year => $row) {
            foreach ($row as $code => $rowTerm) {
                foreach ($rowTerm as $term => $courses) {
                    foreach ($courses as $key => $course) {
                        // If already staged for removal, skip
                        if(isset($toRemove[$year.$code.$term.$key]))
                            continue;

                        if ($course->getCrosslisted() != '') {
                            $crosslisted = explode(",", $course->getCrosslisted());
                            foreach ($crosslisted as $clkey => $cl) {
                                $cl = substr($cl, 0, 7);
                                $crosslisted[$clkey] = $cl;

                                if(isset($this->courses[$year][$code][$term][$cl])) {
                                    // Check if crosslisted course include additional sections to merge
                                    foreach($this->courses[$year][$code][$term][$cl]->getSections() as $clSecs) {
                                        $dupFound = false;
                                        foreach ($course->getSections() as $cSec) {
                                            if (SectionManager::checkDupl($cSec, $clSecs)) {
                                                $dupFound = true;
                                                break;
                                            }
                                        }
                                        if (!$dupFound) {
                                            $course->addSection($clSecs);
                                        }
                                    }
                                } else {
                                    // Course not actually offered, remove mention of crosslisting
                                    unset($crosslisted[$clkey]);
                                }
                                // Stage this course for removal, as it is now represented by first course in list
                                $toRemove[$year.$code.$term.$cl] = array('year' => $year, 'code' => $code, 'term' => $term, 'course' => $cl);
                            }
                            // What is left are the crosslisted courses
                            $course->setCrosslisted(implode(',', $crosslisted));
                        }
                    }
                }
            }
        }

        // Remove courses staged for removal
        foreach($toRemove as $rm) {
            unset($this->courses[$rm['year']][$rm['code']][$rm['term']][$rm['course']]);
        }
    }

    /**
     * Return array of Course objects.
     *
     * @return Course array
     */
    public function getCourses() {
        return $this->courses;
    }

}
?>
