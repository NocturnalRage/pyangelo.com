          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />

          <div class="form-group<?= isset($errors['title']) ? ' has-error' : ''; ?>">
            <label for="title" class="control-label">Title:</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Title" value="<?= $this->esc($formVars['title'] ?? ''); ?>" maxlength="100" required autofocus />
            <?php if (isset($errors['title'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['title']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <div class="checkbox">
              <label>
              <input type="checkbox" id="featured" name="featured" value="1" <?= $this->esc(($formVars['featured'] ?? 0) == 1 ? 'checked' : '') ?> /> Featured Blog Post
              </label>
            </div>
          </div>

          <div class="form-group<?= isset($errors['blog_category_id']) ? ' has-error' : ''; ?>">
            <label for="blog_category_id" class="control-label">Category:</label>
            <select class="form-control" id="blog_category_id" name="blog_category_id">
            <?php foreach ($categories as $category): ?>
              <option 
                <?php if ($category['blog_category_id'] == ($formVars['blog_category_id'] ?? -1)) : ?>
                   <?= 'selected'; ?>
                <?php endif; ?>
                value="<?= $this->esc($category['blog_category_id']); ?>">
                <?= $this->esc($category['description']); ?>
              </option>
            <?php endforeach; ?>
            </select>
            <?php if (isset($errors['blog_category_id'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['blog_category_id']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group<?= isset($errors['content']) ? ' has-error' : ''; ?>">
            <label for="content" class="control-label">Content:</label>
            <textarea name="content" form="blogForm" id="content" class="form-control tinymce" placeholder="Enter your post..." rows="16" required >
              <?= $this->esc($formVars['content'] ?? '') ?>
            </textarea>
            <?php if (isset($errors['content'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['content']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group<?= isset($errors['preview']) ? ' has-error' : ''; ?>">
            <label for="preview" class="control-label">Preview:</label>
            <textarea name="preview" form="blogForm" id="preview" class="form-control tinymce" placeholder="Enter preview text..." rows="3" required >
              <?= $this->esc($formVars['preview'] ?? '') ?>
            </textarea>
            <?php if (isset($errors['preview'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['preview']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group<?= isset($errors['blog_image']) ? ' has-error' : ''; ?>">
            <?php if (isset($blog['blog_image'])) : ?>
            <img src="/uploads/images/blog_thumbnail/<?= $this->esc($blog['blog_image']); ?>" class="img-responsive featuredThumbnail" />
            <?php endif; ?>
            <label for="blog_image" class="control-label">Blog image (.jpg, or .png, and less than 1MB in size):</label>
            <input type="file"
                   name="blog_image"
                   id="blog_image"
                   class="form-control"
                   <?php if (!isset($blog['blog_image'])) echo 'required'; ?>
            />
            <?php if (isset($errors['blog_image'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['blog_image']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <input type="submit" class="btn btn-primary" value="<?=$this->esc($submitButtonText); ?> Blog" />
          </div>
        </form>
