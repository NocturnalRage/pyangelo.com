        <hr />
        <h2>Members Per Country</h2>
        <div id="memberCountries"></div>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
          google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Country', 'Count'],
              <?php
                $first = 'y';
                foreach ($memberCountries as $country) {
                  if ($first != 'y') {
                    echo ",";
                  }
                  else {
                    $first = 'n';
                  }
                  echo "['" . htmlspecialchars($country['country_name'], ENT_QUOTES, 'UTF-8') . "', " .  $country['count'] . "]\n";
                }
              ?>
            ]);
            var options = {
              title: 'Members by Country',
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('memberCountries'));

            chart.draw(data, options);
          }
        </script>
