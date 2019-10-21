
<?php include 'header.php';?>
<link rel="stylesheet" href="./css/form.css" />
<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/login-controller.js"></script>

  <div class="wrapper">
  <div class="main login_wrapper align-center">
    <div class="form-wrapper">
      <div class="form-title">
        <h1>Login</h1>
      </div>
     <div class="form-body">
        <form method="post" id="loginForm" action="">


         <div class="row">
         <div class="flex-item col-8">
            <label for="username">Username:</label>
            <input type="text" class="login" name="username" id="username" placeholder="Username123" required="required" maxlength="20"/>
         </div>
          </div>

         <div class="row">
         <div class="flex-item col-8">
            <label for="password">Password:</label>
            <input type="password" class="login" name="password" id="password" placeholder="********" required="required" />
         </div>
                 </div>

         <button id="submit" type="submit" class="button" >Login</button>

         <div class="extra">
           <p id="createacc"> Don't have an account? <a href="create-account.php">Create one</a> </p>
           <p id="forgotpass"> <a href="forgot-password.php">Forgot My Password</a> </p>
         </div>
       </form>
     </div>

    </div>
  </div>
</div>
