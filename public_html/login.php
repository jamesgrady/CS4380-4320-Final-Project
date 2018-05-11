<?php
    require_once("fragments/header.php");
?>

<?php

    // if logged in, redirect to dashboard page (index.php) //
    
    if(isset($_SESSION['pawprint'])) {
        echo '<script> window.location.assign("https://web.dsa.missouri.edu/~s18group03/index.php"); </script>';
    }

    // input handling so no special characters can screw up the database //

    //$pawprintInput = htmlspecialchars($_POST['pawprint'], ENT_DISALLOWED);
    //$passwordInput = htmlspecialchars($_POST['password'], ENT_DISALLOWED);

    //$hashedPasswordInput = password_hash($passwordInput, PASSWORD_BCRYPT);
    // echo $hashedPasswordInput;
   // echo "<p> $hashedPasswordInput </p>";

?>

<div class="container">

  <div class="row">
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4>Member Login</h4>
        </div>
        <div class="panel-body">
          <form action="" method="POST">
            <div class="form-group">
              <label for="pawprint">Pawprint:</label>
              <input class="form-control" name="pawprint" type="text" />
            </div>
            <div class="form-group">
              <label for="password">Password:</label>
              <input class="form-control" name="password" type="password" />
            </div>
            <div class="form-group">
              <input class="btn btn-primary pull-right" name="submit" type="submit" value="Log In" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php

    if(isset($_POST['submit'])) {

        // input handling so no special characters can screw up the database //

        $pawprintInput = (isset($_POST['pawprint']) ? $_POST['pawprint'] : null);
        //$pawprintInput = htmlspecialchars($pawprintInput, ENT_DISALLOWED);

        $passwordInput = (isset($_POST['password']) ? $_POST['password'] : null);
        //$passwordInput = htmlspecialchars($passwordInput, ENT_DISALLOWED);

        /* IMPORTANT IMPORTANT IMPORTANT IMPORTANT IMPORTANT IMPORTANT IMPORTANT
        //////////////////////////////////////////////////////////////////////////////////////
        Password hashing function used is part of the PHP Password Hashing API
        within PHP Core. Hashing algorithm option used is Blowfish (PASSWORD_BCRYPT)
        for additional security over PASSWORD_DEFAULT or SHA1. Login credentials entered
        into the database must be hashed using Blowfish for login functionity to work
        - James Grady 04/17/18
        /////////////////////////////////////////////////////////////////////////////////////
        IMPORTANT IMPORTANT IMPORTANT IMPORTANT IMPORTANT IMPORTANT IMPORTANT */

        if($db == FALSE) {        
            echo '<script> window.alert("Error. Could not connect to database") </script>';
        }

        // get hash and compare it to password input //

        $result = pg_prepare($db, "", 'SELECT salted_password FROM member WHERE pawprint=$1');
        $result = pg_execute($db, "", array($pawprintInput));
        if($result == false) {
            echo '<script> window.alert("Error. Query failed.")';
        }
        $row = pg_fetch_row($result);
        $salted_password = $row[0];
        if(password_verify($passwordInput, $salted_password)) {
            
            $result = pg_prepare($db, "", 'SELECT * FROM member WHERE pawprint=$1');
            $result = pg_execute($db, "", array($pawprintInput));
            $row = pg_fetch_row($result);
            $student_id = $row[0];
            $result = pg_prepare($db, "", "SELECT position_id FROM chairPosition C, member M WHERE C.student_id=M.student_id AND M.student_id=$1");
            $result = pg_execute($db, "", array($student_id));
            if(pg_num_rows($result) > 1) {
                echo '<script> window.alert("Error. Query returned chair positions greater than 1") </script>';
            }
            else {

                // case where member has no officer position //

                if(pg_num_rows($result) == 0) {
                    $position_id = 0;
                }

                // case where member is an officer

                else {
                    $row = pg_fetch_row($result);
                    $position_id = $row[0];
                }

                // set session variables and redirect webpage to dashboard using javascript //

                $_SESSION['pawprint'] = $pawprintInput;
                $_SESSION['student_id'] = $student_id;
                $_SESSION['position_id'] = $position_id;
                echo '<script> window.location.assign("https://web.dsa.missouri.edu/~s18group03/index.php") </script>';
            }

            
        }
        
        else {
            echo '<script> window.alert("Error. Login Failed.") </script>';
        }
    }
?>

<?php
    require_once("fragments/footer.php");
?>
