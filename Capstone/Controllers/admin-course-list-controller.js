/** List all the courses and their sections
 *
 */
function listCourses() {
    var select = $("#sessionSelection").find(":selected").val();
    var year = select.split(" ")[0];
    var session = select.split(" ")[1];
    var term = $("#termSelection").find(":selected").val();
    $('#main').html("");
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/admin_course_list_model.php',	//	path to file
        data: {functionCall: 'listCourses', session: session, year: year, term: term},
        success: function (data) {
            $('#main').append(data);

            //Attach listeners
            $(".remove-button").click(function() {
                if(confirm("Would you like to remove this TA?")){
                    var assigned = $(this).parents(".lab-info-row").find('.Assigned-TA').attr('id');
                    console.log("current assigned " + assigned);
                    var id = assigned.split("-");
                    var secID = id[0];
                    var taID = id[1];
                    var taName = $(this).parents(".lab-info-row").find('.Assigned-TA').text();

                    $(".lab-info-row").each(function(){
                        var rowclass = $(this).attr("class");
                        var rowclass = rowclass.split(" ")[1];

                        if(rowclass.indexOf(secID) > 0 ){
                            // console.log("removed from: "+ rowclass);
                            $(this).find('.ta-list ').append('<option value="'+taID+'">'+taName+'</option>');
                        }
                    });
                    removeTA($(this));
                }
            });

            $(".add-button").click(function() {
                addTA($(this));
                var id = $(this).prev().find('.ta-list').attr('id');
                var valueSelected = $("#"+id+" option:selected").val();
                var option = "option[value ='"+valueSelected+"']";
                var selector = "select.ta-list " + option;
                // console.log("selector " + selector);
                var notSelector = "select#"+id+" " + option;
                // console.log("not selector " + notSelector);
                // $(selector).not(notSelector).remove();

                var secclass = $(this).parents(".lab-info-row").attr("class");

                var conflicts = secclass.split(" ");
                var currentConflicts = conflicts[1];
                var allConflicts = currentConflicts.split("-");
                var secID =allConflicts[0].substring(1);
                // console.log(secID);
                $(".lab-info-row").each(function(){
                    var rowclass = $(this).attr("class");
                    var rowclass = rowclass.split(" ")[1];

                    if(rowclass.indexOf(secID) > 0 ){
                        // console.log("removed from: "+ rowclass);
                        $(this).find('.ta-list ' + option).remove();
                    }
                });
            });
        }//Success
    });
}


/**	Get the TA's not assigned to specified course
 *
 */
function getMatrixTAs() {
    var select = $("#sessionSelection").find(":selected").val();
    var year = select.split(" ")[0];
    var session = select.split(" ")[1];
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/admin_course_list_model.php',	//	path to file
        data: {functionCall: 'listMatrixTAs', year: year, session:session},
        dataType: 'json',
        success: function (data) {
            //  Start dom
            var dom = "";

            // For every <Select>
            $("select[class='ta-list']").each(function() {
                //  Extract id
                var id = $(this).attr('id').split('-')[1];
                if(data.hasOwnProperty(id)) {
                    var dom = "";
                    var row = data[id];
                    dom += "<option value=\" \">- Select -</option>";
                    for (var key in row) {
                        var info = row[key].split(' ');
                        dom += "<option value='" + info[3] + "'>" + info[1] + " " + info[2] + "</option>";
                    }
                    $(this).append(dom);
                     $(this).parent().addClass('select-wrapper');
                }
                else {
                    $(this).parents('.ta-info-box').empty();
                }

            });
        }
    });
}

/** Adds the selected TA to the selected course
 *
 */
function addTA(button) {
    var sectionID = button.attr('id').split('-')[1];
    var element = '#select-' + sectionID;
    var taName = $(element).find(":selected").text();
    var taID = $(element).find(":selected").val();
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/admin_course_list_model.php',	//	path to file
        //	data is the $_POST[''] variables being sent to the php file
        data: {functionCall: 'addTA', sectionID: sectionID, taID: taID},
        success: function (data) {
            if(data) {
                updateStatus("TA added to section.");
                var name = "#decline-" + sectionID;
                var button = $(name);
                button.removeClass("hidden");
                button.prev().text(taName);
                button.prev().addClass("Assigned-TA");
            }
        }
    });
}

/** Removes selected TA from course
 *
 */
function removeTA(button) {
    var sectionID = button.attr('id').split('-')[1];
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/admin_course_list_model.php',	//	path to file
        //	data is the $_POST[''] variables being sent to the php file
        data: {functionCall: 'removeTA', sectionID: sectionID},
        success: function (data) {
            if(data) {
                updateStatus("TA removed from section.");
                $(button).addClass("hidden");
                $(button).prev().text("");
                $(button).prev().removeClass("Assigned-TA");
            }
        }
    });
}

/** This function builds a JSON object of non blocked sections, their recommended TA info, and assigned TA
 *
 */
