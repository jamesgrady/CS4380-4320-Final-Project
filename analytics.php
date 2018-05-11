<?php
// vim: set expandtab sts=2 sw=2 ts=2 tw=0:
require_once("lib/database.php");
require_once("fragments/header.php");

$query = "SELECT
    roundNum,
    TRUNC(AVG(yes), 2) AS avg_yes,
    TRUNC(AVG(no), 2) AS avg_no,
    TRUNC(AVG(abstain), 2) AS avg_abstain
FROM 
    votingResults VR
GROUP BY
    VR.roundNum";
$voteResults = pg_query($query);
$voteNumRows = pg_num_rows($voteResults);
//$voteArr = pg_fetch_array($voteResults, NULL, PGSQL_ASSOC);


$query = "SELECT
    M.item_name,
    M.price,
    M.semester,
    M.year,
    count
FROM
    merch M
JOIN (
    SELECT
        MO.merch_id,
        COUNT(*)
    FROM
        merch_order MO,
        merch M
    WHERE
        M.merch_id = MO.merch_id
    GROUP BY
        MO.merch_id
    ORDER BY
        COUNT(*) DESC
) AS SUB ON SUB.merch_id = M.merch_id
LIMIT(5)";
$merchResults = pg_query($query);
$merchNumRows = pg_num_rows($merchResults);
//$merchArr = pg_fetch_array($merchResults, NULL, PGSQL_ASSOC);



$query = "SELECT
    P.name,
    COUNT(*) AS count
FROM 
    events E,
    point_type P
WHERE
    E.point_type = P.type_id
GROUP BY
    E.point_type, P.name
ORDER BY
    /*COUNT(*) DESC*/
    P.name";
$eventResults = pg_query($query);
$eventNumRows = pg_num_rows($eventResults);
//$eventArr = pg_fetch_array($eventResults, NULL, PGSQL_ASSOC);


$query = "SELECT
    MS.status_name,
    COUNT(M.status) AS count 
FROM 
    member M,
    member_status MS
WHERE
    MS.status_id = M.status
GROUP BY
    M.status, MS.status_name
ORDER BY
    MS.status_name";
$memberResults = pg_query($query);
$memberNumRows = pg_num_rows($memberResults);
//$memberArr = pg_fetch_array($memberResults, NULL, PGSQL_ASSOC);


$query = "SELECT
    B.budget_item_id,
    B.item_name,
    B.budget_amount,
    (B.budget_amount / T.total) AS percentage,
    T.total
FROM 
    budget AS B,
    (SELECT 
         SUM(budget_amount) AS total
     FROM 
         budget 
     WHERE 
         year = 2017 AND /* Input field */
         semester = 'Fall' AND /* Input field */
         item_type = 1
    ) AS T
WHERE
    /*B.year = date_part('year',current_date) AND*/
    /* Year and Semester should be selectable on the front end side */
    B.year = 2017 AND /* Input field */
    B.semester = 'Fall' AND /* Input field */
    B.item_type = 1";
$revenueResults = pg_query($query);
$revenuenumRows = pg_num_rows($revenueResults);
//$revenueArr = pg_fetch_array($revenueResults, NULL, PGSQL_ASSOC);

$query = "SELECT
    B.budget_item_id,
    B.item_name,
    B.budget_amount,
    (B.budget_amount / T.total) AS percentage,
    T.total
FROM 
    budget AS B,
    (SELECT 
         SUM(budget_amount) AS total
     FROM 
         budget 
     WHERE 
         year = 2017 AND /* Input field */
         semester = 'Fall' AND /* Input field */
         item_type = 0
    ) AS T
WHERE
    /*B.year = date_part('year',current_date) AND*/
    /* Year and Semester should be selectable on the front end side */
    B.year = 2017 AND /* Input field */
    B.semester = 'Fall' AND /* Input field */
    B.item_type = 0";
$expensesResults = pg_query($query);
$expensesnumRows = pg_num_rows($expensesResults);
//$expensesArr = pg_fetch_array($expensesResults, NULL, PGSQL_ASSOC);
?>
  
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="container">
  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
              <h4>Expenses</h4>
            </div>
        <div class="panel-body">
          <div id="piechartExpenses"></div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
              <h4>Revenue</h4>
            </div>
        <div class="panel-body">
          <div id="piechartRevenue"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
              <h4>Members</h4>
            </div>
        <div class="panel-body">
          <div id="chart_div"></div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
              <h4>Events</h4>
            </div>
        <div class="panel-body">
          <div id="chart_divEvents"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
              <h4>Top Selling Merch</h4>
            </div>
        <div class="panel-body">
          <div id="chart_divMerch"></div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
              <h4>Voting</h4>
            </div>
        <div class="panel-body">
          <div id="barchart_material"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php echo '<script type="text/javascript">
// Load google charts
google.charts.load("current", {"packages":["corechart"]});
google.charts.setOnLoadCallback(drawChart);

