<?php
// vim: set expandtab sts=2 sw=2 ts=2 tw=0:
require_once("lib/database.php");
require_once("fragments/header.php");

$query = "SELECT 
    M.student_id,
    M.pawprint,
    M.first_name,
    M.last_name,
    M.birthday,
    M.chapter_book_number,
    MS.status_name,
    MA.street_address,
    MA.city,
    MA.state,
    MA.zip_code,
    MC.email_address,
    MC.phone_number
FROM 
    member_status MS,
    member M
LEFT JOIN member_address MA ON M.student_id = MA.student_id
LEFT JOIN member_contact MC ON M.student_id = MC.student_id
WHERE 
    M.student_id = ".$_SESSION['student_id']." AND
    M.status = MS.status_id";
$memberInfoResults = pg_query($query);
$memberInfoArr = pg_fetch_array($memberInfoResults, NULL, PGSQL_ASSOC);

?>
<div class="container">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4>Edit Your Info</h4>
    </div>
    <div class="panel-body">
      <form action="index.php" method="POST">
          <div class="form-group">
            <label for="streetAddress">Street Address:</label>
            <input class="form-control" name="streetAddress" type="text" value="<?php echo $memberInfoArr["street_address"]?>" />

            <label for="city">City:</label>
            <input class="form-control" name="city" type="text" value="<?php echo $memberInfoArr["city"]?>" />

            <label for="state">State:</label>
            <input class="form-control" name="state" type="text" value="<?php echo $memberInfoArr["state"]?>" />

            <label for="zipCode">Zip Code:</label>
            <input class="form-control" name="zipCode" type="text" value="<?php echo $memberInfoArr["zip_code"]?>" />

            <label for="emailAddress">Email Address:</label>
            <input class="form-control" name="emailAddress" type="text" value="<?php echo $memberInfoArr["email_address"]?>" />

            <label for="phoneNumber">Phone Number:</label>
            <input class="form-control" name="phoneNumber" type="text" value="<?php echo $memberInfoArr["phone_number"]?>" />

            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" name="submit" class="btn btn-primary" >Save changes</button>
          </div>
       </form>
    </div>
  </div>
</div>
<?php require_once("fragments/footer.php"); ?>