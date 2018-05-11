<?php

    session_start();
    require_once("lib/database.php");
    //require_once("lib/status.php");
    //require_once("lib/user.php"); CAUSES A CRASH
    //ini_set('display_errors',1);
    date_default_timezone_set("America/Chicago");
?>

<!DOCTYPE html>
<!-- begin fragments/header.php -->
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="css/site.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <title>Phi Sigma Pi</title>
</head>
<body>
<style>a {
    color: rgb(250, 250, 250);
    text-decoration: none;
}</style>

  
<nav class="navbar navbar-fixed-top navbar-custom">
  <div class="container-fluid">
    
    <div class="navbar-header">
        
      <button class="navbar-toggle collapsed" data-target="#navbar" data-toggle="collapse"
              type="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
        
      <a class="navbar-brand" href="index.php">Phi Sigma Pi</a>
        
    </div>
      
    <div class="collapse navbar-collapse" id="navbar">
        
      <ul class="nav navbar-nav">
          
        <li><a href="roster.php">Roster</a></li>
          <li><a href="info.php">Info</a></li>
          <li><a href="register.php">Register</a></li>
          <li><a href="analytics.php">Analytics</a></li>
        <li><a href="CreateDASEvent.php">CreateDASEvent</a></li>
        <li><a href="CreateFellowshipEvent.php">CreateFellowshipEvent</a></li>
        <li><a href="CreateFundraisingEvent.php">CreateFundraisingEvent</a></li>
        <li><a href="CreateServiceEvent.php">CreateServiceEvent</a></li>
        <li><a href="CreateCampusLiasonEvent.php">CreateCampusLiasonEvent</a></li>
      </ul>
      
      <ul class="nav navbar-nav navbar-right">
        <?php
          $role = (isset($_SESSION['role']) ? $_SESSION['role'] : null);
          if ($role == "none" || $role == NULL) {
            echo '<li><a href="login.php">Member Login</a></li>';
          } 
        ?>            
            
          
          <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- end fragments/header.php -->
