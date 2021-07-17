        <hr />
        <h2>Premium Members Last 12 Months</h2>
        <div id="premiumMemberCount"></div>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
          google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Month', 'Count'],
              <?php
                $first = 'y';
                foreach ($premiumMembers as $month) {
                  if ($first != 'y') {
                    echo ",";
                  }
                  else {
                    $first = 'n';
                  }
                  echo "['" . $month['month'] . "', " .  $month['premium_member_count'] . "]\n";
                }
              ?>
            ]);
            var options = {
              title: 'Count',
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('premiumMemberCount'));

            chart.draw(data, options);
          }
        </script>
