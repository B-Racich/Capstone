function updateHoursandComment(id, minHours, maxHours, comment) {
    $.ajax({
        type:   'post',
        url:    '../model/admin_ta_list_model.php',
        data:   {functionCall: 'updateHoursandComment', id: id, minHours: minHours, maxHours: maxHours, comment: comment},
        success: function (results) {
            if(results == true) {
                updateStatus("Update successful");
            }
            else {
                updateStatus("Update FAILED");
            }
        }
    })
}

function listUTAs(sessionSelection) {
  $.ajax({
    type:   'post',
        url:    '../model/admin_ta_list_model.php',
        data:   {functionCall: 'listUTAs', sessionSelection: sessionSelection},
        success: function (results) {
            $('#content').html(results);
            // alert(results);
        }
    })
}

function listGTAs(sessionSelection) {
  $.ajax({
    type:   'post',
        url:    '../model/admin_ta_list_model.php',
        data:   {functionCall: 'listGTAs', sessionSelection: sessionSelection},
        success: function (results) {
            $('#contentgta').html(results);
            // alert(results);
        }
    })
}

function emailSelectedTAs(sessionSelection) {
    var emailTAList = [];
    var numOfTACheckboxes = 0;
    var numOfGTACheckboxes = 0;

    //Counting how many UTA's and GTA's are listed on the admin-ta-list page
    for(var j = 0; j < 10000; j++){
        if(document.getElementById("TAcheckbox" + j) != null)
            numOfTACheckboxes++;
        else if(document.getElementById("TAcheckbox" + j) == null)
            break;
    }
    for(var j = 0; j < 10000; j++){
        if(document.getElementById("GTAcheckbox" + j) != null)
            numOfGTACheckboxes++;
        else if(document.getElementById("GTAcheckbox" + j) == null)
            break;
    }

    //Generate the list of emails from the selected UTA's and GTA's
	var count = 0;
	for(var i = 0; i < numOfTACheckboxes; i++){
		if(document.getElementById("TAcheckbox" + i).checked){
			emailTAList[count] = document.getElementById("TAemail" + i).innerHTML;//
            count++;
		}
	}
    for(var i = 0; i < numOfGTACheckboxes; i++){//Generate the list of emails from the selected GTA's
		if(document.getElementById("GTAcheckbox" + i).checked){
			emailTAList[count] = document.getElementById("GTAemail" + i).innerHTML;//
            count++;
		}
	}
  $.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/admin_ta_list_model.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'emailSelectedTAs',emailTAList: emailTAList, sessionSelection: sessionSelection},
    success: function (data) {
		if(!data) {
			updateStatus("Data false");
		}
		else {
			//	If success
			updateStatus("Email(s) successfully sent");
		}
    }
  })
}

// This is the main method of the controller, this runs on the page loading
$(document).ready(function() {

  listUTAs($("#sessionSelection").find(":selected").val());
  listGTAs($("#sessionSelection").find(":selected").val());

      $("#sessionSelection").change(function() {
          var sessionSelection = $("#sessionSelection").find(":selected").val();
          listUTAs(sessionSelection);
          listGTAs(sessionSelection);
      });

      //Both email buttons
      document.getElementById("emailOffers").addEventListener("click", function(){
          emailSelectedTAs($("#sessionSelection").find(":selected").val());
  	  });
      document.getElementById("emailOffers2").addEventListener("click", function(){
          emailSelectedTAs($("#sessionSelection").find(":selected").val());
  	  });

      //uta
      $('#submituta').click(function(){

        $("#utarow.ta-info-row").each(function(){

            var id = $(this).find('#utaname').attr('name');
            var minHours = $(this).find('#utamin').val();
            var maxHours = $(this).find('#utamax').val();
            var comment = $(this).find('#utacomment').val();
            // alert(id + " " + minHours + " " + maxHours + " " + comment);

            updateHoursandComment(id,minHours,maxHours,comment);
        })

      });

      //gta
      $('#submitgta').click(function(){

        $("#gtarow.ta-info-row").each(function(){

            var id = $(this).find('#gtaname').attr('name');
            var minHours = $(this).find('#gtamin').val();
            var maxHours = $(this).find('#gtamax').val();
            var comment = $(this).find('#gtacomment').val();
            // updateStatus(id + " " + minHours + " " + maxHours + " " + comment);

            updateHoursandComment(id,minHours,maxHours,comment);
        })

      });

      $("#printbtn").click(function(){
          window.print();

      });

});
