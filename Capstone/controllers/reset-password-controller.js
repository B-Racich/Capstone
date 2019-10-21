
function resetpass(email, username, password, check) {
  $.ajax({
    type: 'post',
    url:  '../model/forgot_password_model.php',
	  data: {functionCall: 'resetPassword',email: email, username: username, password: password, check: check},
    success: function (data) {
      if(data){
		    alert("Your password has successfully been reset."); //success
      }else{
        alert("Sorry password reset failed. Email/Username is incorrect. Try again."); //failure
      }
    }
  })
}
function checkPasswords(pass1, pass2) {
    if(pass1 === pass2) {
        return true;
    }
    else {
        updateStatus("Passwords do not match");
        return false;
    }
}

// code sourced from: https://css-tricks.com/snippets/javascript/get-url-variables/
function getQueryVariable(variable)
{
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}

// This is the main method
$(document).ready(function() {

  var check = getQueryVariable("check");

  $('#resetPassForm').submit(function(e) {
      e.preventDefault();
      if(checkPasswords($('#pass').val(), $('#confirmpass').val()) && check) {
          resetpass($('#email').val(), $('#username').val(), $('#pass').val(), check);
      }
  });

});
