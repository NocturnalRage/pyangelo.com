        <hr />
        <h2>Free Members Per Month</h2>
        <div id="freeMembersPerMonth"></div>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
          google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Month', 'Count'],
              <?php
                $first = 'y';
                foreach ($membersMonthly as $month) {
                  if ($first != 'y') {
                    echo ",";
                  }
                  else {
                    $first = 'n';
                  }
                  echo "['" . $month['month'] . "', " .  $month['count'] . "]\n";
                }
              ?>
            ]);
            var options = {
              title: 'Monthly Count',
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('freeMembersPerMonth'));

            chart.draw(data, options);
          }
        </script>
