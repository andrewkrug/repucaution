<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_directory_settings') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="<?php echo site_url('settings');?>" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_directory_settings') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
    <form action="<?php echo site_url('settings/directories');?>" method="POST">
        <div class="row">
            <div class="col-md-10 col-lg-8">
            <?php foreach($directories as $_directory):?>
                <div class="col-sm-6 <?php if( end($directories->all) === $_directory) echo 'last'; ?>">
                    <p class="text_color strong-size"><?php echo $_directory->name;?><?php if( method_exists($parsers[$_directory->id],'autocomplete')): ?><span class="small-font">&nbsp;<?= lang('type_business_name') ?></span><?php endif;?></p>
                    <div class="form-group <?php if( method_exists($parsers[$_directory->id],'autocomplete')): ?>autocomplete_block <?php endif;?><?php echo $_directory->cssClass();?>">
                        <input class="form-control" name="directory[<?php echo $_directory->id;?>]" value="<?php if(!empty($user_directories[$_directory->id]['link'])) echo $user_directories[$_directory->id]['link']; ?>"/>
                    </div>
                </div>
            <?php endforeach;?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-lg-8">
                <div class="b-Top p-tb20 m-t20">
                    <button class="btn btn-save pull-right" type="submit"><?= lang('save') ?></button>
                </div>
            </div>
        </div>
    </form>
</div>