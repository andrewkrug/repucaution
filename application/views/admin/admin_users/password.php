<div class="span12 box">
    <div class="header span12">
        <span><?= lang('change_user_password', [$user->username]) ?></span>
    </div>
    <div class="row-fluid">
        <div class="body span12">
            <form action="<?php echo site_url('admin/admin_users/password/' . $user->id); ?>" method="POST">
                <fieldset class="formBox special">

                    <div class="control-group">
                        <label for="new_password" class="control-label"><?= lang('new_password') ?></label>
                        <div class="controls">
                            <input type="password" id="new_password" name="new_password">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="confirm_password" class="control-label"><?= lang('confirm_new_password') ?></label>
                        <div class="controls">
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                    </div>

                </fieldset>
              <fieldset class="buttBox"><input class="black-btn" type="submit" value="<?= lang('change') ?>"></fieldset>
          </form>
      </div>
    </div>
</div>
