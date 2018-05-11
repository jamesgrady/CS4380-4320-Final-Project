

<?php
// vim: set expandtab sts=2 sw=2 ts=2 tw=0:
require_once("lib/database.php");
//require_once("lib/user.php");
require_once("fragments/header.php");

if (isset($_POST['submit'])) {
  $street = $_POST['streetAddress'];
  $city = $_POST['city'];
  $state = $_POST['state'];
  $zipCode = $_POST['zipCode'];
  $email = $_POST['emailAddress'];
  $phoneNumber = $_POST['phoneNumber'];
  
  $query = "UPDATE member_address SET street_address ='" . $street . "', city ='" . $city ."', state ='" . $state ."', zip_code ='" . $zipCode ."' WHERE student_id = ".$_SESSION['student_id']."" ;
  pg_query($query);
  
  $query = "UPDATE member_contact SET email_address ='" . $email . "', phone_number ='" . $phoneNumber . "' WHERE student_id = 1" ;
  pg_query($query);
}

//QUERY FOR MEMBER INFO "/*.$_SESSION['student_id'].*/"
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

//QUERY FOR MEMBER DUES
$query = "SELECT 
            MD.amount_owed,
            MD.amount_paid,
            MD.payment_method,
            MD.notes
          FROM 
            member M
              LEFT JOIN memberDues MD ON M.student_id = MD.student_id
          WHERE
              M.student_id = ".$_SESSION['student_id']."";
$memberDuesResults = pg_query($query);
$memberDuesArr = pg_fetch_array($memberDuesResults, NULL, PGSQL_ASSOC);

//QUERY FOR MEMBER EMERGENCY CONTACT INFO
$query = "SELECT 
    EC.last_name,
    EC.first_name,
    EC.email_address,
    EC.phone_number,
    EC.backup_phone_number,
    EC.street_address,
    EC.city,
    EC.state,
    EC.zip_code,
    EC.primary_healthcare_provider,
    EC.blood_type,
    EC.medical_conditions,
    EC.medical_conditions,
    EC.medications,
    EC.allergies
FROM
    member M
    LEFT JOIN emergencyContact EC ON M.student_id = EC.student_id
WHERE
    M.student_id = ".$_SESSION['student_id']."";
$memberEmergContactInfoResults = pg_query($query);
$memberEmergContactInfoArr = pg_fetch_array($memberEmergContactInfoResults, NULL, PGSQL_ASSOC);
  
//QUERY FOR MEMBER PAYMENT PLAN
$query = "SELECT 
    DPP.last_amount_paid,
    DPP.last_payment_date,
    DPP.next_amount_due,
    DPP.next_payment_date
FROM 
    member M,
    memberDues MD
    LEFT JOIN dues_paymentPlan DPP ON MD.dues_id = DPP.dues_id
WHERE
    M.student_id = ".$_SESSION['student_id']." AND
    M.student_id = MD.student_id";
$memberPaymentPlanResults = pg_query($query);
$memberPaymentPlanArr = pg_fetch_array($memberPaymentPlanResults, NULL, PGSQL_ASSOC);

//QUERY FOR MEMBER POINTS
$query = "SELECT 
    PT.name AS point_type,
    SUM(E.num_of_points) AS num_of_points,
    COUNT(*) AS num_of_events_attended
FROM
    member M,
    events E,
    event_attendance EA,
    point_type PT
WHERE
    M.student_id = ".$_SESSION['student_id']." AND
    M.student_id = EA.student_id AND
    EA.event_id = E.event_id AND
    E.point_type = PT.type_id
GROUP BY
    E.point_type, PT.name";
$memberPointsResults = pg_query($query);
//$memberPointsArr = pg_fetch_array($memberPointsResults, NULL, PGSQL_ASSOC);

//QUERY FOR MERCH ORDERS
$query = "SELECT 
    MCH.item_name,
    MCH.price,
    MO.quantity,
    (MCH.price * MO.quantity) AS total_amount_due,
    MO.payment_due_date,
    MO.delivery_date
FROM 
    member M
    LEFT JOIN merch_order MO ON M.student_id = MO.student_id
    LEFT JOIN merch MCH ON MO.merch_id = MCH.merch_id
WHERE 
    M.student_id = ".$_SESSION['student_id']." AND 
    MO.completed = false";
