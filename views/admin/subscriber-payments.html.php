        <hr />
        <h2>Subscription Payments</h2>
        <div id="subscriberPayments"></div>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
          google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Month', 'PyAngelo', 'Stripe', 'Tax'],
              <?php
                $first = 'y';
                foreach ($subscriberPayments as $month) {
                  if ($first != 'y') {
                    echo ",";
                  }
                  else {
                    $first = 'n';
                  }
                  echo "['" . $month['startmonth'] . "', " .  $month['pyangelo'] . ", " . $month['stripe'] . ", " . $month['tax'] . "]\n";
                }
              ?>
            ]);
            var options = {
              title: 'Payments per Month in AUD',
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('subscriberPayments'));

            chart.draw(data, options);
          }
        </script>
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Month</th>
              <th>PyAngelo</th>
              <th>Stripe</th>
              <th>Tax</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($subscriberPayments as $month) : ?>
              <tr>
                <td><?= $month['startmonth'] ?></td>
                <td>$<?= number_format($month['pyangelo']) ?></td>
                <td>$<?= number_format($month['stripe']) ?></td>
                <td>$<?= number_format($month['tax']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
