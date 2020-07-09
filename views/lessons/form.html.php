          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
          <div class="form-group<?= isset($errors['lesson_title']) ? ' has-error' : ''; ?>">
            <label for="lesson_title" class="control-label">Lesson title:</label>
            <input type="text" name="lesson_title" id="lesson_title" class="form-control" placeholder="Lesson title" value="<?= $this->esc($formVars['lesson_title'] ?? ''); ?>" maxlength="100" required autofocus />
            <?php if (isset($errors['lesson_title'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['lesson_title']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['lesson_description']) ? ' has-error' : ''; ?>">
            <label for="lesson_description" class="control-label">Lesson description:</label>
            <textarea name="lesson_description" maxlength="1000" id="lesson_description" class="form-control" placeholder="Enter the description..." rows="8" /><?= $formVars['lesson_description'] ?? '' ?></textarea>
            <?php if (isset($errors['lesson_description'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['lesson_description']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['video_name']) ? ' has-error' : ''; ?>">
            <label for="video_name" class="control-label">Video name (eg. cross.mp4):</label>
            <input type="text" name="video_name" id="video_name" class="form-control" placeholder="The name of the video including .mp4" value="<?= $this->esc($formVars['video_name'] ?? ''); ?>" maxlength="100" required />
            <?php if (isset($errors['video_name'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['video_name']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['youtube_url']) ? ' has-error' : ''; ?>">
            <label for="youtube_url" class="control-label">YouTube Video ID (eg. PipFjWi1QY8):</label>
            <input type="text" name="youtube_url" id="youtube_url" class="form-control" placeholder="Enter a YouTube URL or leave blank" value="<?= $this->esc($formVars['youtube_url'] ?? ''); ?>" maxlength="255" />
            <?php if (isset($errors['youtube_url'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['youtube_url']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['seconds']) ? ' has-error' : ''; ?>">
            <label for="seconds" class="control-label">Duration in seconds: </label>
            <input type="number" min="1" max="9999" name="seconds" id="seconds" class="form-control" placeholder="Duration in seconds" value="<?= $this->esc($formVars['seconds'] ?? ''); ?>" required />
            <?php if (isset($errors['seconds'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['seconds']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['lesson_security_level_id']) ? ' has-error' : ''; ?>">
            <label for="lesson_security_level_id" class="control-label">Lesson security level:</label>
            <select id="lesson_security_level_id" name="lesson_security_level_id" class="form-control">
            <?php foreach ($securityLevels as $securityLevel): ?>
              <option <?php if ($securityLevel['lesson_security_level_id'] == ($formVars['lesson_security_level_id'] ?? '')) echo 'selected'; ?> value="<?= $this->esc($securityLevel['lesson_security_level_id']); ?>"><?= $this->esc($securityLevel['description']); ?></option>
            <?php endforeach; ?>
            </select>
            <?php if (isset($errors['lesson_security_level_id'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['lesson_security_level_id']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['display_order']) ? ' has-error' : ''; ?>">
            <label for="display_order" class="control-label">Display order: </label>
            <input type="number" min="1" max="999" name="display_order" id="display_order" class="form-control" placeholder="Display order" value="<?= $this->esc($formVars['display_order'] ?? ''); ?>" maxlength="3" required />
            <?php if (isset($errors['display_order'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['display_order']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['poster']) ? ' has-error' : ''; ?>">
            <?php if (isset($lesson['poster'])) : ?>
            <img src="/uploads/images/lessons/<?= $this->esc($lesson['poster']); ?>" class="img-responsive featuredThumbnail" />
            <?php endif; ?>
            <label for="poster" class="control-label">Lesson poster image (.jpg, or .png, and less than 1MB in size):</label>
            <input type="file" name="poster" id="poster" class="form-control" />
            <?php if (isset($errors['poster'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['poster']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group">
          <input type="submit" class="btn btn-primary" value="<?=$this->esc($submitButtonText); ?> Lesson" />
          </div>
        </form>
