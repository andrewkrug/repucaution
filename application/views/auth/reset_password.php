
<div class="main sign_in">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="sign_block ">
                    <form action="<?php echo site_url('auth/reset_password/'.$code);?>" method="POST">
                        <h2 class="sign_title">Forgot password</h2>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?php echo form_input($new_password, '', 'class="form-control"');?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?php echo form_input($new_password_confirm, '', 'class="form-control"');?>
                                </div>
                            </div>
                        </div>
                        <?php echo form_input($user_id);?>
                        <?php echo form_hidden($csrf); ?>
                        <div class="row custom-form">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    <button type="submit" class="btn-save">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>