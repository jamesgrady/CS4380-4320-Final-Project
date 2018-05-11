<!DOCTYPE html>


<?php

require_once("fragments/header.php");
require_once("lib/database.php");
require_once("lib/config.php");
//if (!session_start()) {
//	header("Location: index.php");
//	exit;
//}
//
//$user = empty($_SESSION['userOutput']) ? false : $_SESSION['userOutput'];
//$role = empty($_SESSION['loggedInJobTitle']) ? false : $_SESSION['loggedInJobTitle'];
//$loginStat = empty($_SESSION['loggedInStatus']) ? false : $_SESSION['loggedInStatus'];
//
//if ($user == false || $role == false || $loginStat == false) {
//	header("Location: index.php");
//	exit;
//}
//
//if ($role != 15) {
//	header("Location: index.php");
//	exit;
//}

if ($position_id != 16) {
	header("Location: index.php");
	exit;
}
?>






<html lang="en">
  <head>
    <title>Add Fellowship Event</title>
  </head>

  <body>
	


       
       
<div class="container">

<?php
       // require("fragments/status.php");
            
?>

        
   
      <div class="row">
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4>Create Fellowship Event</h4>
        </div>
          
          
        <div class="panel-body">
          <form action="" method="POST">
              
            <div class="form-group">
              <label for="eventName">Event Name:</label>
              <input class="form-control" name="eventName" type="text" required />
            </div>
              
            <div class="form-group">
              <label for="pointValue">Point Value:</label>
              <input class="form-control" name="pointValue" type="number" min="1" max="10" value="1" required />
            </div>
              
              <div class="form-group">
              <label for="eventDate">Event Date:</label>
              <input class="form-control" name="eventDate" type="date" required />
            </div>
              
             
              <div>
              <label >Semester: </label>
             <input  name="semester" type="radio" value="Fall" checked/>Fall 
             <input  name="semester" type="radio" value="Spring"  /> Spring
            </div>
              
            <div class="form-group">
              <input class="btn btn-primary pull-right" name="submit" type="submit" value="Create Event" />
            </div>
              
          </form>
        </div>
      </div>
    </div>
  </div>
 </div>

   
   

<!--
    <script>
      function loadAdminDash() {
        window.location.href = "index.php";
      }
    
    </script>
-->

		<!--php code for submit-->
		<?php
     
		if (isset($_POST['submit'])) {
			
			 $nameCheck = $_POST['eventName'];
             $point = $_POST['pointValue'];
             $date = $_POST['eventDate'];
             $semester = $_POST['semester'];
             $year = DateTime::createFromFormat("Y-m-d", $date);
             $year = $year->format('Y');
//            $year = date_create($date);
//            $year = date_format($year, 'Y');
               
             $result1 = pg_prepare($db, "checkEventName", "SELECT * FROM events WHERE title = $1 AND date_part('year', event_date) = $2 AND semester = $3");
             $result1 = pg_execute($db, "checkEventName", array($nameCheck, $year, $semester));
             $resultcount = pg_numrows($result1);
          
            
            
             
             if($resultcount == 0){
                 
                 $result = pg_prepare($db, "createFellowship", "INSERT INTO events (position_id, point_type, num_of_points, title, event_date, semester)
         VALUES (16, 2, $1, $2, $3, $4)");
                 if($result){
                     $result = pg_execute($db, "createFellowship", array($point, $nameCheck, $date, $semester));
                 }
                 else{
                    echo '<script> window.alert("prepare failed") </script>';
                 }
                 
                    
//                $result = pg_execute($db, "createDAS", array($point, "$nameCheck", $date, $semester));
                
             }
            
            else{
                 echo '<script> window.alert("Event name already exists for this semester & year, please enter a new name") </script>';
                //Error: event already exists
            }
        }
			
		 
		?>
      
      
<?php
    require_once("fragments/footer.php");
?>
  </body>
</html>
