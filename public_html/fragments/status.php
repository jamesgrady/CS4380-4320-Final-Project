<!-- begin fragments/status.php -->

<?php 
    
    $success = array();
    $info = array();
    $error = array();
    
    $success = (isset($_SESSION['success']) ? $_SESSION['success'] : null);
    $info = (isset($_SESSION['info']) ? $_SESSION['info'] : null);
    $error = (isset($_SESSION['error']) ? $_SESSION['error'] : null);
    
    foreach ($success as $msg) { 
        echo '<div class="alert alert-success">';
        echo '<strong>Success!</strong>'; 
        echo $msg;
        echo '</div>';
    }    
    $success = [];

    foreach ($info as $msg) { 
        echo '<div class="alert alert-info">';
        echo '<strong>Note:</strong>';
        echo $msg; 
        echo '</div>';
    } 
    $info = []; 

    foreach ($error as $msg) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong>'; 
        echo $msg;
        echo '</div>';
    }
    $error = [];
  
?>

<!-- end fragments/status.php -->
