<?php include 'header.php';?>

<link rel="stylesheet" href="./css/form.css" />
<link rel="stylesheet" href="./css/table.css" />
<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/admin-review-schedule-controller.js"></script>

<div class="wrapper">
    <div>
        <button id="confirmBtn">Confirm</button><button id="cancleBtn">Cancel</button>
    </div>
    <div>
        <table id="reviewTable">
            <tbody>
                <th>Section</th><th>TA</th><th>labHours</th><th>markingHours</th><th>otherHours</th><th>other2Hours</th>
            </tbody>
        </table>
    </div>
</div>