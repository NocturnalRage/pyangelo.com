          <h1>Payment History</h1>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Type</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach($payments as $payment) {
                    include __DIR__ . '/payment.html.php';
                  }
                ?>
              </tbody>
            </table>
          </div>
