<?php
/**
 * @var array $type_tokens
 * @var Social_group $group
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('create_a_profile') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="<?php echo site_url('settings');?>" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item">
                    <a href="<?php echo site_url('settings/profiles');?>" class="breadcrumbs_link"><?= lang('settings_my_profiles') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('create_a_profile') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <form action="<?php echo site_url('settings/profiles/edit_profile/'.$group->id);?>" method="post">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('name_of_the_profile') ?></p>
                        <div class="form-group">
                            <input type="text" name="group_name" class="form-control" placeholder="<?= lang('name_of_the_profile') ?>" value="<?= $group->name; ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('description_of_the_profile') ?></p>
                        <div class="form-group">
                            <textarea rows="5" name="group_description" class="form-control"><?= $group->description; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="b-Top m-t20 p-t20">
                            <div class="pull-sm-left p-tb10">
                                <a href="<?php echo site_url('settings/profiles/');?>" class="link ">
                                    <i class="fa fa-long-arrow-left blue-color m-r5"></i>
                                    <?= lang('back_to_profile_settings') ?>
                                </a>
                            </div>
                            <div class="pull-sm-right">
                                <button type="submit" class="btn-save m-b20"><?= lang('save') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>