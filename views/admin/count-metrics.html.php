        <hr />
        <h2>Key Statistics</h2>
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Total Members</th>
              <th>Monthly Members</th>
              <th>Yearly Members</th>
              <th>Past Due</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($keyMetrics as $keyMetric) : ?>
              <tr>
                <td><?= $keyMetric['total_members'] ?></td>
                <td><?= $keyMetric['premium_members'] ?></td>
                <td><?= $keyMetric['past_due'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
