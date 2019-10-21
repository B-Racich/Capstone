
<?php include 'header.php';?>
<link rel="stylesheet" href="./css/form.css" />
<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/admin-create-admin-controller.js"></script>

<div class="wrapper">
  <div class="align-center">
    <div class="main">

      <div class="form-wrapper">

        <div class="form-title">
          <h1>Create Admin Account</h1>
        </div>

        <div class="form-body">
          <form method="post" action="" id="create-admin">
            
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
                  <!-- <label for="password">Password:</label><br> -->
                                <input type="password" class="createacc" name="password" id="password" placeholder="&#xF002; password" required="required" />
                </div>
                        
             
            </div>


            <div class="row">
              <div class="flex-item col-12">
                <label for="confirm-pass">Confirm Password:</label><br>
               <input type="password" class="createacc" name="confirm-pass" id="confirm-pass" placeholder="********" required="required" />
             </div>
            </div>
          
        <div class="row">
              <div class="flex-item col-12">  <label for="fname">First Name:</label><br>
            <input type="text" class="createacc" name="firstName" id="firstName" placeholder="John" required="required" />
              </div>
               
        </div>

        <div class="row">
              <div class="flex-item col-12">
              <label for="lname">Last Name:</label><br>
                     <input type="text" class="createacc" name="lastName" id="lastName" placeholder="Doe" required="required" />
                   </div>
        </div>
          
              <br>
              <button id="submit" type="submit" class="button">Create</button>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
