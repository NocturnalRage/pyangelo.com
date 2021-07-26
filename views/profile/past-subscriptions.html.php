          <h1>Past Subscriptions</h1>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Subscription</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach($pastSubscriptions as $pastSubscription) {
                    include __DIR__ . '/past-subscription.html.php';
                  }
                ?>
              </tbody>
            </table>
          </div>
