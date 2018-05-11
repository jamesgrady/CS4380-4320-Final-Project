<?php
    require_once("fragments/header.php");
?>

<div class="container">
    
    <div class="row">
    
    <div class="col-md-4">
    </div>
    
    <div class="col-md-4">
        
      <div class="panel panel-default">
          
        <div class="panel-heading">
            
          <h4>Register a New Member</h4>
            
        </div>
          
        <div class="panel-body">
            
          <form action="" method="POST">
            
            <div class="form-group">
              <label for="first_name">First Name:</label>
              <input class="form-control" name="first_name" type="text" maxlength="45" required/>
            </div>
              
            <div class="form-group">
              <label for="last_name">Last Name:</label>
              <input class="form-control" name="last_name" type="text"  maxlength="45" required/>
            </div>
              
            <div class="form-group">
              <label for="pawprint">Pawprint:</label>
              <input class="form-control" name="pawprint" type="text" maxlength="20" required/>
            </div>
              
            <div class="form-group">
              <label for="student_id">Student ID:</label>
              <input class="form-control" name="student_id" type="number" max="99999999" min="00000000" step="1" required/>
            </div>
              
            <div class="form-group">
              <label for="password1">Password:</label>
              <input id="password1" class="form-control" name="password1" type="password" maxlength="25" required/>
            </div>
              
            <div class="form-group">
              <label for="password2">Verify Password:</label>
              <input id="password2" class="form-control" name="password2" type="password" maxlength="25" required onkeyup="check_passwords();"/>
              <span id="password_match_message"></span>
            </div>
              
            <div class="form-group">
              <label for="birthday">Birthday:</label>
              <input class="form-control" name="birthday" type="date" required/>
            </div>
              
            <div class="form-group">
              <label for="status">Member Status:</label>
              <select name="status">
                  <option value=1 selected>Active</option>
                  <option value=2>Locally Inactive</option>
                  <option value=3>Nationally Inactive</option>
                  <option value=4>Alumni</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="chapter_book_number">Chapter Book Number:</label>
              <input class="form-control" name="chapter_book_number" type="number" min="0" max="100000" step="1" required/>
            </div>
              
            <div class="form-group">
              <input class="btn btn-primary pull-right" name="submit" type="submit" value="Register" />
            </div>
              
          </form>
            
        </div>
          
      </div>
        
    </div>
        
  </div>
    
</div>


<?php 

    if(isset($_POST['submit'])) {
        
        
        $pawprint = (isset($_POST['pawprint']) ? $_POST['pawprint'] : null);
        $student_id = (isset($_POST['student_id']) ? $_POST['student_id'] : null);
        $first_name = (isset($_POST['first_name']) ? $_POST['first_name'] : null);
        $last_name = (isset($_POST['last_name']) ? $_POST['last_name'] : null);
        $member_status = (isset($_POST['status']) ? $_POST['status'] : null);
        $chapter_book_number = (isset($_POST['chapter_book_number']) ? $_POST['chapter_book_number'] : null);
        $password1 = (isset($_POST['password1']) ? $_POST['password1'] : null);
        $password2 = (isset($_POST['password2']) ? $_POST['password2'] : null);
        $birthday = (isset($_POST['birthday']) ? $_POST['birthday'] : null);
        $birthday = strtotime($birthday);
        $birthday = date('Y-m-d H:i:s', $birthday);
        $error = 0;

        if($password1 != $password2) {
            echo '<script> window.alert("Passwords do not match!") </script>';
        }

        else {
            
            if($db == FALSE) {        
                echo '<script> window.alert("Error. Could not connect to database") </script>';
                $error = 1;
            }

            $hashed_password = password_hash($password1, PASSWORD_BCRYPT);

            $result1 = pg_prepare($db, "", 'SELECT * FROM member WHERE student_id = $1');
            $result1 = pg_execute($db, "", array($student_id));
            $result2 = pg_prepare($db, "", 'SELECT * FROM member WHERE pawprint = $1');
            $result2 = pg_execute($db, "", array($pawprint));

            if(pg_num_rows($result1) != 0) {
                echo '<script> window.alert("Error. Student ID already taken.") </script>';
                $error = 1;
            }

            if(pg_num_rows($result2) != 0) {
                echo '<script> window.alert("Error. Pawprint already taken.") </script>';
                $error = 1;
            }    

            if($error == 0) {

                $result = pg_prepare($db, "", 'INSERT INTO member VALUES ($1, $2, $3, $4, $5, $6, $7, $8)');

                $result = pg_execute($db, "", array($student_id, $pawprint, $hashed_password, $member_status, $last_name, $first_name, $birthday, $chapter_book_number));
                
                if($result == false) {
                    echo '<script> window.alert("Error. Registration failed.") </script>';
                }
                else {
                    echo '<script> window.alert("New member record created.") </script>';    
                }
            }
        }
    }
?>


<?php
    require_once("fragments/footer.php");
?>
