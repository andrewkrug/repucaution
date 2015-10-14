<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <a href="<?php echo site_url('admin/payment_settings'); ?>">< <?= lang('back') ?></a>

            <h1 class="head_title"><?= lang('edit_influencers_condition') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <form action="" method="post">
        <div class="row custom-form">
            <div class="col-xs-12">
                <div class="form-group">
                    <label><?php echo $condition->option_name;?></label>
                    <input name="value" type="text" placeholder="<?= lang('value') ?>" value="<?php echo $condition->value;?>">

                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-save" value="<?= lang('save') ?>" />
                </div>
            </div>
        </div>
    </form>
</div>
