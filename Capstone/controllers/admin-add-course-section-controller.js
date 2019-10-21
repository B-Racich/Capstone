/** Populate the <select> of courses from a JSON object
 *
 */
 function getCourseInformation() {
     var options = "";
     var courseID;
     var subject;
     var courseNum;

     var first = true;

     $.ajax({
         type: 'post',	//	Type of request GET/POST
         url:  '../model/admin_add_course_section.php',	//	path to file
         data: {functionCall: 'getCoursesJson'},
         dataType: 'json',
         success: function (data) {
             for(var i = 0; i < data.length; i++) {
                 courseID = "course-" + data[i]['courseID'];
                 subject = data[i]['subject'];
                 courseNum = data[i]['courseNum'];

                 //  Start option
                 options += "<option id='" + courseID + "'>";
                 options += subject + " " + courseNum;
                 options += "</option>"; //  Close option
             }
             $("#courses").html(options);
             // alert('alert1');
             getCourseSection();
         }
     });
 }

/** This function updates the information for the selected course
 *
 */
function getCourseSection() {
    //  Update the text
    var courseid = $("#courses").find(":selected").attr('id').split('-')[1];
    var name = $("#courses").find(":selected").val();
    $("#courseTitle").text(name);
    clearTable();

    //  Ajax for displaying course sections
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/admin_add_course_section.php',	//	path to file
        data: {functionCall: 'getCourseSection', courseID: courseid},
        success: function (results) {
            // alert('check 3');
            $('#content').html(results);
            // alert(results);
        },
        error:
          function(XMLHttpRequest, textStatus, errorThrown) {
          console.log("Status: " + textStatus);
          console.log("Error: " + errorThrown);
        }
    });
}

/** Clear the HTML table
 *
 */
function clearTable() {
    $("#table").find("tr:gt(0)").remove();
}

/** Delete checkboxed sections of the course
 *
 */
function deleteSection(sectionid, courseid) {

  $.ajax({
      type: 'post',	//	Type of request GET/POST
      url:  '../model/admin_add_course_section.php',	//	path to file
      data: {functionCall: 'deleteSection', sectionID: sectionid, courseID: courseid},
      success: function (results) {
          //basically a refresh
          // if(results){
          //   alert("sections deleted successfully");
          // }else {
          //   alert("sections failed to delete, try again");
          // }
          getCourseSection();
      }
  });
}

/** Delete entire selected course
 *
 */
function deleteCourse(sectionid, courseid) {

  $.ajax({
      type: 'post',	//	Type of request GET/POST
      url:  '../model/admin_add_course_section.php',	//	path to file
      data: {functionCall: 'deleteCourse', sectionID: sectionid, courseID: courseid},
      success: function (results) {
          //basically a refresh
          if(results){
            alert("course deleted successfully");
          }else {
            alert("course failed to delete, try again");
          }
          getCourseSection();
      }
  });
}

/** add new section to selected course
 *
 */
function addSection(courseid, secNo, daysMet, startTime, endTime, totalReleasedSeats, building, room) {
  $.ajax({
      type: 'post',	//	Type of request GET/POST
      url:  '../model/admin_add_course_section.php',	//	path to file
      data: {functionCall: 'addSection', courseID: courseid, secNo: secNo, daysMet: daysMet, startTime: startTime, endTime: endTime, totalReleasedSeats: totalReleasedSeats, building: building, room: room},
      success: function () {
          //basically a refresh
          getCourseSection();
      }
  });
}

/** add an entire NEW course
 *
 */
function addCourse(subject, courseNum, longTitle, labHours, markingHours, prepHours, otherHours, secNo, daysMet, startTime, endTime, totalReleasedSeats, building, room, term, secStartDate, secEndDate, sessionYear, sessionCode) {

  $.ajax({
      type: 'post',	//	Type of request GET/POST
      url:  '../model/admin_add_course_section.php',	//	path to file
      data: {functionCall: 'addCourse', subject: subject, courseNum: courseNum, longTitle: longTitle, labHours: labHours, markingHours: markingHours, prepHours: prepHours, otherHours: otherHours, secNo: secNo, daysMet: daysMet, startTime: startTime, endTime: endTime, totalReleasedSeats: totalReleasedSeats, building: building, room: room, term: term, secStartDate: secStartDate, secEndDate: secEndDate, sessionYear: sessionYear, sessionCode: sessionCode},
      success: function (data) {
          if (data){
            alert("it worked");
          } else {
            alert("SWitching it up to check");
          }

          //basically a refresh
          getCourseSection();
      }
  });
}



