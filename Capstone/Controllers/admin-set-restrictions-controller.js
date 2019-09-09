/** populates the prereq select options
 *
 */
function getPreReqs () {
    var options = "";
    var courseID;
    var subject;
    var courseNum;
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/admin_set_restrictions_model.php',	//	path to file
        data: {functionCall: 'getPreReqs', courseID: urlParam("course")},
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
            $("#courseReqs").html(options);
        },error: function(res, sts, err) {
            console.log(err);
        }
    });
}

/** set page info with the set courseID in url
 *
 */
function getCourseInfo() {
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/admin_set_restrictions_model.php',	//	path to file
        data: {functionCall: 'getCourseInfo', courseID: urlParam("course")},
        dataType: 'json',
        success: function (data) {
            console.log(data);
            var title = data[0]['subject'] + " " + data[0]['courseNum'];
            $("#course-title").text(title);
            updateSelectedCourse();
        },error: function(res, sts, err) {
            console.log(err);
        }
    });
}

/** get the restrictions for the current course
 *
 */
function updateSelectedCourse() {
    //  Update the text
    clearTable();

    //  Ajax for restrictions
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/admin_set_restrictions_model.php',	//	path to file
        data: {functionCall: 'getCourseRestrictions', courseID: urlParam("course")},
        dataType: 'json',
        success: function (data) {
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    addRow(key, data[key]);
                }
            }
        },error: function(res, sts, err) {
            console.log(err);
        }
    });
}

/** configure the inputs depending on what input type is selected
 *
 */
function inputConfiguration() {
    var restriction = $("#restrictions").find(":selected").val();
    var $inputDiv = $("#input-wrapper");
    var $input = $("#input");

    if($input.length >= 0) {
        $input.remove();
    }

    $("#updateBtn").show();
    $("#prereqsSelect").hide();
    switch(restriction) {
        case    'labHours':
        case    'markingHours':
        case    'prepHours':
        case    'otherHours':
            //  Set the input type and params for this group
            $input = "<input id='input' type='number' maxlength='3' min='0' max='100' placeholder='0'>";
            $inputDiv.append($input);
            break;
        case    'minAvgOverall':
        case    'minAvgInSubject':
        case    'UTAminGrade':
            $input = "<input id='input' type='number' maxlength='3' min='0' max='100' placeholder='85'>";
            $inputDiv.append($input);
            break;
        case    'minCredits':
            $input = "<input id='input' type='number' maxlength='3' min='0' max='100' placeholder='30'>";
            $inputDiv.append($input);
            break;
        case    'prereq':
            $("#updateBtn").hide();
            $("#prereqsSelect").show();
            break;
    }

}

/** Clear the HTML table
 *
 */
function clearTable() {
    $("#table").find("tr:gt(0)").remove();
}

/** Build an HTML row for the table
 *
 * @param restriction
 * @param value
 */
function addRow(restriction, value) {
    //   Get existing row
    var $curRow = $("#"+restriction+"Value");

    if($curRow.length) {
        $curRow.html(value);
    }
    else {
        //  Add row
        var restriction = restriction;
        var value = value;
        var element = "#" + restriction+"Row";
        var row = "<tr id='" + restriction+"Row" + "'></tr>";

        $("#table").append(row);
        //  Update text in row
        $(element).html("<td>"+restriction+"</td>"+"<td id='"+restriction+"Value"+"'>"+value+"</td>");

        var removeBtn = "remove-" + restriction;
        //  Add listener for remove button
        $("#"+removeBtn).click(function() {
            $(this).parent().remove();
        });
    }
}

/** Adds the configured restriction to the table
 *
 */
function addRestriction() {
    var restriction = $("#restrictions").find(":selected").val();
    var value = $("#input").val();

    //  check for input
    if($("#input").length && $("#input").length <= 0 || $("#input").val() == "") {
       updateStatus("Please enter a value!");
    }
    else {
        var element = "#" + restriction;

        if($("#input").attr('type') == 'radio') {
            value = $("input[name='input']:checked").val();
        }
        addRow(restriction, value);
    }
}

/** Updates the course with the current restrictions in the table
 *
 */
function updateCourseRestrictions() {
    var json = {};

    var restriction;
    var value;
    var id = urlParam("course");

    $('#table tr').each(function(i, row) {
        var curRow = $(row);
        if($(curRow).attr('id') != 'firstRow') {
            restriction = $(curRow).children().eq(0).text();
            value = $(curRow).children().eq(1).text();
            json[restriction] = value;
        }
    });
    console.log(JSON.stringify(json));
    //  Ajax for restrictions
    $.ajax({
        type: 'post',	//	Type of request GET/POST
        url:  '../model/admin_set_restrictions_model.php',	//	path to file
        data: {functionCall: 'updateCourseRestrictions', courseID: id, json: JSON.stringify(json)},
        success: function (data) {
            if(data)
                updateStatus("Course restrictions updated!");
            else
                updateStatus("No values were changed");
        },error: function(res, sts, err) {
            console.log(err);
        }
    });
}

/** Adds the selected prereq to the list
 *
 */
function addPreReq() {
    var course = $("#courseReqs").find(":selected").val();
    //   Get existing row
    var $curRow = $("#prereqValue");

    var curPreReqs = $curRow.html();

    if(!curPreReqs.includes(course) && course != $("#courses").find(":selected").val()) {
        if(curPreReqs == "") {
            var newPreReqs = course;
        }
        else {
            var newPreReqs = curPreReqs + ", "+ course;
        }
    }
    else {
        updateStatus("Can not add current course as pre-req");
    }
    $curRow.html(newPreReqs);
}

/** Removes the selected prereq from the list
 *
 */
function removePreReq() {
    var course = $("#courseReqs").find(":selected").val();
    //   Get existing row
    var $curRow = $("#prereqValue");

    var curPreReqs = $curRow.html();

    var arrStr = $curRow.html().split(", ");

    var newPreReqs;

    if(arrStr.length > 1) {
        if(arrStr[0] == course) {
            newPreReqs  = curPreReqs.replace(course+", ", "");
        }
        else
            newPreReqs = curPreReqs.replace(", "+course, "");
    }
    else {
        newPreReqs = curPreReqs.replace(course, "");
    }

    $curRow.html(newPreReqs);
}

$(document).ready(function() {
    getCourseInfo();
    getPreReqs();
    inputConfiguration();

    $("#courses").change(function() {
        updateSelectedCourse();
    });

    $("#restrictions").change(function() {
        inputConfiguration();
    });

    $("#courseReqAddBtn").click(function() {
        addPreReq();
    });
    $("#courseReqRemoveBtn").click(function() {
        removePreReq();
    });

    $('#updateBtn').click(function() {
        addRestriction();
    });

    $('#saveBtn').click(function() {
        updateCourseRestrictions();
    });

});