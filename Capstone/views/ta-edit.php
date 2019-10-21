<?php include_once 'header.php';  ?>
<link rel="stylesheet" type="text/css" href="css/form.css">
<link rel="stylesheet" type="text/css" href="css/ta-edit.css">

<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/account-edit-controller.js"></script>

<div class="col">

    <div id="nav" class="row flex-center">
        <ul>
            <a id="changeName" class="button button-primary">Change Name</a>
            <!-- <a href="" id="changeID" class="button button-primary">Change Student ID</a> -->
            <a id="changeEmail" class="button button-primary">Change Email</a>
            <a id="changePassword" class="button button-primary">Change Password</a>
        </ul>
    </div>

    
        <div id="forms" class="">
           <div class="form-wrapper">
               <div class="form-title">
                    <h2 id="formTitle" class="flex"></h2>
                </div>
                <div id="formContent" class="form-body">
                    
                        <div id="changeEmailDiv" class="col-12">
                            <form method="post" id="changeEmailForm" action="">
                                <div class="row vert-margin">
                                    <div class="col side-margin">
                                        <label>Current Email: </label>
                                    </div>
                                    <div class="col side-margin">
                                        <label id="curEmail"></label>
                                    </div>
                                </div>
    
                                <div class="row vert-margin">
                                    <div class="col side-margin">
                                        <label for="newEmail">New Email: </label>
                                    </div>
                                    <div class="col side-margin">
                                        <input type="text" name="newEmail" id="newEmail" required="required"/>
                                    </div>
                                </div>
    
                                <div class="row-center">
                                    <button type="submit" id="emailSubmit">Submit</button>
                                </div>
                            </form>
                        </div>
           </div>

                <div id="changeNameDiv" class="row-center hidden">
                    <form method="post" id="changeNameForm" action="">
                        <div class="row vert-margin">
                            <div class="col side-margin">
                                <label>Current First Name: </label>
                            </div>
                            <div class ="col side-margin">
                                <label id="curFirstName"></label>
                            </div>
                        </div>

                        <div class="row vert-margin">
                            <div class="col side-margin">
                                <label>New First Name: </label>
                            </div>
                            <div class="col side-margin">
                                <input type="text" name="newFirstName" id="newFirstName"/>
                            </div>
                        </div>

                        <div class="row vert-margin">
                            <div class="col side-margin">
                                <label>Current Last Name: </label>
                            </div>
                            <div class="col side-margin">
                                <label id="curLastName"></label>
                            </div>
                        </div>

                        <div class="row vert-margin">
                            <div class="col side-margin">
                                <label>New Last Name: </label>
                            </div>
                            <div class="col side-margin">
                                <input type="text" name="newLastName" id="newLastName"/>
                            </div>
                        </div>

                        <div class="row-center">
                            <button type="submit" id="nameSubmit">Submit</button>
                        </div>
                    </form>
                </div>

                <div id="changePasswordDiv" class="hidden">
                    <form method="post" id="changePasswordForm" action="">
                        <div class="row vert-margin">
                            <div class="col side-margin">
                                <label for="curPass">Current Password:</label>
                            </div>
                            <div class="col side-margin">
                                <input type="password" name="curPass" id="curPass" required="required"/>
                            </div>
                        </div>

                        <div class="row vert-margin">
                            <div class="col side-margin">
                                <label for="newPass">New Password:</label>
                            </div>
                            <div class="col side-margin">
                                <input type="password" name="newPass" id="newPass" required="required"/>
                            </div>
                        </div>

                        <div class="row vert-margin">
                            <div class="col side-margin">
                                <label for="retypePass">Retype New Password:</label>
                            </div>
                            <div class="col side-margin">
                                <input type="password" name="retypePass" id="retypePass" required="required"/>
                            </div>
                        </div>

                        <div class="row-center">
                            <button type="submit" id="passwordSubmit">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

</div>