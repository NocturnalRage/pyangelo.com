          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />

          <div class="form-group<?= isset($errors['title']) ? ' has-error' : ''; ?>">
            <label for="class_name" class="control-label">Class Name:</label>
            <input type="text" name="class_name" id="class_name" class="form-control" placeholder="Class name" value="<?= $this->esc($formVars['class_name'] ?? ''); ?>" maxlength="100" required autofocus />
            <?php if (isset($errors['class_name'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['class_name']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <input type="submit" class="btn btn-primary" value="<?=$this->esc($submitButtonText); ?> Class" />
          </div>
        </form>
