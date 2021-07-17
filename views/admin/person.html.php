          <div class="media">
            <div class="media-left">
              <img class="media-object featuredThumbnail" src="<?= $avatar->getAvatarUrl($person['email']) ?>" alt="<?= $this->esc($person['display_name']) ?>" />
            </div>
            <div class="media-body">
              <h4 class="media-heading"><?= $this->esc($person['display_name']) ?> <small><i><?= $person['premium_status_boolean'] == 1 ? 'Premium Member' : 'Free Member' ?></i></small></h4>
              <p><strong>Email: </strong><?= $this->esc($person['email']) ?></p>
              <p><strong>Country: </strong><?= $this->esc($person['country_name']) ?></p>
              <p><strong>Joined: </strong><?= $person['created_at'] ?></p>
              <?php if (isset($person['premium_start_date'])) : ?>
                <p><strong>Premium Start Date: </strong><?= $this->esc($person['premium_start_date'] ?? 'Not set') ?></p>
              <?php endif; ?>
              <?php if (isset($person['premium_end_date'])) : ?>
                <p><strong>Premium End Date: </strong><?= $this->esc($person['premium_end_date'] ?? 'Not set') ?></p>
              <?php endif; ?>
            </div>
          </div>
