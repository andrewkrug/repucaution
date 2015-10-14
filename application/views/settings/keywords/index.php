<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_google_places_keywords') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_google_places_keywords') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
<form action="<?php echo site_url('settings/keywords'); ?>" method="POST">
    <div class="row">
        <div class="col-xs-12">
            <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
            <p class="black strong-size"><?= lang('keywords_info') ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-lg-8">
            <p class="text_color strong-size"><?= lang('company_name') ?></p>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group autocomplete_block">
                        <input class="form-control m-b10"  name="address" id="address" value="<?php echo $address_name; ?>"/>
                        <input type="hidden" name="address_id" id="address_id" value="<?php echo $address_id; ?>">
                        <i class="fa fa-times clear"></i>
                        <?php if (isset($errors['address'])): ?>
                            <span class="message-error"><?php echo $errors['address']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <p class="black strong-size"><?= lang('choose_keyword_phrases') ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-lg-8">
            <div class="row">
            <?php for($i = 1; $i <= $keywords_count; $i++): ?>
                <div class="col-sm-6">
                    <p class="text_color strong-size"><?= lang('keyword_phrase', [$i]); ?>
                        <?php if (isset($errors['keywords'][$i-1])): ?>
                            <span class="message-error"><?= $errors['keywords'][$i-1]['keyword']; ?></span>
                        <?php endif; ?>
                    </p>
                    <div class="form-group">
                        <input class="form-control m-b10"name="keywords[<?= $i; ?>]" id="keyword_<?= $i; ?>" value="<?= (isset($keywords_names[$i-1])) ? $keywords_names[$i-1] : ''; ?>"/>
                        <i class="fa fa-times clear"></i>
                    </div>
                </div>
            <?php endfor; ?>
            <div class="row">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button class="btn btn-save m-tb20 pull-right"><?= lang('save') ?></button>
        </div>
    </div>
</form>