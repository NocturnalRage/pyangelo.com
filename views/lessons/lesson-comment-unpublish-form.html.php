              <form action="/lessons/comment/<?= $this->esc($comment['comment_id']); ?>/unpublish" method="post">
                <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
                <button type="submit" class="pull-right btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">
                  <i class="fa fa-times"></i>
                </button>
              </form>
