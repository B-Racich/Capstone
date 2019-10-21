<?php include 'header.php';?>
<link rel="stylesheet" href="./css/form.css" />
<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/reset-password-controller.js"></script>

  <div class="wrapper">
  <div class="main login_wrapper align-center">
    <div class="form-wrapper">
      <div class="form-title">
        <h1>RESET Password</h1>
      </div>
     <div class="form-body">
        <form method="post" id="resetPassForm" action="" class="resetpass">


         <div class="row">
         <div class="flex-item col-8">
            <label for="email">Email:</label> <br>
            <input type="email" class="resetpass" name="email" id="email" required="required"/>
         </div>
          </div>

          <div class="row">
          <div class="flex-item col-8">
             <label for="username">Username:</label> <br>
             <input type="text" class="resetpass" name="username" id="username" required="required"/>
          </div>
           </div>

          <div class="row">
          <div class="flex-item col-8">
             <label for="email">New Password:</label> <br>
             <input type="password" class="resetpass" name="pass" id="pass" required="required"/>
          </div>
           </div>

           <div class="row">
           <div class="flex-item col-8">
              <label for="email">Confirm New Password:</label>
              <input type="password" class="resetpass" name="confirmpass" id="confirmpass" required="required"/>
           </div>
            </div>

         <button id="submit" type="submit" class="button" >Reset Password</button>

         <div class="extra">
           <p id="login"> <a href="login.php">Return to Login</a> </p>
         </div>
       </form>
     </div>

    </div>
  </div>
</div>
