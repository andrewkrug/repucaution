<?php $hasErrors = isset($errors);?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('crm') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <p class="black large-size"><?= lang('add_record') ?></p>
        </div>
    </div>
    <form action="" method="POST">
        <div class="row">
            <div class="col-md-10 col-lg-8">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('first_name') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" value="<?php echo (!empty($recent['firstname'])) ? $recent['firstname'] : $directory->firstname; ?>" name="firstname"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('last_name') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" value="<?php echo (!empty($recent['lastname'])) ? $recent['lastname'] : $directory->lastname; ?>" name="lastname"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('email') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" name="email" value="<?php echo (!empty($recent['email'])) ? $recent['email'] : $directory->email; ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('phone') ?></p>
                        <div class="form-group">
                            <input class="form-control" type="text" name="phone" value="<?php echo (!empty($recent['phone'])) ? $recent['phone'] : $directory->phone; ?>"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('company') ?></p>
                        <div class="form-group">
                            <input class="form-control" type="text" name="company" value="<?php echo (!empty($recent['company'])) ? $recent['company'] : $directory->company; ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('address') ?></p>
                        <div class="form-group">
                            <input class="form-control" type="text" name="address" value="<?php echo (!empty($recent['address'])) ? $recent['address'] : $directory->address; ?>"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('website') ?></p>
                        <div class="form-group">
                            <input class="form-control" type="text" name="website" value="<?php echo (!empty($recent['website'])) ? $recent['website'] : $directory->website; ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('notes') ?></p>
                        <div class="form-group">
                            <input class="form-control" type="text" name="notes" value="<?php echo (!empty($recent['notes'])) ? $recent['notes'] : $directory->notes; ?>"/>
                        </div>
                    </div>
                </div>
                <div class="row">
<!--                    <div class="col-sm-6">-->
<!--                        <p class="text_color strong-size">Facebook profile link</p>-->
<!--                        <div class="form-group">-->
<!--                            <input class="form-control" type="text" name="facebook_link" value="--><?php //echo (!empty($recent['facebook_link'])) ? $recent['facebook_link'] : $directory->facebook_link; ?><!--"/>-->
<!--                        </div>-->
<!--                    </div>-->
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('twitter_link') ?></p>
                        <div class="form-group">
                            <input class="form-control" type="text" name="twitter_link" value="<?php echo (!empty($recent['twitter_link'])) ? $recent['twitter_link'] : $directory->twitter_link; ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('instagram_link') ?></p>
                        <div class="form-group">
                            <input class="form-control" type="text" name="instagram_link" value="<?php echo (!empty($recent['instagram_link'])) ? $recent['instagram_link'] : $directory->instagram_link; ?>"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button class="btn btn-save m-tb20 pull-right"><?= lang('save') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function(){
        if (g_settings.errors) {
            showFormErrors(g_settings.errors);
        }
    })
</script>