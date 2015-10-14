<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('export_to_mailchimp') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <?php if ($lists['total']) :?>
    <form id="form-edit-plan" method="POST" class="custom-form" action="<?php echo site_url('admin/mailchimp')?>">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-3">
                <h4>Lists:</h4>
                <ul class="mch-list">
                    <?php if ($lists['total']) :?>
                        <?php foreach ($lists['data'] as $list) :?>
                            <li class="list-unstyled">
                                <label class="cb-checkbox">
                                    <span><?php echo $list['name'];?></span>
                                    <input type="checkbox" name="lists[<?php echo $list['id'];?>]" value="<?php echo $list['name'];?>">
                                </label>
                            </li>
                        <?php endforeach; ?>
                    <?php endif;?>
                </ul>
            </div>
            <div class="col-xs-3">
                <h4>Groups:</h4>
                <ul class="mch-list">
                    <?php foreach ($groups as $group) :?>
                        <?php if (!(strtolower($group->name) == 'superadmin')): ?>
                            <li class="list-unstyled">
                                <label class="cb-checkbox">
                                    <span><span><?php echo $group->description;?></span></span>
                                    <input type="checkbox" name="groups[]" value="<?php echo $group->id;?>">
                                </label>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="row m-t10">
        <div class="col-xs-12">
            <div class="form-group">
                <input type="submit" class="btn btn-save" value="<?= lang('export') ?>" />
            </div>
        </div>
    </div>
    </form>
    <?php else : ?>
        <?= lang('no_lists') ?>
    <?php endif; ?>
</div>