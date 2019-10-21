<?php include 'header.php';?>

<link rel="stylesheet" href="./css/form.css" />
<link rel="stylesheet" href="./css/table.css" />
<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/admin-set-restrictions-controller.js"></script>

<div class="wrapper">
    <div class="align-center">
        <div class="main row row-center space-evenly">

            <div class="c-r-wrapper">
                <h2 id="course-title"></h2>
                <div>
                    <h3>Restrictions</h3>
                    <div class="restriction-wrapper">
                        <div class="select-wrapper">
                            <select id="restrictions">
                                <option id="restr-1">labHours</option>
                                <option id="restr-2">markingHours</option>
                                <option id="restr-3">prepHours</option>
                                <option id="restr-4">otherHours</option>
                                <option id="restr-5">minAvgOverall</option>
                                <option id="restr-6">minAvgInSubject</option>
                                <option id="restr-7">minCredits</option>
                                <option id="restr-8">UTAminGrade</option>
                                <option id="restr-9">prereq</option>
                            </select>
                        </div>
                        <div id = "input-wrapper">
                            <div id='prereqsSelect' class='flex column flex-center' style='display: none'">
                                <div class='select-wrapper'>
                                    <select id='courseReqs'></select>
                                </div>
                                <div class='flex flex-row'>
                                    <button id='courseReqAddBtn'>Add</button>
                                    <button id='courseReqRemoveBtn'>Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button id="updateBtn">Update</button>
                    </div>
                </div>
            </div>

            <div class="rest-list">
                <table id="table">
                    <tr id="firstRow">
                        <h2>Restriction List</h2>
                        <!-- <th id="courseTitle">-Selected Course-</th> -->
                    </tr>
                </table>
                <div class="row row-center">
                    <button id="saveBtn">Save</button>
                </div>
            </div>

        </div>
    </div>
</div>