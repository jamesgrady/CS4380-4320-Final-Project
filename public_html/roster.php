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
                    <h4>Roster Selection</h4>
                </div>
        
                <div class="panel-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="active" value="1" checked>
                                <label class="form-check-label" for="active">Active</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="locally_inactive" value="2">
                                <label class="form-check-label" for="locally_inactive">Locally Inactive</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="nationally_inactive" value="3">
                                <label class="form-check-label" for="nationally_inactive">Nationally Inactive</label>
                            </div>
                            
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="alumni" value="4">
                                <label class="form-check-label" for="alumni">Alumni</label>
                            </div>
                            
                        </div>
                        
                        <div class="form-group">
                            <input class="btn btn-primary pull-right" name="submit" type="submit" value="Display Roster" />
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
    <div class="container-fluid">
        
        <div class="panel-body">
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Student Number</th>
                <th scope="col">Pawprint</th>
                <th scope="col">First Name</th>
                <th scope="col">Last Name</th>
                <th scope="col">Birthday</th>
                <th scope="col">Chapter Book Number</th>
            </tr>
        </thead>
        <tbody>
            
            <?php 
            if(isset($_POST['submit'])) {
                $status = (isset($_POST['type']) ? $_POST['type'] : null);
                
                
                if($db == FALSE) {        
                    echo '<script> window.alert("Error. Could not connect to database") </script>';
                    $error = 1;
                }
                
                $result = pg_prepare($db, "", "SELECT student_id, pawprint, first_name, last_name, birthday, chapter_book_number FROM member WHERE status=$1");
                $result = pg_execute($db, "", array($status));
                $num_rows = pg_num_rows($result); 
                for($counter = 0; $counter < $num_rows; $counter++) {
                    
                    $student_number = pg_fetch_result($result, $counter, 0);
                    $pawprint = pg_fetch_result($result, $counter, 1);
                    $first_name = pg_fetch_result($result, $counter, 2);
                    $last_name = pg_fetch_result($result, $counter, 3);
                    $birthday = pg_fetch_result($result, $counter, 4);
                    $chapter_book_number = pg_fetch_result($result, $counter, 5);
                    
                    echo '<tr>';
                    echo '<th scope="row">';
                    echo $student_number;
                    echo '</th>';
                    
                    echo '<td>'; 
                    echo $pawprint;
                    echo '</td>';
                    
                    echo '<td>'; 
                    echo $first_name;
                    echo '</td>';
                    
                    echo '<td>'; 
                    echo $last_name;
                    echo '</td>';
                    
                    echo '<td>'; 
                    echo $birthday;
                    echo '</td>';
                    
                    echo '<td>'; 
                    echo $chapter_book_number;
                    echo '</td>';
                
                }
                
            }
    
            ?>
            
        </tbody>
    </table>
    </div>
    </div> 
    </div>
</div>

<?php
    require_once("fragments/footer.php");
?>