function getOptimizationData() {
    var select = $("#sessionSelection").find(":selected").val();
    var year = select.split(" ")[0];
    var session = select.split(" ")[1];
    var term = $("#termSelection").find(":selected").val();
    var tmLim = $("#tmLim").val();
    var utahr = $("#utahr").val();
    var gtahr = $("#gtahr").val();
    var sectionArr = {};
    $(".lab-info-row").each(function() {
        var $checkBox = $(this).find("input[type='checkbox']");
        var checked = $checkBox.prop('checked');
        if(!checked) {
            var sectionID = $checkBox.attr('name');
            if(sectionID != null) {
                sectionID = sectionID.replace("blocked[", "");
                sectionID = sectionID.replace("]","");
                var taArr = [];
                $(this).find(".ta-list > option").each(function() {
                    if($.isNumeric($(this).val())) {
                        taArr.push($(this).val());
                    }
                });

                var ta = $(this).find("span.Assigned-TA");
                if(ta.length !== 0) {
                    var taName = ta.attr('id').split('-')[1];
                    taArr.push(taName);
                } else {
                    taArr.push(null);
                }
            }
        }
        sectionArr[sectionID] = taArr;
    });
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/admin_course_list_model.php',	//	path to file
        data: {functionCall: 'runScheduler', sectionData: JSON.stringify(sectionArr), session: session, year: year, term: term, tmLim: tmLim, utahr: utahr, gtahr: gtahr},
        success: function (status) {
            // do stuff
            console.log("Optimization Status: " + status);
            if(status == 300) {
                updateStatus("Scheduler running, redirecting...")
                window.setTimeout(function(){
                    window.location.href = "../admin-review-schedule.php";
                }, 2000);
            }
        }
    });
}

function goToSingleTAwithCurrentSelectedSession(webstring){
	var currentSessionSelection = document.getElementById("sessionSelection").value; //Grab the current session
	window.location.href = webstring + currentSessionSelection;
}


function removeAssigned(){
    var assigned = $('.Assigned-TA');
    // console.log(assigned);
    for(var i =0 ; i < assigned.length ; i ++){
        var id = assigned[i].id.split("-");
        // console.log(id);
        var secID = id[0];
        var taID = id[1];
         var option = "option[value ='"+taID+"']";
        
         $(".lab-info-row").each(function(){
            var rowclass = $(this).attr("class");
            var rowclass = rowclass.split(" ")[1];


            if(rowclass.indexOf(secID) > 0 ){
             // console.log("removed from: "+ rowclass);
             $(this).find('.ta-list ' + option).remove();

            }
         });
    }
}

$(document).ready(function() {
    listCourses();
  
    $(".box ~ *").hide();
    //$("#main_header").hide();

    //  On session change recall the listCourses
    $("#sessionSelection").change(function () {
        listCourses();
    });

    //  On session change recall the listCourses
    $("#termSelection").change(function () {
        listCourses();
    });

    $('select.ta-list').each(function () {
        $(this).change(function () {

        });
    });

    $('#filterSelection').on('change', function () {
        var category = this.value;
        // alert(category);
        if (category == 'all') {
            $('.course-row ').each(function () {
                $(this).fadeIn();
            })
            $('#yearSelection-wrapper').hide();
            $('#progamSelection-wrapper').hide();
        } else if (category == 'program') {
            $('#yearSelection-wrapper').hide();
            // $('#filterSelection-wrapper').hide();
            $('#progamSelection-wrapper').show();
        } else if (category == 'year') {
            $('#filterSelection-wrapper').show();
            $('#progamSelection-wrapper').hide();
            $('#yearSelection-wrapper').show();
        }

    });

    $('#progamSelection').on('change', function () {
        var program = this.value;
        // alert(program);
        $('.course-row ').each(function () {
            $(this).fadeOut();
        })

        $('[class*=' + program + ']').each(function () {
            $(this).fadeIn();
        });

        $('[class*=' + program + ']').each(function () {
            $(this).fadeIn();
        });
    });

    $('#yearSelection').on('change', function () {
        $('.course-row ').each(function () {
            $(this).fadeOut();
        });
        var year = this.value;
        if (year > 0) {
            $('[class*=year' + year + ']').each(function () {
                $(this).fadeIn();
            });
        } else {
            $('.graduate').each(function () {
                $(this).fadeIn();
            });
        }
    });

    var interval = setInterval(function() {
        if ($('select.ta-list').length) {
            $("a.expand").click(function(e){
                e.preventDefault();
                $(this).siblings(".course-lab").find(".lab-info-row:not(:first-child)").slideToggle( "slow");
                 $(this).text($(this).text() == '+' ? '-' : '+');
          });
          removeAssigned();
          clearInterval(interval);
        }
    }, 100);

    var interval2 = setInterval(function() {
        $(".box ~ *").show();
        $("#main_header").show();

        $(".box ").hide();
        $(".box *").hide();
        clearInterval(interval);
        
    }, 3000)

    $("#opt-form").submit(function (e) {
        e.preventDefault();
        getOptimizationData();
    });
 
});
