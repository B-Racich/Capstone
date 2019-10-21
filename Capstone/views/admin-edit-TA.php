
<?php include 'header.php';?>
<link rel="stylesheet" href="./css/form.css" />
<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/admin-edit-ta-controller.js"></script>
<div class="wrapper">
  <div class="align-center">
    <div class="main">

      <div class="form-wrapper">

        <div class="form-title">
          <h1>Admin Edit TA</h1>
        </div>

        <div class="form-body">
          <form method="post" action="" id="admin-edit-ta">
            <div class="row">
                <div class="flex-item col-12">
                   <!-- <label for="username">Username:</label><br> -->
                    <input type="text" class="createacc" name="inputusername" id="inputusername" placeholder="Username" required="required" maxlength="20"/>
                </div>
            </div>

            <input type="text" class="createacc" name="uid" id="uid" hidden="true" maxlength="50"/>

            <div class="row">
                <div class="flex-item col-12">
                    <label for="username">Before Username</label><br>
                    <input type="text" class="createacc" name="beforeusername" id="beforeusername" placeholder="" disabled="true" maxlength="50"/>
                </div>
            </div>
            <div class="row">
                <div class="flex-item col-12">
                    <label for="username">Updated Username</label><br>
                    <input type="text" class="createacc" name="username" id="username" placeholder=""  maxlength="50"/>
                </div>
            </div>
            <div class="row">
                <div class="flex-item col-12">
                    <label for="username">Before First Name</label><br>
                    <input type="text" class="createacc" name="beforefirstname" id="beforefirstname" placeholder="" disabled="true" maxlength="50"/>
                </div>
            </div>
            <div class="row">
                <div class="flex-item col-12">
                    <label for="username">Updated First Name</label><br>
                    <input type="text" class="createacc" name="firstname" id="firstname" placeholder="" maxlength="50"/>
                </div>
            </div>
            <div class="row">
                <div class="flex-item col-12">
                    <label for="username">Before Last Name</label><br>
                    <input type="text" class="createacc" name="beforelastname" id="beforelastname" placeholder="" disabled="true" maxlength="50"/>
                </div>
            </div>
            <div class="row">
                <div class="flex-item col-12">
                    <label for="username">Updated Last Name</label><br>
                    <input type="text" class="createacc" name="lastname" id="lastname" placeholder="" maxlength="50"/>
                </div>
            </div>
            <div class="row">
                <div class="flex-item col-12">
                    <label for="username">Before Email</label><br>
                    <input type="text" class="createacc" name="beforeemail" id="beforeemail" placeholder="" disabled="true" maxlength="50"/>
                </div>
            </div>
            <div class="row">
                <div class="flex-item col-12">
                    <label for="username">Updated Email</label><br>
                    <input type="text" class="createacc" name="email" id="email" placeholder="" maxlength="50"/>
                </div>
            </div>
            <div class="row">
                <div class="flex-item col-12">
                    <label for="username">Before Graduate</label><br>
                    <input type="text" class="createacc" name="beforegraduate" id="beforegraduate" placeholder="" disabled="true" maxlength="50"/>
                </div>
            </div>
            <div class="row">
                <div class="flex-item col-12">
                    <label for="username">Updated Graduate</label><br>
                    <input type="text" class="createacc" name="graduate" id="graduate" placeholder="" maxlength="50"/>
                </div>
            </div>
              <br>
              <button id="submit" type="submit" class="button">Retrieve Account Info</button>
              <button id="updateTA" type="button" class="button">Update TA Info</button>
              <div class="extra">
                <p id="createacc"> Already have an account? <a href="ta-login.php">Login</a> </p>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
