
<?php include 'header.php';?>
<link rel="stylesheet" href="./css/form.css" />
<script type="text/javascript" src="../controllers/create-account-controller.js"></script>

<div class="wrapper">
  <div class="align-center">
    <div class="main">

      <div class="form-wrapper">

        <div class="form-title">
          <h1>Create TA Account</h1>
        </div>

        <div class="form-body">
          <form method="post" action="../model/create-accountProcess.php" id="create-account">
            <div class="row">
              <div class="flex-item col-12">
                <input type="text" class="createacc" name="fname" id="fname" placeholder="First Name" required="required" />
              </div>
            </div>

            <div class="row">
              <div class="flex-item col-12">
                <input type="text" class="createacc" name="lname" id="lname" placeholder="Last Name" required="required" />
              </div>
            </div>
             <div class="row">
                <div class="flex-item col-12">
                   <!-- <label for="username">Username:</label><br> -->
                    <input type="text" class="createacc" name="username" id="username" placeholder="Username" required="required" maxlength="20"/>
                </div>
              </div>
              <div class="row">
                <div class="flex-item col-12">
                   <!-- <label for="email">Email:</label><br> -->
                  <input type="email" class="createacc" name="email" id="email" placeholder="Email" required="required" maxlength="50"/>
                </div>
              </div>
         <div class="row">
                <div class="flex-item col-12">
             <input type="number" class="create" name="sid" id="sid" placeholder="Student Number" required="required" maxlength="8" pattern="[0-9]{8}"/><br>
           </div>
         </div>



            <div class="row">
                <div class="flex-item col-12">
                              <input type="password" class="createacc" name="password" id="password" placeholder="Password" required="required" />
                </div>


            </div>


            <div class="row">
              <div class="flex-item col-12">
               <input type="password" class="createacc" name="confirm-pass" id="confirm-pass" placeholder="Confirm Password" required="required" />
             </div>
            </div>



              <br>
              <button id="submit" type="submit" class="button">Create</button>

              <div class="extra">
                <p id="createacc"> Already have an account? <a href="login.php">Login</a> </p>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
