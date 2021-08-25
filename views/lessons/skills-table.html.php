    <?php if (!empty($skills)) : ?>
      <h2><?= $this->esc($tutorial['title']); ?> Skills</h2>
      <div class="table-responsive skills-table">
        <table class="table table-striped table-hover">
          <tbody>
            <?php foreach($skills as $skill) : ?>
              <tr>
                <td>
                    <?= $skill['skill_name']; ?>
                </td>
                <td class="text-right">
                    <?= $skill['mastery_level_desc']; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div><!-- table-responsive -->

      <div class="row text-center">
        <div class="col-md-12 add-bottom">
            <a href="/tutorials/<?= $this->esc($tutorial['slug']); ?>/quizzes/create" class="btn btn-lg btn-primary"
              onclick="event.preventDefault();
              document.getElementById('create-quiz-form').submit();">
              <i class="fa fa-check" aria-hidden="true"></i> <strong>Take the <?= $this->esc($tutorial['title']); ?> Tutorial Quiz</strong>
            </a>
            <form id="create-quiz-form" action="/tutorials/<?= $this->esc($tutorial['slug']); ?>/quizzes/create" method="POST" style="display: none;">
              <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken'] ?>" />
            </form>
        </div>

    <?php endif; ?>
