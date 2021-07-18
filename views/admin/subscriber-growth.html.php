        <hr />
        <h2>Paying Member Growth</h2>
        <div id="subscriberGrowth"></div>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
          google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Month', 'Subscribed', 'Cancelled', 'Net'],
              <?php
                $first = 'y';
                foreach ($subscriberGrowth as $month) {
                  if ($first != 'y') {
                    echo ",";
                  }
                  else {
                    $first = 'n';
                  }
                  echo "['" . $month['startmonth'] . "', " .  $month['subscribed'] . ", " . $month['cancelled'] . ", " . $month['net'] . "]\n";
                }
              ?>
            ]);
            var options = {
              title: 'Members Subscribed/Cancelled per Month',
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('subscriberGrowth'));

            chart.draw(data, options);
          }
        </script>
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Month</th>
              <th>Subscribed</th>
              <th>Cancelled</th>
              <th>Net</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($subscriberGrowth as $month) : ?>
              <tr>
                <td><?= $month['startmonth'] ?></td>
                <td><?= $month['subscribed'] ?></td>
                <td><?= $month['cancelled'] ?></td>
                <td><?= $month['net'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