$memberMerchOrdersResults = pg_query($query);
//$memberMerchOrdersArr = pg_fetch_array($memberMerchOrdersResults, NULL, PGSQL_ASSOC);

//QUERY FOR MEMBER TEAMS
$query = "SELECT 
    RS.sport_name,
    RST.team_name,
    RST.day_of_week,
    RS.semester,
    RS.start_date,
    RS.end_date,
    current_date
FROM
    member M,
    team_roster TR,
    recSports_teams RST,
    recSports RS
WHERE
    M.student_id = ".$_SESSION['student_id']." AND
    M.student_id = TR.student_id AND
    TR.team_id = RST.team_id AND 
    RST.sport_id = RS.sport_id AND
    RS.end_date >= current_date";
$memberTeamResults = pg_query($query);
//$memberTeamArr = pg_fetch_array($memberTeamResults, NULL, PGSQL_ASSOC);

?>
<head>
  <title>Dashboard</title>  
</head>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4>Member Info</h4>
        </div>
        <div class="panel-body">
         <?php if(isset($memberInfoArr)) { ?>          
            <ul class="list-group list-group-flush">
              <li class="list-group-item"><strong>First Name:</strong> <?php echo $memberInfoArr["first_name"]?></li>
              <li class="list-group-item"><strong>Last name:</strong> <?php echo $memberInfoArr["last_name"]?></li>
              <li class="list-group-item"><strong>Student ID:</strong> <?php echo $memberInfoArr["student_id"]?></li>
              <li class="list-group-item"><strong>PawPrint:</strong> <?php echo $memberInfoArr["pawprint"]?></li>
              <li class="list-group-item"><strong>Birthday:</strong> <?php echo $memberInfoArr["birthday"]?></li>
              <li class="list-group-item"><strong>Chapter Book Number:</strong> <?php echo $memberInfoArr["chapter_book_number"]?></li>
              <li class="list-group-item"><strong>Status:</strong> <?php echo $memberInfoArr["status_name"]?></li>
              <li class="list-group-item"><strong>Street Address:</strong> <?php echo $memberInfoArr["street_address"]?></li>
              <li class="list-group-item"><strong>City:</strong> <?php echo $memberInfoArr["city"]?></li>
              <li class="list-group-item"><strong>State:</strong> <?php echo $memberInfoArr["state"]?></li>
              <li class="list-group-item"><strong>Zip Code:</strong> <?php echo $memberInfoArr["zip_code"]?></li>
              <li class="list-group-item"><strong>Email Address:</strong> <?php echo $memberInfoArr["email_address"]?></li>
              <li class="list-group-item"><strong>Phone Number:</strong> <?php echo $memberInfoArr["phone_number"]?></li>
            </ul>     
         <?php } ?>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4>Member Dues</h4>
        </div>
        <div class="panel-body">
          <?php if(isset($memberDuesResults)) { ?>       
            <div class="list-group list-group-flush">
              <h5 class="mb-1"><u>Total Dues</u></h5>
                <p> <strong>Amount Owed:</strong> $<?php echo $memberDuesArr["amount_owed"]?></p>
                <p> <strong>Amount Payed:</strong> $<?php echo $memberDuesArr["amount_paid"]?></p>
              <h5 class="mb-1"><u>Transactions</u></h5>
                <p> <strong>Last Payment:</strong><?php if(isset($memberPaymentPlanArr["last_amount_paid"])){echo $memberPaymentPlanArr["last_amount_paid"]; echo " Payed On " . $memberPaymentPlanArr["last_payment_date"];}else{echo " No Last Payment";}?></p>
                <p> <strong>Next Payment:</strong> $<?php echo $memberPaymentPlanArr["next_amount_due"]; echo '<font color="red">   DUE: </font>' . $memberPaymentPlanArr["next_payment_date"];?></p>
            </div>
         <?php } ?>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4>Member Points</h4>
        </div>
        <div class="panel-body">
          <?php while($memberPointsArr = pg_fetch_array($memberPointsResults, NULL, PGSQL_ASSOC)) {    
          ?>          
                <p><?php echo "<font size='4'><strong>".$memberPointsArr["point_type"] ."</strong></font><p> You have attended ". $memberPointsArr["num_of_events_attended"] ." events with a total of ". $memberPointsArr["num_of_points"] . " points</p>";?></p>                
         <?php } ?>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4>Member Merch Orders</h4>
            </div>
            <div class="panel-body">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Item</th>
                    <th scope="col">Cost</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Amount Due</th>
                    <th scope="col">Paymenmt due date</th>
                    <th scope="col">Delivery Date</th>
                  </tr>
                </thead>
                <tbody>
                <?php while($memberMerchOrdersArr = pg_fetch_array($memberMerchOrdersResults, NULL, PGSQL_ASSOC)) 
                  {       
                    echo "<tr>";
                      echo "<td>". $memberMerchOrdersArr["item_name"] ."</td>";
                      echo "<td>$". $memberMerchOrdersArr["price"] ."</td>";
                      echo "<td>". $memberMerchOrdersArr["quantity"] ."</td>";
                      echo "<td>". $memberMerchOrdersArr["total_amount_due"] ."</td>";
                      echo "<td>". $memberMerchOrdersArr["payment_due_date"] ."</td>";
                      echo "<td>". $memberMerchOrdersArr["delivery_date"] ."</td>";
                    echo "</tr>";
                  } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4>Member Teams</h4>
            </div>
            <div class="panel-body">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Sport</th>
                    <th scope="col">Team Name</th>
                    <th scope="col">Game Day</th>
                    <th scope="col">Semester</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">End Date</th>
                    <th scope="col">Date</th>
                  </tr>
                </thead>
                <tbody>
                <?php while($memberTeamArr = pg_fetch_array($memberTeamResults, NULL, PGSQL_ASSOC)) 
                  {       
                    echo "<tr>";
                      echo "<td>". $memberTeamArr["sport_name"] ."</td>";
                      echo "<td>". $memberTeamArr["team_name"] ."</td>";
                      echo "<td>". $memberTeamArr["day_of_week"] ."</td>";
                      echo "<td>". $memberTeamArr["semester"] ."</td>";
                      echo "<td>". $memberTeamArr["start_date"] ."</td>";
                      echo "<td>". $memberMerchOrdersArr["end_date"] ."</td>";
                      echo "<td>". $memberTeamArr["date"] ."</td>";
                    echo "</tr>";
                  } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading">
                 <h4>Member Emergency Info</h4>
              </div>
              <div class="panel-body">
              <?php if(isset($memberEmergContactInfoArr)) { ?>          
                <div class="col-md-6">
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>First Name:</strong> <?php echo $memberEmergContactInfoArr["first_name"]?></li>
                    <li class="list-group-item"><strong>Last name:</strong> <?php echo $memberEmergContactInfoArr["last_name"]?></li>
                    <li class="list-group-item"><strong>Email Address:</strong> <?php echo $memberEmergContactInfoArr["email_address"]?></li>
                    <li class="list-group-item"><strong>Phone Number:</strong> <?php echo $memberEmergContactInfoArr["phone_number"]?></li>
                    <li class="list-group-item"><strong>Street Address:</strong> <?php echo $memberEmergContactInfoArr["street_address"]?></li>
                    <li class="list-group-item"><strong>City:</strong> <?php echo $memberEmergContactInfoArr["city"]?></li>
                  </ul>
                  </div>
                  <div class="col-md-6">
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>State:</strong> <?php echo $memberEmergContactInfoArr["state"]?></li>
                    <li class="list-group-item"><strong>Zip Code:</strong> <?php echo $memberEmergContactInfoArr["zip_code"]?></li>
                    <li class="list-group-item"><strong>Medications:</strong> <?php echo $memberEmergContactInfoArr["medications"]?></li>
                    <li class="list-group-item"><strong>Allergies:</strong> <?php echo $memberEmergContactInfoArr["allergies"]?></li>
                    <li class="list-group-item"><strong>Medical Conditions:</strong> <?php echo $memberEmergContactInfoArr["medical_conditions"]?></li>
                    <li class="list-group-item"><strong>Blood Type:</strong> <?php echo $memberEmergContactInfoArr["blood_type"]?></li>
                    <li class="list-group-item"><strong>Primary Healthcare Provider:</strong> <?php echo $memberEmergContactInfoArr["primary_healthcare_provider"]?></li>
                  </ul>
                </div>
              <?php }else{
                echo "No emergency contact info found";
              } ?>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once("fragments/footer.php"); ?>

