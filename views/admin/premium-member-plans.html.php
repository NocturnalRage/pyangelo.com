        <hr />
        <h2>Premium Members By Plan</h2>
        <div id="premiumMemberPlans"></div>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
          google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Plan', 'Count'],
              <?php
                $first = 'y';
                foreach ($plans as $plan) {
                  if ($first != 'y') {
                    echo ",";
                  }
                  else {
                    $first = 'n';
                  }
                  echo "['" . $plan['display_plan_name'] . "', " .  $plan['count'] . "]\n";
                }
              ?>
            ]);
            var options = {
              title: 'Count',
            };

            var chart = new google.visualization.PieChart(document.getElementById('premiumMemberPlans'));

            chart.draw(data, options);
          }
        </script>
