function isUserLoggedIn() {
    $.ajax({
        type:   'post',
        url:    '../model/UserHandler.php',
        data:   {functionCall: 'isLoggedIn'},
        success: function (data) {}
    })
}

function logout() {
    $.ajax({
        type:   'post',
        url:    '../model/UserHandler.php',
        data:   {functionCall: 'logout'},
        success: function () {}
    })
}

$(document).ready(function() {

    isUserLoggedIn();

    $('#logout').click(function() {
        logout();
    });

});