$(document).ready(function() {

    //getting the dropdown
    getCourseInformation();


    /**
    * ------ checking for dropdown selection changes
    */
    $("#courses").change(function() {
        getCourseSection();
    });



    /**
    * ------ checking for 'DELETE SECTION' button click
    */
    $('#delete-sect').click(function(){

      $("tr.lab-info-row").each(function(){

          var checked = $(this).find('#sectioncheckbox').is(":checked");

          if (checked) {
            var sectionid = $(this).attr('name');
            var courseid = $("#courses").find(":selected").attr('id').split('-')[1];

            var confirmdeletesect = confirm("Are you sure you want to delete this section(s)?");
            if(confirmdeletesect){
              deleteSection(sectionid, courseid);
            }
            // alert(sectionid);
            // alert(courseid);
          }

      })

    });



    //------ checking for 'DELETE COURSE' button click
    $('#delete-course').click(function(){
      var courseid = $("#courses").find(":selected").attr('id').split('-')[1];

      var confirmdeletecourse = confirm("Are you sure you want to delete this ENTIRE course?");
      if(confirmdeletecourse){
        deleteCourse(courseid);
      }
    });


    /**
    * ----- checking for 'ADD SECTION' button click
    */
    $('#add-section').click(function(){
      var courseid = $("#courses").find(":selected").attr('id').split('-')[1];
      var secNo = $('#addsect-secNo').val();
      var daysMet = $('#addsect-daysMet').val();
      var startTime = $('#addsect-startTime').val();
      var endTime = $('#addsect-endTime').val();
      var totalReleasedSeats = $('#addsect-numSeats').val();
      var building = $('#addsect-building').val();
      var room = $('#addsect-room').val();

      // alert(courseid + " " + secNo + " " + daysMet + " " + startTime + " " + endTime + " " + totalEnrolment);

      addSection(courseid, secNo, daysMet, startTime, endTime, totalReleasedSeats, building, room);
    });



    //----- checking for 'ADD COURSE' button click
    $('#add-course').click(function(){
      //(7 variables)
      var subject = $('#addcourse-subject').val();
      var courseNum = $('#addcourse-courseNum').val();
      var longTitle = $('#addcourse-longTitle').val();
      var labHours = $('#addcourse-labHours').val();
      var markingHours = $('#addcourse-markingHours').val();
      var prepHours = $('#addcourse-prepHours').val();
      var otherHours = $('#addcourse-otherHours').val();

      //(7 variables)
      var secNo = $('#addcourse-secNo').val();
      var daysMet = $('#addcourse-daysMet').val();
      var startTime = $('#addcourse-startTime').val();
      var endTime = $('#addcourse-endTime').val();
      var totalReleasedSeats = $('#addcourse-totalReleasedSeats').val();
      var building = $('#addcourse-building').val();
      var room = $('#addcourse-room').val();

      //(5 variables)
      var term = $('#addcourse-term').val();
      var secStartDate = $('#addcourse-secStartDate').val();
      var secEndDate = $('#addcourse-secEndDate').val();
      var sessionYear = $('#addcourse-sessionYear').val();
      var sessionCode = $('#addcourse-sessionCode').val();

      alert(subject, courseNum, longTitle, labHours, markingHours, prepHours, otherHours, secNo, daysMet, startTime, endTime, totalReleasedSeats, building, room, term, secStartDate, secEndDate, sessionYear, sessionCode);

      addCourse(subject, courseNum, longTitle, labHours, markingHours, prepHours, otherHours, secNo, daysMet, startTime, endTime, totalReleasedSeats, building, room, term, secStartDate, secEndDate, sessionYear, sessionCode);
    });

});
