          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
          <div class="form-group<?= isset($errors['title']) ? ' has-error' : ''; ?>">
            <label for="title" class="control-label">Title:</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Title" value="<?= $this->esc($formVars['title'] ?? ''); ?>" maxlength="100" required autofocus />
            <?php if (isset($errors['title'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['title']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['description']) ? ' has-error' : ''; ?>">
            <label for="description" class="control-label">Description:</label>
            <textarea name="description" maxlength="1000" form="tutorialForm" id="description" class="form-control" placeholder="Enter the description..." rows="8" /><?= $formVars['description'] ?? '' ?></textarea>
            <?php if (isset($errors['description'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['description']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['tutorial_category_id']) ? ' has-error' : ''; ?>">
            <label for="tutorial_category_id" class="control-label">Category:</label>
            <select id="tutorial_category_id" name="tutorial_category_id" class="form-control">
            <?php foreach ($categories as $category): ?>
              <option <?php if ($category['tutorial_category_id'] == ($formVars['tutorial_category_id'] ?? '')) echo 'selected'; ?> value="<?= $this->esc($category['tutorial_category_id']); ?>"><?= $this->esc($category['category']); ?></option>
            <?php endforeach; ?>
            </select>
            <?php if (isset($errors['tutorial_category_id'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['tutorial_category_id']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['tutorial_level_id']) ? ' has-error' : ''; ?>">
            <label for="tutorial_level_id" class="control-label">Level:</label>
            <select id="tutorial_level_id" name="tutorial_level_id" class="form-control">
            <?php foreach ($levels as $level): ?>
              <option <?php if ($level['tutorial_level_id'] == ($formVars['tutorial_level_id'] ?? '')) echo 'selected'; ?> value="<?= $this->esc($level['tutorial_level_id']); ?>"><?= $this->esc($level['description']); ?></option>
            <?php endforeach; ?>
            </select>
            <?php if (isset($errors['tutorial_level_id'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['tutorial_level_id']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['display_order']) ? ' has-error' : ''; ?>">
            <label for="display_order" class="control-label">Display order: </label>
            <input type="number" min="1" max="999" name="display_order" id="display_order" class="form-control" placeholder="Display order" value="<?= $this->esc($formVars['display_order'] ?? ''); ?>" maxlength="3" required />
            <?php if (isset($errors['display_order'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['display_order']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['thumbnail']) ? ' has-error' : ''; ?>">
            <?php if (isset($tutorial['thumbnail'])) : ?>
            <img src="/uploads/images/tutorials/<?= $this->esc($tutorial['thumbnail']); ?>" class="img-responsive featuredThumbnail" />
            <?php endif; ?>
            <label for="thumbnail" class="control-label">Tutorial image (.jpg, or .png, and less than 1MB in size):</label>
            <input type="file"
                   name="thumbnail"
                   id="thumbnail"
                   class="form-control"
                   <?php if (!isset($tutorial['thumbnail'])) echo 'required'; ?>
            />
            <?php if (isset($errors['thumbnail'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['thumbnail']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['pdf']) ? ' has-error' : ''; ?>">
            <label for="pdf" class="control-label">PDF Document:</label>
            <input type="file" name="pdf" id="pdf" class="form-control" />
            <?php if (isset($errors['pdf'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['pdf']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group">
            <input type="submit" class="btn btn-primary" value="<?=$this->esc($submitButtonText); ?> Tutorial" />
          </div>
        </form>
