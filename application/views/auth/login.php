
<div class="main sign_in">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="sign_block ">
                    <form action="<?php echo site_url('auth/login'); ?>" method="POST">
                        <h2 class="sign_title text-center"><?= lang('login_heading') ?></h2>
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <a href="<?php echo site_url('auth/facebook'); ?>" class="btn btn-facebook m-b20"><?= lang('login_facebook_submit_btn') ?></a>
                                <a href="<?php echo site_url('auth/google_auth'); ?>" class="btn btn-google m-b20"><?= lang('login_google_submit_btn') ?></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <input type="text" name="identity" class="form-control" placeholder="<?= lang('login_identity_label') ?>" value="<?php echo $identity; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control" placeholder="<?= lang('login_password_label') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row custom-form">
                            <div class="col-xs-12">
                                <div class="pull-left">
                                    <label class="cb-checkbox text_color">
                                        <input name="remember" type="checkbox">
                                        <?= lang('login_remember_label') ?>
                                    </label>
                                    <a href="<?php echo site_url(); ?>auth/forgot_password" class="link"><?= lang('login_forgot_password') ?></a>
                                </div>

                                <div class="pull-right">
                                    <button class="btn-save"><?= lang('login_submit_btn') ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>