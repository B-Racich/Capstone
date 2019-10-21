<link rel="stylesheet" href="css/home.css" />
<?php include 'header.php';?>

<div class="wrapper">
  <div class="align-center">
    <h1>Admin Portal</h1>
    <div class="allbuttons">

      <div class="dropdown">
        <a href="" class="button">View</a>
        <div class="dropdown-content">
          <a href="admin-ta-list.php">TA List</a>
          <a href="admin-course-list.php">Course List</a>
          <a href="admin-view-applications.php">Applications</a>
        </div>
      </div>

      <a href="admin-upload-courses.php" class="button">Upload Class Schedule</a>

      <div class="dropdown">
        <a href="" class="button">Edit</a>
        <div class="dropdown-content">
            <a href="admin-create-admin.php">Create Admin</a>
            <a href="admin-edit-TA.php">Edit TA Account</a>
            <a href="admin-add-course-section.php">Add/Remove Course Section</a>
        </div>
      </div>

    </div>
  </div>
</div>
