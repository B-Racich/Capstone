
function forgotpass(email, username) {
  $.ajax({
    type: 'post',
    url:  '../model/forgot_password_model.php',
	  data: {functionCall: 'sendReset',email: email, username: username},
    success: function (data) {
      if(data){
		    alert("Confirmation email sent. Please check your email."); //success
      }else{
        alert("Email/Username does NOT exist. Please enter valid information."); //failure
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus); alert("Error: " + errorThrown);
    }
  })
}


// This is the main method
$(document).ready(function() {

	$('#forgotPassForm').submit(function(e) {
    var email = $('#email').val();
    var username = $('#username').val();
		forgotpass(email, username);
	});

});
