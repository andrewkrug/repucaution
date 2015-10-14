<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_piwik') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_piwik') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
        <div class="col-md-10 col-lg-8">
            <div class="row">
                <div class="col-xs-12">
                    <p class="text_color strong-size"><?= lang('please_add_piwik_api_keys') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>