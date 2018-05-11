<?php
// vim: set expandtab sts=2 sw=2 ts=2 tw=0:
require_once("lib/database.php");
//require_once("lib/user.php");
require_once("fragments/header.php");



//QUERY FOR all MEMBER points "/*.$_SESSION['student_id'].*/"
$query = "SELECT 
    m.student_id, 
    m.last_name, 
    m.first_name, 
    PT.name,
    sub.sum AS num_of_points
FROM 
    member AS m,
    ( 
        SELECT 
            ea.student_id, 
            p.type_id, 
            SUM(e.num_of_points)
        FROM 
            events AS e, 
            event_attendance AS ea, 
            point_type AS p
        WHERE 
            e.event_id = ea.event_id AND 
            ea.point_type = p.type_id
        GROUP BY (ea.student_id, p.type_id)
    ) AS sub,
    point_type PT
WHERE 
    m.student_id = sub.student_id AND
    PT.type_id = sub.type_id
ORDER BY (M.student_id)
;";
$AllMemberPoints = pg_query($query);

//query for all chair positions
$query = "SELECT 
    M.first_name, 
    M.last_name, 
    CP.position_name
FROM 
    member M, 
    chairPosition CP
WHERE 
    M.student_id = CP.student_id
ORDER BY (CP.position_id)
;";
$AllChairPositions = pg_query($query);

//query for all member payment plans
$query = "SELECT 
    MD.student_id, 
    M.last_name, 
    M.first_name, 
    DP.last_amount_paid, 
    DP.next_amount_due, 
    DP.last_payment_date, 
    DP.next_payment_date 
FROM 
    memberDues AS MD, 
    dues_paymentPlan AS DP, 
    member AS M
WHERE 
    MD.dues_id = DP.dues_id AND 
    M.student_id = MD.student_id
;";
$PaymentPlans = pg_query($query);

//QUERY FOR MERCH ORDERS NOT YET COMPLETED
$query = "SELECT 
    M.first_name, 
    M.last_name, 
    Mch.item_name, 
    Mch.price
FROM 
    member M, 
    merch Mch, 
    merch_order MO
WHERE 
    M.student_id = MO.student_id AND 
    Mch.merch_id = MO.merch_id AND 
    MO.completed = false
;";
$IncompleteMerch = pg_query($query);


//QUERY FOR FAMILY ROSTER
$query = "SELECT 
    M.student_id,
    M.pawprint,
    M.first_name,
    M.last_name,
    F.family_id,
    F.family_name
FROM 
    member M, 
    familyTree_roster FTR,
    family F
WHERE 
    M.student_id = FTR.student_id AND 
    FTR.family_id = F.family_id
    
;";
$FamilyRoster = pg_query($query);


//QUERY FOR Number of members that attended each event
$query = "SELECT 
    E.event_id,
    P.name,
    E.title,
    SUB.count
FROM
    (
        SELECT
            EA.event_id, 
            count(EA.student_id)  
        FROM 
            event_attendance EA,
            events E
        WHERE
            E.event_id = EA.event_id
        GROUP BY EA.event_id
    ) AS SUB,
    events E,
    point_type P
WHERE
    E.event_id = SUB.event_id AND
    E.point_type = P.type_id AND
    date_part('year', E.event_date) = date_part('year', current_date) AND
    E.semester = 'Spring'
;";
$EventNumbers = pg_query($query);

//QUERY FOR Number of members that attended each event
$query = "SELECT
    CP.position_name,
    B.budget_amount,
    TE.totalexpenses,
    TR.totalrevenues,
    (B.budget_amount - (
        CASE 
            WHEN TE.totalexpenses IS NULL THEN 0
            ELSE TE.totalexpenses
        END
        +
        CASE 
            WHEN TR.totalrevenues IS NULL THEN 0
            ELSE TR.totalrevenues
        END 
    )) AS BudgetRemaining
