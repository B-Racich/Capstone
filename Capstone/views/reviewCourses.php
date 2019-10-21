<?php
include_once('../model/SectionLoader.class.php');
include_once('../model/Course.class.php');
include_once('../model/Section.class.php');
include_once('../model/Professor.class.php');

include_once('header.php');
if(!isset($_SESSION))
    session_start();

function sanitizeHrs($val) {
    if(trim($val) == '') {
        $val = 0;
    }
    return intval($val);
}

echo '<link rel="stylesheet" type="text/css" href="css/master.css">'."\n";
echo '<link rel="stylesheet" type="text/css" href="css/table.css">'."\n";
echo '<link rel="stylesheet" type="text/css" href="css/course-list.css">'."\n";
if(!isset($_SESSION['uploadCourses']) || $_SESSION['uploadCourses'] !== true) {
    error('You must be here by accident; no courses have been uploaded.');
} else {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $admHrs = $_POST['admHrs'];
        $tchHrs = $_POST['tchHrs'];
        $mrkHrs = $_POST['mrkHrs'];
        $prpHrs = $_POST['prpHrs'];

        $objCourses = $_SESSION['objCourses'];
        foreach ($objCourses as $year => $row)
            foreach ($row as $code => $rowTerm)
                foreach ($rowTerm as $term => $courses)
                    foreach ($courses as $key => $course) {
                        $id = $code . $year . 'T' . $term . $key;
                        $course->setOtherHours(sanitizeHrs($admHrs[$id]));
                        $course->setLabHours(sanitizeHrs($tchHrs[$id]));
                        $course->setMarkingHours(sanitizeHrs($mrkHrs[$id]));
                        $course->setPrepHours(sanitizeHrs($prpHrs[$id]));

                        // By default a section is set to optimized=true; if no hours entered
                        // for all options, set all sections to not be optimized.
                        if(sanitizeHrs($admHrs[$id]) == 0 && sanitizeHrs($tchHrs[$id]) == 0 && sanitizeHrs($mrkHrs[$id]) == 0 && sanitizeHrs($prpHrs[$id]) == 0)
                            $course->setOptimized(false);

                        $course->save();
                    }
        $_SESSION['uploadCourses'] = false;
        $_SESSION['objCourses'] = null;
        // Redirect admin
        header('Location:admin-portal.php');
        success('Successfully imported courses.');
    } else {
        echo '<div class="wrapper">' . "\n";
        echo '<div class="inner_wrapper  course-list-wrapper in_grid">' . "\n";
        echo '<div class="row headings">' . "\n";
        echo '<div class="col-4">' . "\n";
        echo '<h2>Course Name</h2>' . "\n";
        echo '</div>' . "\n";
        echo '<div class="col-8">' . "\n";
        echo '<h2>Section Information</h2>' . "\n";
        echo '</div>' . "\n";
        echo '</div>' . "\n";

        echo '<form action="" method="post">';
        // echo '<input type="submit" value="Save" name="submit">';
        $objCourses = $_SESSION['objCourses'];
        foreach ($objCourses as $year => $row) {
            foreach ($row as $code => $rowTerm) {
                echo '<h3>' . $code . $year . '</h3>' . "\n";
                foreach ($rowTerm as $term => $courses) {
                    echo '<h3>Term ' . $term . '</h3>' . "\n";
                    foreach ($courses as $key => $course) {
                        // check if crosslisted course
                        $crosslisted = array();
                        $crosslist_pr = '';
                        if ($course->getCrosslisted() != '') {
                            $crosslisted = explode(",", $course->getCrosslisted());
                            for ($i = 0; $i < count($crosslisted); $i++)
                                $crosslisted[$i] = substr($crosslisted[$i], 0, 7);
                            $crosslist_pr = ', ' . implode(", ", $crosslisted);
                        }

                        echo '<div class="row course-row" id="course-' . $key . '">' . "\n";
                        echo '<div class="col-4">' . "\n";
                        echo '<h3 style="margin-bottom: 0px;">' . $key . $crosslist_pr . '</h3>' . "\n";
                        echo '<h5>' . $course->getTitle() . '</h5>' . "\n";
                        $sections = $course->getSections();
                        // Determine number labs, length of lab
                        $numLec = 0;
                        $numLab = 0;
                        $labLen = 0;
                        foreach (array_reverse($sections) as $section) {
                            if ($section->getSecType() == 'LEC')
                                $numLec++;
                            else {
                                $numLab++;
                                $labLen = strtotime("1 January 1970 " . $section->getEndTime()) - strtotime("1 January 1970 " . $section->getStartTime());
                            }
                        }
                        $admHrs = '';
                        $tchHrs = '';
                        $mrkHrs = '';
                        $prpHrs = '';
                        if ($numLab > 6)
                            $admHrs = 2;
                        if ($numLec > 0 && $numLab > 0) {
                            $tchHrs = $labLen / 3600;
                            $mrkHrs = $labLen / 3600;
                            $prpHrs = 2;
                        }

                        echo '<table style="width:190px;" class="course-hours">' . "\n";
                        echo '<tr><td>Teaching Hours</td><td><input type="text" name="tchHrs[' . $code . $year . 'T' . $term . $key . ']" size="1" value="' . $tchHrs . '"/></td></tr>' . "\n";
                        echo '<tr><td>Marking Hours</td><td><input type="text" name="mrkHrs[' . $code . $year . 'T' . $term . $key . ']" size="1" value="' . $mrkHrs . '" /></td></tr>' . "\n";
                        echo '<tr><td>Preparation Hours</td><td><input type="text" name="prpHrs[' . $code . $year . 'T' . $term . $key . ']" size="1" value="' . $prpHrs . '" /></td></tr>' . "\n";
                        echo '<tr><td>Admin/Other Hours</td><td><input type="text" name="admHrs[' . $code . $year . 'T' . $term . $key . ']" size="1" value="' . $admHrs . '" /></td></tr>' . "\n";
                        echo '</table>' . "\n";
                        echo '</div>' . "\n";
                        echo '<div class="col-8">' . "\n";
                        echo '<div class="scrollable scrollable-wrapper course-lab ">' . "\n";
                        echo '<table class="lab-info-table-upload course-table">' . "\n";
                        echo '						<thead>
                                    <tr>
                                        <th>BLOCK</th>
                                        <th>ID</th>
                                        <th>TIME</th>
                                        <th># STUDENTS</th>
                                    </tr>
                                </thead>' . "\n";
                        foreach ($sections as $section) {
                            if (strcmp($section->getSecType(), 'LAB') == 0) {
                                echo '<tr class="lab-info-row-upload">' . "\n";
                                echo '<td><input type="checkbox"></td>' . "\n";
                                echo '<td class="lab-id">' . $section->getSectionNumber() . '</td>' . "\n";
                                echo '<td class="lab-time">' . $section->getDays() . ' ' . $section->getStartTime() . '-' . $section->getEndTime() . '</td>' . "\n";
                                echo '<td class="lab-num">' . $section->getEnrolled() . '</td>' . "\n";
                                echo '</tr>' . "\n";
                            }
                        }
                        echo '</table>' . "\n";
                        echo '</div>' . "\n";
                        echo '</div>' . "\n";
                        echo '</div>' . "\n";
                    }
                }
            }
        }
        echo '<input type="submit" value="Save" name="submit">';
        echo '</form>' . "\n";
        echo '</div>' . "\n";
        echo '</div>' . "\n";
    }
}
?>
