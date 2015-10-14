<?php
/**
 * @var array $sites
 * @var string $site_id
 */
?>
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
                    <select class="chosen-select" id="piwik_site">
                        <option value=""><?= lang('please_select_site') ?></option>
                        <?php foreach($sites as $site): ?>
                            <option value="<?= $site->idsite ?>" <?= ($site_id == $site->idsite) ? "selected" : "" ?>><?= $site->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>