// Draw the chart and set the chart values
function drawChart() {
  var data = google.visualization.arrayToDataTable([
  ["Expenses","expense per type"],';
  
  for ($x = 0; $x < $expensesnumRows; $x++) {
    $expensesArr = pg_fetch_array($expensesResults, NULL, PGSQL_ASSOC);
    if ($expensesArr != NULL){
      echo "['";
      echo $expensesArr["item_name"];
      echo "',";
      echo $expensesArr["budget_amount"];
      if($x == $expensesnumRows - 1){
        echo "]";
      }else{
        echo "],"; 
      }
    }
  } 
echo ']);

  // Optional; add a title and set the width and height of the chart
  var options = {"title":"", "width":540, "height":400};

  // Display the chart inside the <div> element with id="piechart"
  var chart = new google.visualization.PieChart(document.getElementById("piechartExpenses"));
  chart.draw(data, options);
}
</script>
'; ?>


<?php echo '<script type="text/javascript">
// Load google charts
google.charts.load("current", {"packages":["corechart"]});
google.charts.setOnLoadCallback(drawChartRevenue);

// Draw the chart and set the chart values
function drawChartRevenue() {
  var data = google.visualization.arrayToDataTable([
  ["Expenses","expense per type"],';
  
  for ($x = 0; $x < $revenuenumRows; $x++) {
    $revenueArr = pg_fetch_array($revenueResults, NULL, PGSQL_ASSOC);
    if ($revenueArr != NULL){
      echo "['";
      echo $revenueArr["item_name"];
      echo "',";
      echo $revenueArr["budget_amount"];
      if($x == $revenuenumRows - 1){
        echo "]";
      }else{
        echo "],"; 
      }
    }
  } 
echo ']);

  // Optional; add a title and set the width and height of the chart
  var options = {"title":"", "width":525, "height":400};

  // Display the chart inside the <div> element with id="piechart"
  var chart = new google.visualization.PieChart(document.getElementById("piechartRevenue"));
  chart.draw(data, options);
}
</script>
'; ?>

<?php echo '<script type="text/javascript">
google.charts.load("current", {packages: ["corechart", "bar"]});
google.charts.setOnLoadCallback(drawBarColors);
function drawBarColors() {

      var data = google.visualization.arrayToDataTable([';
        echo '["City", "Members"],';
        for ($x = 0; $x < $memberNumRows; $x++) {
          $memberArr = pg_fetch_array($memberResults, NULL, PGSQL_ASSOC);
          if ($memberArr != NULL){
            echo "['";
            echo $memberArr["status_name"];
            echo "',";
            echo $memberArr["count"];
            if($x == $memberNumRows - 1){
              echo "]";
            }else{
              echo "],"; 
            }
          }
        } 
      echo ']);

      var options = {
        title: "",
        chartArea: {width: "50%"},
        hAxis: {
          title: "Total Members",
          minValue: 0
        },
        vAxis: {
          title: "Member Type"
        }
      };

      var chart = new google.visualization.BarChart(document.getElementById("chart_div"));

      chart.draw(data, options);
    }
</script>
'; ?>


<?php echo '<script type="text/javascript">
google.charts.load("current", {packages: ["corechart", "bar"]});
google.charts.setOnLoadCallback(drawBarColorsEvents);
function drawBarColorsEvents() {

      var data = google.visualization.arrayToDataTable([';
        echo '["City", "Events"],';
        for ($x = 0; $x < $eventNumRows; $x++) {
          $eventArr = pg_fetch_array($eventResults, NULL, PGSQL_ASSOC);
          if ($eventArr != NULL){
            echo "['";
            echo $eventArr["name"];
            echo "',";
            echo $eventArr["count"];
            if($x == $eventNumRows - 1){
              echo "]";
            }else{
              echo "],"; 
            }
          }
        } 
      echo ']);

      var options = {
        title: "",
        chartArea: {width: "88%"},
        hAxis: {
          title: "Event Type",
          minValue: 0
        },
        vAxis: {
          title: "Total Events"
        }
      };

      var chart = new google.visualization.ColumnChart(document.getElementById("chart_divEvents"));

      chart.draw(data, options);
    }
</script>
'; ?>


<?php echo '<script type="text/javascript">
google.charts.load("current", {packages: ["corechart", "bar"]});
google.charts.setOnLoadCallback(drawBarColorsMerch);
function drawBarColorsMerch() {

      var data = google.visualization.arrayToDataTable([';
        echo '["City", "Events"],';
        for ($x = 0; $x < $merchNumRows; $x++) {
          $merchArr = pg_fetch_array($merchResults, NULL, PGSQL_ASSOC);
          if ($merchArr != NULL){
            echo "['";
            echo $merchArr["item_name"];
            echo "',";
            echo $merchArr["count"];
            if($x == $merchNumRows - 1){
              echo "]";
            }else{
              echo "],"; 
            }
          }
        } 
      echo ']);

      var options = {
        title: "",
        chartArea: {width: "88%"},
        hAxis: {
          title: "Merch Type",
          minValue: 0
        },
        vAxis: {
          title: "Total Orders"
        }
      };

      var chart = new google.visualization.ColumnChart(document.getElementById("chart_divMerch"));

      chart.draw(data, options);
    }
</script>
'; ?>

<?php echo '<script type="text/javascript">
      google.charts.load("current", {"packages":["bar"]});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([';
        echo '["Rounds", "Yes", "No", "Obstain"],';


          for ($x = 0; $x < $voteNumRows; $x++) {
            $voteArr = pg_fetch_array($voteResults, NULL, PGSQL_ASSOC);
            if ($voteArr != NULL){
              echo "['";
              echo "Round " . $x;
              echo "',";
              echo $voteArr["avg_yes"];
              echo ",";
              echo $voteArr["avg_no"];
              echo ",";
              echo $voteArr["avg_abstain"];
              if($x == $voteNumRows - 1){
                echo "]";
              }else{
                echo "],"; 
              }
            }
          } 


        echo ']);

        var options = {
          chart: {
            title: "",
            subtitle: "",
          },
          bars: "horizontal" // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById("barchart_material"));

        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>
'; ?>
<?php require_once("fragments/footer.php"); ?>