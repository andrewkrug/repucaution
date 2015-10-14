<?php
/**
 * @var array $timezones
 * @var string $current_timezone
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_personal_settings') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="<?php echo site_url('settings');?>" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_personal_settings') ?></a>
                </li>
            </ul>

        </div>
    </div>
</div>
<div class="main_block">
    <form action="<?php echo site_url('settings/personal'); ?>" method="POST">
        <div class="row">
            <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
            <div class="col-md-10 col-lg-8">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('email') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" class="disabled" name="email" value="<?php echo $email; ?>" disabled/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('first_name') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" value="<?php echo $first_name;?>" name="first_name"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('last_name') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" value="<?php echo $last_name;?>" name="last_name"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('old_password') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" type="password" name="old_password"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('new_password') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" type="password" name="new_password"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('confirm_new_password') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" type="password" name="confirm_password"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <select name="timezone" class="chosen-select" id="name-timezone">
                            <?php foreach($timezones as $_timezone_name => $_timezone_value): ?>
                                <?php
                                // use urlencode, because string has "+" sign and
                                // http://stackoverflow.com/a/7410522
                                $_timezone_combined = urlencode($_timezone_value . '^'
                                    . $_timezone_name);
                                ?>
                                <option
                                    <?php
                                    // use urlencode, because string has "+" sign and
                                    // http://stackoverflow.com/a/7410522
                                    echo (isset($current_timezone)
                                        && (urlencode($current_timezone) == $_timezone_combined))
                                        ? 'selected="selected"'
                                        : '';
                                    ?>
                                        value="<?php echo $_timezone_combined; ?>"
                                    >
                                    <?php echo $_timezone_name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-lg-8">
                <div class="b-Top p-tb20 m-t20">
                    <button class="btn btn-save pull-right"><?= lang('save') ?></button>
                </div>
            </div>
        </div>
    </form>
</div>