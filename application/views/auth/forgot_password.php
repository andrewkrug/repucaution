
<div class="main sign_in">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="sign_block ">
                    <form action="<?php echo site_url('auth/forgot_password'); ?>" method="POST">
                        <h2 class="sign_title"><?= lang('forgot_password_heading') ?></h2>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?php echo form_input($email, '', 'class="form-control"');?>
                                </div>
                            </div>
                        </div>
                        <div class="row custom-form">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    <button type="submit" class="btn-save"><?= lang('forgot_password_submit_btn') ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>