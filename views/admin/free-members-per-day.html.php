        <hr />
        <h2>Free Members Per Day</h2>
        <div id="freeMembersPerDay"></div>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
          google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Day', 'Count'],
              <?php
                $first = 'y';
                foreach ($membersDaily as $day) {
                  if ($first != 'y') {
                    echo ",";
                  }
                  else {
                    $first = 'n';
                  }
                  echo "['" . $day['created_at'] . "', " .  $day['count'] . "]\n";
                }
              ?>
            ]);
            var options = {
              title: 'Daily Count',
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('freeMembersPerDay'));

            chart.draw(data, options);
          }
        </script>
