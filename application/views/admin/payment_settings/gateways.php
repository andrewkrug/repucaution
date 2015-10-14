<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <a href="<?php echo site_url('admin/payment_settings'); ?>">< <?= lang('back') ?></a>

            <h1 class="head_title"><?= lang('payment_gateway') ?>: <?php echo $gateway->name;?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <form action="" method="post">
        <div class="row custom-form">
            <div class="col-xs-12">
                <div class="form-group">
                    <label class="cb-checkbox"><?= lang('enable') ?>

                        <input type="checkbox" name="gateway[enable]" <?php if ($gateway->status) echo 'checked="checked"'; ?> value="1">

                    </label>
                </div>
                <?php foreach($gateway->getRequiredFieldsInfo() as $field):?>
                    <div class="form-group">
                        <label class="control-label" ><?php echo $field->getLabel();?></label>

                            <?php if($field->isRequired()):?>
                                <span class="required">*</span>
                            <?php endif;?>
                            <input type="text" class="form-control" name="gateway[<?php echo $field->getSlug();?>]" value="<?php echo $gateway->getFieldValue($field->getSlug());?>">
                            <?php if($field->hasDescription()):?>
                                <p><?php echo $field->getDescription();?></p>
                            <?php endif;?>

                    </div>
                <?php endforeach;?>
                <div class="form-group">
                    <input type="submit" class="btn btn-save" value="<?= lang('save') ?>" />
                </div>
            </div>
        </div>
    </form>
</div>