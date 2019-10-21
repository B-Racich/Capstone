/**
 * Check to see if selected session is already submitted
 */
function hasApplication(session) {
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/ta_application_model.php',	//	path to file
        data: {functionCall: 'hasApplication', session: session},
        success: function (results) {
            if(results) {
                var sess;
                if(session === '2018 S')
                    sess = 'Summer';
                else if(session === '2018 W')
                    sess = 'Winter';
                updateStatus('Notice: You have already submitted an application for ' + sess);
                hasSubmitted = true;
            } else {
                hasSubmitted = false;
            }
        }
    })
}

/**
 * Submit application to DB
 */
var hasSubmitted;
function submitApplication() {
    var form = $("#applicationForm")[0];
    var formData = new FormData(form);
    formData.append('submitApp',true);
    formData.append('functionCall', 'submitApplication');
    for(key in formData) {
        console.log(key);
    }
    var days = buildJSON();
    formData.append('days',days);
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        processData: false,
        contentType: false,
        url:  '../model/ta_application_model.php',	//	path to file
        data: formData,
        success: function (results) {
            hasSubmitted = true;
            updateStatus(results);
             window.setTimeout(function(){
                 window.location.href = "../ta-portal.php";
             }, 2000);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            updateStatus(errorThrown);
            hasSubmitted = false;
        }
    })
}

function buildJSON() {
    var days = {};

    $('#whenCantWorkTable > tbody  > tr').each(function() {
        var weekday = $(this).find("select[name='selectweekday']").find(":selected").val();
        if(weekday != null) {
            var time = "";
            var allDay = $(this).find("input[name='allday']").is(":checked");
            if(allDay) {
                time = "AllDay";
            }else {
                var timeBefore = $(this).find("input[name='from-time']").val();
                var timeAfter = $(this).find("input[name='to-time']").val();
                time = timeBefore + " | " + timeAfter;
            }
            days[weekday] = time;
        }
    });
    console.log(days);
    return JSON.stringify(days);
}

$(document).ready(function() {

    $("input[name='session']").change(function() {
        hasApplication($(this).val());
    });

    $('#applicationForm').submit(function(e) {
        e.preventDefault();
        if(hasSubmitted) {
            if(confirm("Submitting this application will replace your last application received, do you wish to precede?")) {
                submitApplication();
            }
        } else {
            submitApplication();
            hasSubmitted = true;
        }
    })
    $('.tabs-stage div').hide();
    $('.tabs-stage div:first').show();
    $('.tabs-nav li:first').addClass('tab-active');
    
    // Change tab class and display content
    $('.tabs-nav a').on('click', function(event){
      event.preventDefault();
      $('.tabs-nav li').removeClass('tab-active');
      $(this).parent().addClass('tab-active');
      $('.tabs-stage div').hide();
      $($(this).attr('href')).show();
    });
});