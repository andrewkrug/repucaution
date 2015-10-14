
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_social_keywords') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_social_keywords') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
        <div class="col-md-10 col-lg-8">
            <div class="row custom-form">
                <div class="col-xs-12">
                    <p class="text_color strong-size pull-sm-left p-t5">
                        <?= lang('keywords_info') ?>
                    </p>
                </div>
                <div class="col-xs-12">
                    <div class="row hidden insert_block">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <input class="form-control m-b10" type="text" name="keyword[]" value="">
                                <i class="cb-remove"></i>
                                <div class="clearfix">
                                    <label class="cb-checkbox text-size pull-sm-left">
                                        <input type="checkbox" id="keyword_exact_" name="exact[]">
                                        <?= lang('exact') ?>
                                    </label>
                                    <div class="pull-sm-right">
                                        <a href="" class="link show_block"><?= lang('include_exclude') ?></a>
                                    </div>
                                </div>
                                <div class="toggle_block row">
                                    <div class="col-sm-6">
                                        <p class="text_color"><?= lang('include') ?></p>
                                        <div class="form-group">
                                            <textarea class="form-control" placeholder="<?= lang('comma_separated_words') ?>"
                                                      id="mentions_keywords_include_"
                                                      name="include[]"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text_color"><?= lang('exclude') ?></p>
                                        <div class="form-group">
                                            <textarea class="form-control" placeholder="<?= lang('comma_separated_words') ?>"
                                                      id="mentions_keywords_exclude_"
                                                      name="exclude[]"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="mention_form" action="<?php echo site_url('settings/mention_keywords'); ?>" method="POST">
                    <div class="col-xs-12 m-t20">

                        <?php /*$i = 1; */?>
                        <?php $keywordsCount = count($keywords);?>
                        <?php $inputsCount = $keywordsCount;?>
                        <?php //foreach ($keywords as $keyword): ?>
                        <?php for ($i = 1; $i <= $inputsCount; $i++):?>
                            <?php $keyword = !empty($keywords[$i-1]) ? $keywords[$i-1] : null;?>
                            <?php $id = ((!empty($keyword)) && $keyword->id) ? $keyword->id : 'new_' . $i; ?>
                            <div class="row past_block">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control m-b10" name="keyword[<?php echo $id; ?>]"
                                               value="<?php echo ($keyword) ? HTML::chars($keyword->keyword) : ''; ?>" type="text">
                                        <i class="cb-remove"></i>
                                        <div class="clearfix">
                                            <label class="cb-checkbox text-size pull-sm-left">
                                                <input type="checkbox" name="exact[<?php echo $id; ?>]"
                                                       <?php if ($keyword && $keyword->exact): ?>checked="checked"<?php endif; ?>
                                                    >
                                                <?= lang('exact') ?>
                                            </label>
                                            <div class="pull-sm-right">
                                                <a href="" class="link show_block"><?= lang('include_exclude') ?></a>
                                            </div>
                                        </div>
                                        <div class="toggle_block row">
                                            <div class="col-sm-6">
                                                <p class="text_color"><?= lang('include') ?>+</p>
                                                <div class="form-group">
                                                    <textarea class="form-control" placeholder="<?= lang('comma_separated_words') ?>"
                                                              id="mentions_keywords_include_<?php echo $id ?>"
                                                              name="include[<?php echo $id; ?>]"><?php echo ($keyword) ? HTML::chars($keyword->get_other_fields('include', TRUE)) : ''; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <p class="text_color"><?= lang('exclude') ?></p>
                                                <div class="form-group">
                                                    <textarea class="form-control" placeholder="<?= lang('comma_separated_words') ?>"
                                                              id="mentions_keywords_exclude_<?php echo $id ?>"
                                                              name="exclude[<?php echo $id; ?>]"><?php echo ($keyword) ? HTML::chars($keyword->get_other_fields('exclude', TRUE)) : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor;?>

                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-sm-right">
                                <button id="add_mention" type="button" class="btn btn-add m-tb20 m-r20"><?= lang('add_keyword') ?></button>
                                <button class="btn btn-save m-tb20 pull-right"><?= lang('save') ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
