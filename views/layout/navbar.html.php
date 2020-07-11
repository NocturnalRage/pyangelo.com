  <nav class="navbar navbar-default navbar-static-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand navbar-logo" href="/">
          <img class="pyangelo-logo" src="/images/logos/pyangelo-logo.png" alt="PyAngelo">
        </a>
      </div>
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav">
          <li <?= $activeLink == 'My Sketches' ? 'class="active"' : ''; ?> ><a href="/sketch">My Sketches</a></li>
          <li class="dropdown<?= $activeLink == 'Tutorials' ? ' active' : '' ?>">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
              Tutorials <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <li>
                <a href="/categories/introduction-to-pyangelo"><i class="fa fa-handshake-o fa-fw"></i> Introduction to PyAngelo</a>
                <a href="/tutorials"><i class="fa fa-th fa-fw"></i> All</a>
              </li>
            </ul>
          </li>
          <li <?= $activeLink == 'Blog' ? 'class="active"' : ''; ?> ><a href="/blog">Blog</a></li>

          <li class="dropdown<?= $activeLink == 'Ask the Teacher' ? ' active' : '' ?>">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
              Ask the Teacher <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <li>
                <a href="/ask-the-teacher"><i class="fa fa-list-alt fa-fw"></i> Latest Questions</a>
                <a href="/ask-the-teacher/my-questions"><i class="fa fa-user-circle fa-fw"></i> My Questions</a>
                <a href="/ask-the-teacher/favourite-questions"><i class="fa fa-star fa-fw"></i> Favourite Questions</a>
                <a href="/ask-the-teacher/ask"><i class="fa fa-question-circle fa-fw"></i> Ask a Question</a>
              </li>
            </ul>
          </li>
        </ul>
        <!-- Right Side Of Navbar -->
        <ul class="nav navbar-nav navbar-right">
          <!-- Authentication Links -->
          <?php if (! $personInfo['loggedIn']) : ?>
            <li><a href="/login">Login</a></li>
            <li><a href="/register">Register</a></li>
          <?php else : ?>
            <li>
              <a href="/latest-comments">
                <i class="fa fa-comment" aria-hidden="true"></i>
              </a>
            </li>
            <li>
              <a href="/notifications">
                <i class="fa fa-bell" aria-hidden="true"></i>
                  <?php if ($personInfo['unreadNotificationCount']) : ?>
                    <span id="notification-badge" class="badge badge-nav">
                      <?= $personInfo['unreadNotificationCount'] ?>
                    </span>
                  <?php endif; ?>
              </a>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                <?= $this->esc($personInfo['details']['given_name']); ?> <span class="caret"></span>
              </a>

              <ul class="dropdown-menu" role="menu">
                <li><a href="/profile"><i class="fa fa-user fa-fw"></i> Profile</a></li>
                <li><a href="/favourites"><i class="fa fa-star fa-fw"></i> Favourites</a></li>
                <li>
                  <a href="/logout"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out fa-fw"></i> Logout
                  </a>

                  <form id="logout-form" action="/logout" method="POST" style="display: none;">
                    <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken'] ?>" />
                  </form>
                </li>
                <?php if ($personInfo['isAdmin']) : ?>
                  <li role="separator" class="divider"></li>
                  <li><a href="/admin"><i class="fa fa-lock fa-fw"></i> Admin</a></li>
                <?php endif; ?>
                <?php if ($personInfo['isImpersonating']) : ?>
                  <li role="separator" class="divider"></li>
                  <li><a href="/admin/stop-impersonating"><i class="fa fa-user fa-fw"></i> Stop Impersonating</a></li>
                <?php endif; ?>
              </ul>
            </li>
          <?php endif; ?>
        </ul>
      </div><!--/.nav-collapse -->
    </div><!--container -->
  </nav>
