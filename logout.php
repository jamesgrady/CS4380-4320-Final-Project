<?php

    session_start();
    $_SESSION['pawprint'] = NULL;
    $_SESSION['position_id'] = NULL;
    echo "<script> window.location.assign('login.php') </script>";

?>