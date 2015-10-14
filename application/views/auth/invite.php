<div class="login-block reset-password clearfix">
    <form action="<?php echo site_url(); ?>auth/invite" method="POST">
        <div class="title">Complete registration</div>
        <div class="control-group">

                <div class="control-group">
                    <div class="control">
                        <input type="password" name="password" placeholder="Password">
                    </div>
                </div>
                <div class="control-group">
                    <div class="control">
                        <input type="password" name="confirm" placeholder="Confirm password">
                    </div>
                </div>
                <input type="hidden" name="code" value="<?php echo $code;?>">

        </div>
        <div class="control-group last">
            <div class="control pull-right">
                <input type="submit" class="black-btn" value="Submit">
            </div>
            <div class="clear"></div>
        </div>
    </form>
</div>