<?php
/**
 * @var array $type_tokens
 * @var array $timezones
 * @var string $currentTimezone
 * @var Social_group[] $groups
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_my_profiles') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="<?= site_url('settings');?>" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_my_profiles') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <p class="text_color large-size"><?= lang('settings') ?></p>
            <a href="<?= site_url('settings/profiles/edit_profile');?>"
               class="btn btn-add create_account">
                <?= lang('create_a_profile') ?>
            </a>
            <div class="row m-b20 custom-form">
                <?php $index = 1; ?>
                <?php $half = (int)(count($groups->all) / 2); ?>
                <?php foreach($groups as $group) : ?>
                    <?php if($index == 1 || $index == $half+1) : ?>
                        <div class="col-sm-6">
                    <?php endif; ?>
                    <div class="group_account_item clearfix dTable">
                        <div class="dRow">
                            <div class="dCell cellImg">
                                <i class="icon-group"></i>
                            </div>
                            <div class="dCell">
                                <h3 class="group_account_title">
                                    <?= $group->name; ?>
                                </h3>
                                <small class="group_account_description"><?= $group->description; ?></small>
                                <div class="group_account_control">
                                    <a href="<?= site_url('settings/profiles/edit_profile/'.$group->id);?>" class="link ">
                                        <i class="fa fa-pencil-square-o"></i> <?= lang('edit') ?>
                                    </a>
                                    <a href="<?= site_url('settings/profiles/delete_group/'.$group->id);?>" class="remove_link m-l10">
                                        <i class="fa fa-remove"></i> <?= lang('remove') ?>
                                    </a>
                                    <label class="cb-radio w-100">
                                        <input
                                            type="radio"
                                            name="is_active"
                                            data-id="<?= $group->id ?>"
                                            <?= $group->is_active ? ' checked="checked"' : '' ?>
                                            >
                                        <?= lang('active') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if($index == $half || $index == count($groups->all)) : ?>
                        </div>
                    <?php endif; ?>
                    <?php $index++; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