FROM
    budget B,
    budget_register BR,
    chairPosition CP,
    (
        SELECT
            SUM(transaction_amount) AS TotalRevenues
        FROM
            budget_register BR,
            budget B
        WHERE 
            BR.transaction_type = 1 AND /* Revenues */ 
            BR.budget_item_id = B.budget_item_id AND
            date_part('year',BR.transaction_date) = date_part('year',current_date) AND
            B.semester = 'Spring' AND
            BR.chairposition = 16
    ) AS TR,
    (
        SELECT
            SUM(transaction_amount) AS TotalExpenses
        FROM
            budget_register BR,
            budget B
        WHERE 
            BR.transaction_type = 0 AND /* Expense */ 
            BR.budget_item_id = B.budget_item_id AND
            date_part('year',BR.transaction_date) = date_part('year',current_date) AND
            B.semester = 'Spring' AND
            BR.chairposition = 16
    ) AS TE
WHERE
    B.budget_item_id = BR.budget_item_id AND
    B.year = date_part('year', current_date) AND
    B.semester = 'Spring' AND
    BR.chairposition = CP.position_id AND
    BR.chairposition = 16
LIMIT(1)
;";
$ChairBudget = pg_query($query);


//QUERY FOR Class Rosters
$query = "SELECT
    CR.student_id,
    M.first_name,
    M.last_name,
    C.class_name, 
    C.semester, 
    C.year 
FROM 
    classes C,
    class_roster CR,
    member M
WHERE
    M.student_id = CR.student_id AND
    C.class_id = CR.class_id
;";
$ClassRosters = pg_query($query);


//QUERY FOR All Upcoming Events for Specific PointType or Chair
$query = "SELECT
    E.event_id,
    E.title,
    PT.name AS point_type,
    E.num_of_points,
    E.event_date
FROM
    events E,
    point_type PT
WHERE
    E.point_type = PT.type_id AND
    E.event_date >= current_date AND 
    /*E.position_id = 16*/
    E.point_type = 1
;";
$UpcomingEvents = pg_query($query);

//QUERY FOR Most Active Members 
$query = "SELECT
    M.student_id,
    M.first_name,
    M.last_name,
    MA.num_of_points
FROM
    member M,
    (
        SELECT 
            m.student_id, 
            SUM(sub.sum) AS num_of_points
        FROM 
            member AS m,
            ( 
                SELECT 
                    ea.student_id, 
                    p.type_id, 
                    SUM(e.num_of_points)
                FROM 
                    events AS e, 
                    event_attendance AS ea, 
                    point_type AS p
                WHERE 
                    e.event_id = ea.event_id AND 
                    ea.point_type = p.type_id
                GROUP BY (ea.student_id, p.type_id)
            ) AS sub,
            point_type PT
        WHERE 
            m.student_id = sub.student_id AND
            PT.type_id = sub.type_id
        GROUP BY (M.student_id)
        ORDER BY (num_of_points) DESC
        LIMIT(5)
    ) AS MA
WHERE
    MA.student_id = M.student_id
;";
$MostActive = pg_query($query);


//QUERY FOR Potential Members 
$query = "SELECT 
    PR.student_id,
    PR.pawprint,
    PR.first_name,
    PR.last_name,
    PR.email_address
FROM
    potential_roster PR
;";
$PotentialMembers = pg_query($query);

//QUERY FOR Potential Members that passed a the first round 
$query = "WITH percentage AS (
    SELECT
        VR.student_id,
        VR.roundNum,
        round((VR.yes::decimal  / (VR.yes + VR.no + VR.abstain)::decimal), 2) * 100 AS yes_percentage
    FROM
        votingResults VR
)
SELECT
    VR.roundNum,
    VR.student_id,
    M.first_name,
    M.last_name,
    P.yes_percentage
FROM
    votingResults VR,
    percentage P,
    member M
WHERE
    M.student_id = VR.student_id AND
    P.student_id = VR.student_id AND
    P.roundNum = VR.roundNum AND
    /* roundNum and yes_percentage selections should be user inputed */
    VR.roundNum = 1 AND
    P.yes_percentage >= 50
;";
$PassedRoundOne = pg_query($query);

?>


<head>
  <title>Dashboard</title>  
