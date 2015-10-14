<?php
/**
 * @var array $options
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('options') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <form action="" method="post">
                <?php foreach($options as $slug => $option): ?>
                    <input type="checkbox" name="options[<?= $slug ?>]" <?php if ($option) echo 'checked="checked"'; ?> value="1"> <?= lang($slug) ?>
                <?php endforeach; ?>
                <div>
                    <input type="submit" class="btn btn-save" value="<?= lang('save') ?>"/>
                </div>
            </form>
        </div>
    </div>
</div>