<div class="main sign_in">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="sign_block " style="width: 35%;margin: 10% auto;padding: 40px;">
                    <?php $url = ($inviteCode) ? 'auth/register/'.$planId.'/'.$inviteCode : 'auth/register/'.$planId;?>
                    <form id="register-form" action="<?php echo site_url($url); ?>" method="POST">
                        <h2 class="sign_title text-center"><?= lang('sign_up_heading') ?></h2>
                        <p class="text-center"><?= lang('sign_up_heading_subtitle') ?></p>
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <a href="<?php echo site_url('auth/facebook/'.$planId.'/'.$inviteCode); ?>"
                                   class="btn btn-facebook m-b20">
                                    <?= lang('sign_up_with_facebook') ?>
                                </a>
                                <a href="<?php echo site_url('auth/google/'.$planId.'/'.$inviteCode); ?>"
                                   class="btn btn-google m-b20">
                                    <?= lang('sign_up_with_google') ?>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <p class="text-color text-center"><?= lang('sign_up_or') ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <input type="text" name="first_name" class="form-control" placeholder="<?= lang('sign_up_first_name') ?>" value="<?php echo $first_name; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <input type="text" name="last_name" class="form-control"  placeholder="<?= lang('sign_up_last_name') ?>" value="<?php echo $last_name; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control" placeholder="<?= lang('sign_up_email') ?>" value="<?php echo $email; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" placeholder="<?= lang('sign_up_password') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <input type="password" class="form-control" name="confirm" placeholder="<?= lang('sign_up_confirm_password') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row custom-form">
                            <div class="col-xs-12">

                                <div class="pull-left">
                                    <label class="cb-checkbox text_color">
                                        <input name="terms" id="terms" type="checkbox" <?php if($terms): ?>checked="checked"<?php endif; ?>>
                                        <?= lang('sign_up_agree_to') ?>
                                        <a href="/terms"><?= lang('sign_up_terms_and_conditions') ?></a>
                                    </label>
                                </div>
                                <div class="pull-right">
                                    <button type="submit" class="btn-save"><?php echo $paymentsEnabled ? lang('next') : lang('sign_up_submit'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>