</head>
<div class="container-fluid">
    
    
     <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Potential Members That Passed Round 1 Voting</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                          <th scope="col">Round Num</th>
                        <th scope="col">Student ID</th>
                          <th scope="col">First Name</th>
                          <th scope="col">Last Name</th>
                        <th scope="col">Yes Percentage</th>
                
                        
                         
                        
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($PassedRoundOneArr = pg_fetch_array($PassedRoundOne, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $PassedRoundOneArr["roundnum"] ."</td>";
                          echo "<td>". $PassedRoundOneArr["student_id"] ."</td>";
                          echo "<td>". $PassedRoundOneArr["first_name"] ."</td>";
                          echo "<td>". $PassedRoundOneArr["last_name"] ."</td>";
                          echo "<td>". $PassedRoundOneArr["yes_percentage"] ."%</td>";
                          
                         
                          
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
    
    
     <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Potential Members</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Student ID</th>
                          <th scope="col">Pawprint</th>
                        <th scope="col">First Name</th>
                          <th scope="col">Last Name</th>
                        <th scope="col">Email Address</th>
                        
                         
                        
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($PotentialMembersArr = pg_fetch_array($PotentialMembers, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $PotentialMembersArr["student_id"] ."</td>";
                          echo "<td>". $PotentialMembersArr["pawprint"] ."</td>";
                          echo "<td>". $PotentialMembersArr["first_name"] ."</td>";
                          echo "<td>". $PotentialMembersArr["last_name"] ."</td>";
                          echo "<td>". $PotentialMembersArr["email_address"] ."</td>";
                         
                          
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
    
     <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Top 5 Most Active Members</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Student ID</th>
                        <th scope="col">First Name</th>
                          <th scope="col">Last Name</th>
                        <th scope="col">Num of Points</th>
                        
                         
                        
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($MostActiveArr = pg_fetch_array($MostActive, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $MostActiveArr["student_id"] ."</td>";
                          echo "<td>". $MostActiveArr["first_name"] ."</td>";
                          echo "<td>". $MostActiveArr["last_name"] ."</td>";
                          echo "<td>". $MostActiveArr["num_of_points"] ."</td>";
                         
                          
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
    
    <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Upcoming Events For a Specific Chair</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Event ID</th>
                        <th scope="col">Event Title</th>
                          <th scope="col">Point Type</th>
                        <th scope="col">Num of Points</th>
                        <th scope="col">Date</th>
                         
                        
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($UpcomingEventsArr = pg_fetch_array($UpcomingEvents, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $UpcomingEventsArr["event_id"] ."</td>";
                          echo "<td>". $UpcomingEventsArr["title"] ."</td>";
                          echo "<td>". $UpcomingEventsArr["point_type"] ."</td>";
                          echo "<td>". $UpcomingEventsArr["num_of_points"] ."</td>";
                          echo "<td>". $UpcomingEventsArr["event_date"] ."</td>";
                          
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
    
    <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Class Rosters</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Student ID</th>
                        <th scope="col">First Name</th>
                          <th scope="col">Last Name</th>
                        <th scope="col">Class Name</th>
                        <th scope="col">Semester</th>
                          <th scope="col">Year</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($ClassRostersArr = pg_fetch_array($ClassRosters, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $ClassRostersArr["student_id"] ."</td>";
                          echo "<td>". $ClassRostersArr["first_name"] ."</td>";
                          echo "<td>". $ClassRostersArr["last_name"] ."</td>";
                          echo "<td>". $ClassRostersArr["class_name"] ."</td>";
                          echo "<td>". $ClassRostersArr["semester"] ."</td>";
                          echo "<td>". $ClassRostersArr["year"] ."</td>";
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
    
    <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Budget Info For Specific Chair</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Position Name</th>
                        <th scope="col">Budget Amount</th>
                          <th scope="col">Total Expenses</th>
                        <th scope="col">Total Revenues</th>
                        <th scope="col">Budget Remaining</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($ChairBudgetArr = pg_fetch_array($ChairBudget, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $ChairBudgetArr["position_name"] ."</td>";
                          echo "<td>$". $ChairBudgetArr["budget_amount"] ."</td>";
                          echo "<td>$". $ChairBudgetArr["totalexpenses"] ."</td>";
                          echo "<td>$". $ChairBudgetArr["totalrevenues"] ."</td>";
                          echo "<td>$". $ChairBudgetArr["budgetremaining"] ."</td>";
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    

    <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Member Points</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Student ID</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Point Type</th>
                        <th scope="col">Points Acquired</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($memberPointsArr = pg_fetch_array($AllMemberPoints, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $memberPointsArr["student_id"] ."</td>";
                          echo "<td>". $memberPointsArr["last_name"] ."</td>";
                          echo "<td>". $memberPointsArr["first_name"] ."</td>";
                          echo "<td>". $memberPointsArr["name"] ."</td>";
                          echo "<td>". $memberPointsArr["num_of_points"] ."</td>";
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
    
      <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Chair Positions</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Position Name</th>
                       
                        
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($chairPositionsArr = pg_fetch_array($AllChairPositions, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $chairPositionsArr["first_name"] ."</td>";
                          echo "<td>". $chairPositionsArr["last_name"] ."</td>";
                          echo "<td>". $chairPositionsArr["position_name"] ."</td>";
                          
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
    <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Payment Plans</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Student ID</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">First Name</th>
                         <th scope="col">Last Amt Paid</th>
                        <th scope="col">Next Amt Due</th>
                        <th scope="col">Last Payment Date</th>
                        <th scope="col">Next Payment Date</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($PaymentPlansArr = pg_fetch_array($PaymentPlans, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $PaymentPlansArr["student_id"] ."</td>";
                           echo "<td>". $PaymentPlansArr["last_name"] ."</td>";
                          echo "<td>". $PaymentPlansArr["first_name"] ."</td>";
                          echo "<td>$". $PaymentPlansArr["last_amount_paid"] ."</td>";
                          echo "<td>$". $PaymentPlansArr["next_amount_due"] ."</td>";
                          echo "<td>". $PaymentPlansArr["last_payment_date"] ."</td>";
                          echo "<td>". $PaymentPlansArr["next_payment_date"] ."</td>";
                          
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
    
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Incomplete Merch Orders</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                          <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Item</th>
                        <th scope="col">Cost</th>
                     
                       
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($IncompleteMerchArr = pg_fetch_array($IncompleteMerch, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $IncompleteMerchArr["first_name"] ."</td>";
                          echo "<td>". $IncompleteMerchArr["last_name"] ."</td>";
                          echo "<td>". $IncompleteMerchArr["item_name"] ."</td>";
                          echo "<td>$". $IncompleteMerchArr["price"] ."</td>";
                          
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
     <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Family Roster</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                          <th scope="col">Student ID</th>
                        <th scope="col">PawPrint</th>
                          <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Family ID</th>
                        <th scope="col">Family Name</th>
                     
                       
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($FamilyRosterArr = pg_fetch_array($FamilyRoster, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $FamilyRosterArr["student_id"] ."</td>";
                          echo "<td>". $FamilyRosterArr["pawprint"] ."</td>";
                          echo "<td>". $FamilyRosterArr["first_name"] ."</td>";
                          echo "<td>". $FamilyRosterArr["last_name"] ."</td>";
                          echo "<td>". $FamilyRosterArr["family_id"] ."</td>";
                          echo "<td>". $FamilyRosterArr["family_name"] ."</td>";
                          
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
    
      <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4>Number of Members at Each Event</h4>
                </div>
                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                          <th scope="col">Event ID</th>
                        <th scope="col">Point Type</th>
                          <th scope="col">Event Name</th>
                        <th scope="col">Number of Members</th>
      
                       
                      </tr>
                    </thead>
                    <tbody>
                    <?php while($EventNumbersArr = pg_fetch_array($EventNumbers, NULL, PGSQL_ASSOC)) 
                      {       
                        echo "<tr>";
                          echo "<td>". $EventNumbersArr["event_id"] ."</td>";
                          echo "<td>". $EventNumbersArr["name"] ."</td>";
                          echo "<td>". $EventNumbersArr["title"] ."</td>";
                          echo "<td>". $EventNumbersArr["count"] ."</td>";
                        
                          
                        echo "</tr>";
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
    </div>
    
    
</div>
    
    
<?php require_once("fragments/footer.php"); ?>

