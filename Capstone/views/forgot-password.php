<?php include 'header.php';?>
<link rel="stylesheet" href="./css/form.css" />
<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/forgot-password-controller.js"></script>

  <div class="wrapper">
  <div class="main login_wrapper align-center">
    <div class="form-wrapper">
      <div class="form-title">
        <h1>Forgot Password</h1>
      </div>
     <div class="form-body">
        <form method="post" id="forgotPassForm" action="">

          <div class="row">
          <div class="flex-item col-8">
             <label for="username">Username:</label>
             <input type="text" class="forgotpass" name="username" id="username" required="required"/>
          </div>
           </div>

         <div class="row">
         <div class="flex-item col-8">
            <label for="email">Email:</label>
            <input type="email" class="forgotpass" name="email" id="email" required="required"/>
         </div>
          </div>

         <button id="submit" type="submit" class="button">Reset Password</button>

         <div class="extra">
           <p id="login"> <a href="login.php">Return to Login</a> </p>
         </div>
       </form>
     </div>

    </div>
  </div>
</div>
