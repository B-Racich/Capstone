

function getSchedule(studentID) {
    $.ajax({
        type: 'post',
        url:    '../model/admin_view_schedule.php',
        data:   {functionCall: 'getSchedule', studentID: studentID},
        success: function (data) {
            if(!data) {
                // updateStatus('Data is wrong in the function "loadTAPrevCourseHistory" inside the file "single-ta-info-controller.js"');
                // alert("HELO");
            }
            //	If success
            else {
			//    var info = data.split(',');
                // alert("hello");
                $("#main").html(data);

            }
        }
    })
}
function checkIfStudent(sID){
	$.ajax({
		type: 'post',
		url:    '../model/UserHandler.php',
		data:   {functionCall: 'getAccountType'},
		success: function (data) {
			//alert("DEBUG: checkIfStudent =" + data);
			if(data === "student"){//If a student is logged in, we will get his studentID and use display everything with that
				displayStudentInfo();//We have to go to another function to get his studentID
			}
			else if(data === "administrator"){
				//alert("DEBUG: admin hit, sID="+sID);
				document.getElementById("student-num").innerHTML = sID;//If an admin is logged in, we take the sID sent as a queryString argument in this function
				getSchedule(sID);
				
			}
		}
    })
}
$(document).ready(function() {
});
