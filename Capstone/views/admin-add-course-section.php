<?php include 'header.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);?>

<!-- <link rel="stylesheet" href="css/master.css" /> -->
<link rel="stylesheet" href="css/form.css" />
<link rel="stylesheet" href="css/table.css" />
<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/admin-add-course-section-controller.js"></script>


<div class="flex center">
    <p id="statusText" class="hidden row-center"></p>
</div>
<div class="wrapper">
    <div class="align-center">
        <div class="main row row-center space-evenly">

            <div>

                <h3>Courses</h3>
                <div class="select-wrapper">
                    <select id="courses">
                    </select>
                </div>

            </div>


            <div>

                <h4>Delete Section(s) or Entire Selected Course</h4>
                <table class="format-table ta-table" id="courseSecTable">

                  <thead class="headings">
                    <tr id="firstRow">
                        <th id="courseTitle">Course Section</th>
                        <th>ID</th>
                        <th>Time</th>
                        <th># Students</th>
                    </tr>
                  </thead>

                  <tbody id='content'>

                  </tbody>

                </table>

                <button id="delete-sect" type="submit" class="button" >Delete Section</button>
                <button id="delete-course" type="submit" class="button" >Delete Course</button>

                <br><br><br>

                <h4>Add Section to Selected Course</h4>
                <table class="format-table ta-table" id="addSectionTable">

                  <thead class="headings">
                    <tr id="firstRow">
                        <th>Lab Section ID</th>
                        <th>Days (MTWRF)</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th># Seats</th>
                        <th>Building</th>
                        <th>Room</th>
                    </tr>
                  </thead>

                  <tbody id='add-sect-content'>
                    <tr id="firstRow">
                        <td><input type="text" id='addsect-secNo' placeholder="L01" required></td>
                        <td><input type="text" id='addsect-daysMet' placeholder="MTWRF" required></td>
                        <td><input type="time" id='addsect-startTime' required></td>
                        <td><input type="time" id='addsect-endTime' required></td>
                        <td><input type="number" id='addsect-numSeats' placeholder="20" required></td>
                        <td><input type="text" id='addsect-building' placeholder="FIP" required></td>
                        <td><input type="text" id='addsect-room' placeholder="133" required></td>
                    </tr>

                  </tbody>

                </table>

                <button id="add-section" type="submit" class="button" >Add Section</button>

                <br><br><br>

                <h4>Add NEW Course</h4>
                <table class="format-table ta-table" id="addCourseTable">

                  <thead class="headings">
                    <tr id="firstRow">
                        <th>Subject Code</th>
                        <th>Course Number</th>
                        <th>Long Title</th>
                        <th>Lab Hrs</th>
                        <th>Marking Hrs</th>
                        <th>Prep Hrs</th>
                        <th>Other Hrs</th>
                    </tr>
                  </thead>

                  <tbody id='add-course-content'>
                    <tr id="firstRow">
                        <td><input type="text" id='addcourse-subject' placeholder="COSC" required></td>
                        <td><input type="number" id='addcourse-courseNum' placeholder="101" required></td>
                        <td><input type="text" id='addcourse-longTitle' placeholder="Digital Citizenship" required></td>
                        <td><input type="number" id='addcourse-labHrs' placeholder="1" required></td>
                        <td><input type="number" id='addcourse-markingHrs' placeholder="2" required></td>
                        <td><input type="number" id='addcourse-prepHrs' placeholder="2" required></td>
                        <td><input type="number" id='addcourse-otherHrs' placeholder="2" required></td>
                    </tr>

                  </tbody>

                  <thead class="headings">
                    <tr id="secondRow">
                        <th>Lecture Section ID</th>
                        <th>Days (MTWRF)</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th># Seats</th>
                        <th>Building</th>
                        <th>Room</th>
                    </tr>
                  </thead>

                  <tbody id='add-lec-content'>
                    <tr id="secondRow">
                        <td><input type="number" id='addcourse-secNo' placeholder="1" required></td>
                        <td><input type="text" id='addcourse-daysMet' placeholder="MTWRF" required></td>
                        <td><input type="time" id='addcourse-startTime' required></td>
                        <td><input type="time" id='addcourse-endTime' required></td>
                        <td><input type="number" id='addcourse-numSeats' placeholder="20" required></td>
                        <td><input type="text" id='addcourse-building' placeholder="FIP" required></td>
                        <td><input type="text" id='addcourse-room' placeholder="133" required></td>
                    </tr>

                  </tbody>

                  <thead class="headings">
                    <tr id="thirdRow">
                        <th>Term</th>
                        <th>Section Start Date</th>
                        <th>Section End Date</th>
                        <th>Session Year</th>
                        <th>Session Code</th>
                    </tr>
                  </thead>

                  <tbody id='add-lec-content'>
                    <tr id="thirdRow">
                        <td><select id='addlec-term' required>
                              <option>1</option>
                              <option>2</option></td>
                        <td><input type="date" id='addcourse-startDate' required></td>
                        <td><input type="date" id='addcourse-endDate' required></td>
                        <td><input type="number" id='addcourse-sessionYear' required ></td>
                        <td><select id='addcourse-sessionCode' required>
                              <option>W</option>
                              <option>S</option></td>
                    </tr>

                  </tbody>

                </table>
                <h5>Note: to add sections to this new course refresh the page,<br> select this new course and fill out the add section part</h5>
                <button id="add-course" type="submit" class="button" >Add Course</button>


            </div>

        </div>
    </div>
</div>
