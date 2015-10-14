<div class="span12 box">
    <div class="header span12">
        <span><?=lang('user_profile') ?></span>
    </div>
    <div style="padding-left:20px">
        <a href="<?php echo site_url('admin/admin_users');?>"><?= lang('back_to_list') ?></a>
    </div>
    <div class="row-fluid">
        <div class="body span12">
            <form action="<?php echo site_url('settings/personal'); ?>" method="POST">
                <fieldset class="formBox special">
                    <div class="control-group">
                        <label for="email" class="control-label keyword"><?= lang('email') ?></label>
                        <div class="controls">
                            <?php echo $email; ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="first_name" class="control-label keyword"><?= lang('first_name') ?></label>
                        <div class="controls">
                            <?php echo $firstName; ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="last_name" class="control-label keyword"><?= lang('last_name') ?></label>
                        <div class="controls">
                            <?php echo $lastName; ?>

                        </div>
                    </div>
                    <div class="control-group">
                        <label for="cerated" class="control-label keyword"><?= lang('created_on') ?></label>
                        <div class="controls">
                            <?php echo $created; ?>

                        </div>
                    </div>
                    <div class="control-group">
                        <label for="last_login" class="control-label keyword"><?= lang('last_login') ?></label>
                        <div class="controls">
                            <?php echo $lastLogin; ?>
                        </div>
                    </div>
                </fieldset>
          </form>
      </div>
    </div>
</div>
