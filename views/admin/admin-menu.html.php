      <div class="col-md-3">
        <div class="well">
          <h3>Administration</h3>
          <div class="list-group">
            <a href="/admin" class="list-group-item<?= $activeLink == 'metrics' ? ' active' : '' ?>"><i class="fa fa-bar-chart fa-fw"></i> Metrics</a>
            <a href="/admin/users" class="list-group-item<?= $activeLink == 'users' ? ' active' : '' ?>"><i class="fa fa-users fa-fw"></i> Users</a>
            <a href="/admin/premium-users" class="list-group-item<?= $activeLink == 'premium-users' ? ' active' : '' ?>"><i class="fa fa-user-secret fa-fw"></i> Premium Members</a>
          </div><!-- list-group -->
        </div><!-- well -->
        <div class="well">
          <h3>Emails</h3>
          <div class="list-group">
            <a href="/admin/campaigns" class="list-group-item<?= $activeLink == 'campaigns' ? ' active' : '' ?>"><i class="fa fa-envelope fa-fw"></i> Campaigns</a>
            <a href="/admin/autoresponders" class="list-group-item<?= $activeLink == 'autoresponders' ? ' active' : '' ?>"><i class="fa fa-list-ol fa-fw"></i> Autoresponders</a>
            <a href="/admin/images/email" class="list-group-item<?= $activeLink == 'images' ? ' active' : '' ?>"><i class="fa fa-picture-o fa-fw"></i> Images</a>
          </div><!-- list-group -->
        </div><!-- well -->
      </div><!-- col-md-3 -->
