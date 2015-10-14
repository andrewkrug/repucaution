<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('web_radar') ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="<?php echo site_url('webradar/all');?>" class="breadcrumbs_link"><?= lang('web_radar') ?></a>
                </li>

                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?php echo $social;?></a>
                </li>

            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-sm-6">
            <select class="chosen-select" id="select-period">
                <option value="0" <?= $dateRange == 0 ? 'selected=selected' : '' ?>><?= lang('today') ?></option>
                <option value="3" <?= $dateRange == 3 ? 'selected=selected' : '' ?>><?= lang('past_3_days') ?></option>
                <option value="7" <?= $dateRange == 7 ? 'selected=selected' : '' ?>><?= lang('past_week') ?></option>
            </select>
        </div>
        <div class="col-sm-6">
            <?php echo form_dropdown('keyword', $keywords, $keyword, 'class="chosen-select"'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="date_range p-tb10">
                <div class="reportrange" >
                    <i class="fa fa-calendar"></i>
                    <span></span>

                    <b class="caret"></b>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">

                <?php if (! $hasKeywords || empty($feed)): ?>
                    <p class="large-size m-t20 p-b10 b-Bottom text_color">

                        <?php if( ! $hasKeywords): ?>
                        <?= lang('no_keywords') ?>
                        <a href="<?php echo site_url('settings/mention_keywords'); ?>"
                           class=""
                            >
                            <?= lang('go_to_keywords_settings') ?>
                        </a>
                        <?php elseif( empty($feed)): ?>
                            <?= lang('no_mentions'); ?>
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <div id="ajax-area" class="web_radar m-t20 pull_border ">
                    <!-- HERE GOES PARTICULAR SOCIAL BLOCK -->
                    <?php echo $feed; ?>
                    <!-- HERE GOES PARTICULAR SOCIAL BLOCK -->
                    <!-- Modal-->
                        <div id="reply-window" class="modal fade" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <div class="modal-body">
                                        <h4 id="myModalLabel" class="head_tab"><?= lang('enter_reply_text') ?></h4>
                                        <textarea rows="5" cols="70" class="twitter_reply_textarea"></textarea>
                                    </div>
                                    <div class="modal-footer clearfix">
                                        <div class="pull-right">
                                            <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('cancel') ?></a>
                                            <button type="button" id="reply" class="btn btn-save"><?= lang('send') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>


            </div>
        </div>
    </div>
</div>