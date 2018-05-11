<?php
// vim: set expandtab sts=2 sw=2 ts=2 tw=0:
require_once("config.php");
require_once("status.php");

function check_boolean($query, $type, $data) {
  global $db;

  $stmt = $db->prepare($query);
  $stmt->bind_param($type, $data);
  if (!$stmt->execute()) {
    error("Could not query the database (checking '$data')");
    $value = false;
  } else {
    $stmt->store_result();
    $value = $stmt->num_rows == 1;
    $stmt->free_result();
  }
  $stmt->close();
  return $value;
}

$db = new mysqli("localhost", 'CS3380GRP3', '4c7d499', DATABASE);
