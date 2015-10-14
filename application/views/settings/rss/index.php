<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_rss') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_rss') ?></a>
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
                    <div class="row feeds">
                        <div class="col-xs-12">
                            <p class="black strong-size">
                                <?= lang('add_new_rss') ?>
                            </p>
                        </div>
                        <div class="col-sm-5">
                            <form id="custom_form" action="<?php echo site_url('settings/rss/update_rss_custom'); ?>" method="POST">
                                <div class="row hidden more_block">
                                    <div class="col-xs-12">
                                        <p class="text_color strong-size"><?= lang('title') ?>
                                            <a href="" class="pull-right link remove_more"><?= lang('remove') ?></a>
                                        </p>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="rss_custom_title-">
                                        </div>
                                        <p class="text_color strong-size"><?= lang('link_url') ?></p>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="rss_custom_link-">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <p class="text_color strong-size"><?= lang('title') ?></p>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="custom[1][title]" id="rss_custom_title-1">
                                        </div>
                                        <p class="text_color strong-size"><?= lang('link_url') ?></p>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="custom[1][link]" id="rss_custom_link-1">
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <button class="btn btn-add pull-left more-link" type="button"><?= lang('one_more') ?></button>
                                    <button class="btn btn-save pull-right" id="#rss_custom_save"><?= lang('save') ?></button>
                                </div>
                            </form>
                        </div>
                        <div class="col-xs-12 p-t15">
                            <p class="text_color strong-size"><?= lang('list_of_rss') ?></p>
                            <?php
                                if ($rss_custom->exists()):
                                $counter = 0;
                            ?>
                            <?php foreach($rss_custom as $custom): ?>
                                <div <?php if (++$counter == $rss_custom->result_count()): ?>class="last"<?php endif; ?> data-id="<?php echo $custom->id; ?>">
                                    <p class="black">
                                        <span class="bold"><?php echo $title = HTML::chars($custom->title); ?></span> <?php echo $link = HTML::chars($custom->link); ?>
                                    </p>
                                    <a class="link" data-toggle="modal" data-target=".remove_custom_modal" data-title="<?php echo $title; ?>" data-link="<?php echo $link; ?>"><?= lang('remove') ?></a>
                                </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <div class="last">-</div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<div class="modal fade in remove_custom_modal" aria-hidden="true" id="remove_custom_feed_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4 class="head_tab"><?= lang('remove_feed') ?></h4>
                <p class="black"><?= lang('remove_text') ?></p>
            </div>
            <div class="modal-footer clearfix">
                <div class="pull-right">
                    <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('close') ?></a>
                    <button type="button" id="remove_custom_button" class="btn btn-save" data-id=""><?= lang('remove') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>