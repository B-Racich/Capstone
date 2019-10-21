function editTaAccount(uID, userName, firstName, lastName, email, graduate){
    $.ajax({
        type:   'post',
        url:    '../model/UserHandler.php',
        data:   {functionCall: 'editTaAccount', uID: uID, userName: userName, firstName: firstName, lastName: lastName, email: email, graduate: graduate},
        success: function (data) {
            if(!data) {
                alert("Edit TA NOT successfull");//I changed these back to alerts from "updateStatus" as we cannot see them as we are scrolled far too low
            }
            //	If success
            else {
                alert("Edit TA successfull");//I changed these back to alerts from "updateStatus" as we cannot see them as we are scrolled far too low
                window.location.href = '../admin-edit-TA.php';
            }
        }
    })
}

function adminLoadTA(userName) {
    $.ajax({
        type:   'post',
        url:    '../model/UserHandler.php',
        data:   {functionCall: 'adminLoadTA', userName: userName},
        success: function (data) {
            if(!data) {
                updateStatus("Retreiving TA info unsuccessful");
            }
            //	If success
            else {
				var dataSplit = data.split(','); //Splitting retrieved TA info by comma
                if(dataSplit[0] == ""){
                    alert("No TA found by the name of " + document.getElementById('inputusername').value);//I changed these back to alerts from "updateStatus" as we cannot see them as we are scrolled far too low
                }
				document.getElementById('uid').value = dataSplit[1]; //This is a hidden element to pass through to the adminEditTA function
				document.getElementById('beforeusername').value = dataSplit[0]; document.getElementById('username').value = dataSplit[0];
				document.getElementById('beforefirstname').value = dataSplit[2]; document.getElementById('firstname').value = dataSplit[2];
				document.getElementById('beforelastname').value = dataSplit[3]; document.getElementById('lastname').value = dataSplit[3];
				document.getElementById('beforeemail').value = dataSplit[4]; document.getElementById('email').value = dataSplit[4];
				document.getElementById('beforegraduate').value = dataSplit[6]; document.getElementById('graduate').value = dataSplit[6];
            }
        }
    })
}

$(document).ready(function() {
	//Listener to list TA info
	$('#admin-edit-ta').submit(function(e) {
        e.preventDefault();
        adminLoadTA($('#inputusername').val());
    });

	document.getElementById("updateTA").addEventListener("click", function(){
		editTaAccount($('#uid').val(), $('#username').val(), $('#firstname').val(), $('#lastname').val(), $('#email').val(), $('#graduate').val());
	});
});
