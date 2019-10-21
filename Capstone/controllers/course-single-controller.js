var numOfTAsListed = 0;
var previousTAHours = [];
var dataSplit;

function listCourse(course) {
  $.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/AdminFunction.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'listCourse',course: course},
    success: function (data) {
		if(!data) {
			alert("Data false, could not find any TA's for this course");
			$('#statusText').html("Fail");
		}
		//	If success
		else {
			var count = 0;
			//display course data
			//alert("ajax success. course=" + course);
			//alert("DEBUG:DATA: " + data + " END DATA DEBUG");
			dataSplit = data.split(','); //Splitting retrieved info by comma

			//Populating TA's info into course-single.php
			for (i = 0; i < dataSplit.length-1; i = i + 7) { //dataSplit.length -1 because the dataSplit arrays last element is just a comma, will try to fix later
				document.getElementById("name" + count).innerHTML = dataSplit[i];
				document.getElementById("gtaUta" + count).innerHTML = dataSplit[i+1];
				document.getElementById("prep" + count).value = dataSplit[i+2]; previousTAHours[i+1] = dataSplit[i+2];
				document.getElementById("mark" + count).value = dataSplit[i+3]; previousTAHours[i+2] = dataSplit[i+3];
				document.getElementById("other" + count).value = dataSplit[i+4]; previousTAHours[i+3] = dataSplit[i+4];
				document.getElementById("total" + count).innerHTML = dataSplit[i+5];
				document.getElementById("email" + count).innerHTML = dataSplit[i+6]; previousTAHours[i+4] = dataSplit[i+6];
				count++;
			}
		}
    }
  })
}

function emailSelectedTAs() {
	var emailTAList = [];
	var count3 = 0;
		for(var i = 0; i < numOfTAsListed; i++){
			if(document.getElementById("checkbox" + i).checked){
				alert((i) + "is checked");
				emailTAList[count3] = document.getElementById("email" + i).innerHTML;//email
				emailTAList[count3+1] = document.getElementById("prep" + i).value;//prep
				emailTAList[count3+2] = document.getElementById("mark" + i).value;//mark
				emailTAList[count3+3] = document.getElementById("other" + i).value;//other
				emailTAList[count3+4] = document.getElementById("total" + i).innerHTML;//total
				count3 += 5;
			}
		}
  $.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/AdminFunction.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'emailSelectedTAs',emailTAList: emailTAList},
    success: function (data) {
		if(!data) {
			alert("Data false");
			alert(data);
			$('#statusText').html("Fail");
		}
		else {
			//	If success
			alert("reached the end of emailSelectedTAs ");
			alert(data);
		}
    }
  })
}

function updateTAsHours(prevTAHours) {
	//need to take all of the potentially altered "prep" "mark" and "other" hours to see if they've changed
	var alteredTAHours = [];
	var count2 = 0;
	for(i = 0; i < numOfTAsListed * 4; i = i + 4){
		alteredTAHours[i] = document.getElementById("prep"+ count2).value;
		alteredTAHours[i+1] = document.getElementById("mark"+ count2).value;
		alteredTAHours[i+2] = document.getElementById("other"+ count2).value;
		alteredTAHours[i+3] = document.getElementById("email"+ count2).innerHTML;
		count2++;
	}
  $.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/AdminFunction.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'updateTAsHours', prevTAHours: prevTAHours, alteredTAHours: alteredTAHours},
    success: function (data) {
		if(!data) {
			alert("Data false");
			$('#statusText').html("Fail");
		}
		//	If success
		else {
			//alert("Data true");
			//alert(data);
			alert("TA's successfully updated!");
			location.reload();
		}
    }
  })
}

function numOfTAsInCourse(course){
	$.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/AdminFunction.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'numOfTAsInCourse',course: course},
    success: function (data) {
		if(!data) {
			alert("No TA's in course");
			$('#statusText').html("Fail");
		}
		//	If success
		else {
			//alert("success data= " + data);
			numOfTAsListed = data;
			listCourse(course);//We first had to find the number of TA's in the course and build the table, we can now list the TA's and put them into the table now
		}
    }
  })
}

$(document).ready(function() {
	document.getElementById("emailTaButton").addEventListener("click", function(){
		emailSelectedTAs();
	});
	document.getElementById("updateTaButton").addEventListener("click", function(){
		updateTAsHours(previousTAHours);
	});
});
