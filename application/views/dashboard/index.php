<?php
/**
 * @var array $summary
 * @var bool $need_welcome_notification
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('dashboard');?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-md-8 box_dashboard">
            <div class="block_content">
                <h2 class="block_content_title"><?= lang('trends');?></h2>
                <div class="block_content_body">
                    <ul class="trends_settings nav nav-tabs">
                        <?php if(!empty($opportunities['web_traffic'])):?>
                            <li class="trends_item active">
                                <a href="#trends_traffic" class="trends_link" data-toggle="tab">
                                    <i class="ti-server"></i>
                                    <?= lang('web_traffic');?>
                                <span class="trends_count">
                                    <?php echo $summary['web_traffic'];?>
                                </span>
                                </a>
                            </li>
                        <?php endif;?>
                        <?php if(!empty($opportunities['twitter'])):?>
                            <li class="trends_item">
                                <a href="#trends_followers" class="trends_link" data-toggle="tab">
                                    <i class="ti-twitter"></i>
                                    <?= lang('twiter_followers');?>
                                <span class="trends_count">
                                    <?php echo $summary['twiter_followers'];?>
                                </span>
                                </a>
                            </li>
                        <?php endif;?>
                        <?php if(!empty($opportunities['facebook'])):?>
                            <li class="trends_item">
                                <a href="#trends_facebook" class="trends_link" data-toggle="tab">
                                    <i class="ti-facebook"></i>
                                    <?= lang('fb_likes');?>
                                <span class="trends_count">
                                    <?php echo $summary['fb_likes'];?>
                                </span>
                                </a>
                            </li>
                        <?php endif;?>
                        <?php if(!empty($opportunities['google_rank'])):?>
                            <li class="trends_item">
                                <a href="#trends_rank" class="trends_link" data-toggle="tab">
                                    <i class="ti-bar-chart-alt"></i>
                                    <?= lang('google_rank');?>
                                <span class="trends_count">
                                    <?php echo $summary['google_rank'];?>
                                </span>
                                </a>
                            </li>
                        <?php endif;?>
                        <?php if(!empty($opportunities['reviews'])):?>
                            <li class="trends_item">
                                <a href="#trends_review" class="trends_link" data-toggle="tab">
                                    <i class="ti-comment"></i>
                                    <?= lang('reviews');?>
                                <span class="trends_count">
                                    <?php echo $summary['reviews'];?>
                                </span>
                                </a>
                            </li>
                        <?php endif;?>
                    </ul>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="trends_chart">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="trends_traffic">
                                        <div id="chart-web-traffic"></div>
                                    </div>
                                    <div class="tab-pane" id="trends_rank">
                                        <div id="chart-google-rank"></div>
                                    </div>
                                    <div class="tab-pane" id="trends_facebook">
                                        <div id="chart-facebook-likes"></div>
                                    </div>
                                    <div class="tab-pane" id="trends_followers">
                                        <div id="chart-twitter-followers"></div>
                                    </div>
                                    <div class="tab-pane" id="trends_review">
                                        <div id="chart-reviews"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 web_radar">
            <div class="block_content">
                <h2 class="block_content_title"><?= lang('web_mentions');?></h2>
                <div class="block_content_body">
                    <div class="radar_content" id="ajax-area">

                    </div>
                    <div class="block_content_footer">
                        <a href="<?php echo site_url('webradar/all') ?>" class="link"><?= lang('view_all');?></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal-->
        <div id="reply-window" class="modal fade" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <h4 id="myModalLabel" class="head_tab"><?= lang('enter_reply_text');?></h4>
                        <textarea rows="5" cols="70" class="twitter_reply_textarea"></textarea>
                    </div>
                    <div class="modal-footer clearfix">
                        <div class="pull-right">
                            <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('cancel');?></a>
                            <button type="button" id="reply" class="btn btn-save"><?= lang('send');?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="main_block m-t20">
    <div class="row">
        <div class="col-sm-8">
            <div class="block_content">
                <h2 class="block_content_title post_title"><?= lang('create_post');?></h2>
                <?php echo $this->template->block('_post_update', 'social/create/blocks/_post_update',  array(
                        'is_user_set_timezone' => $is_user_set_timezone,
                        'dashboard' => true)
                ); ?>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="block_content">
                <h2 class="block_content_title calendar_title"><?= lang('scheduled_posts');?></h2>
                <div class="block_content_body calendar_body">

                    <?php
                    echo $this->template->block('_scheduled_post', 'social/scheduled/blocks/_post_dashboard', array(
                        'posts'                => $posts,
                        'dont_show_pagination' => true
                    ));
                    ?>

                    <div class="block_content_footer">
                        <a href="<?php echo site_url('social/scheduled') ?>" class="link"><?= lang('view_all');?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($need_welcome_notification): ?>
    <script language="JavaScript">
        $(window).ready(function() {
            $('#welcome').modal();
            $.ajax({
                url: g_settings.base_url + 'social/create/update_notification',
                data: {
                    notification: 'welcome',
                    show: false
                },
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if(!data.success) {
                        showFlashErrors(data.message);
                    } else {
                        $('#welcome').modal('close');
                    }
                },
                complete: function () {
                    //stopWait();
                }
            });
        });
    </script>
<div id="welcome" class="modal welcome-modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <h4 class="modal-body__title text-center"><?= lang('thanks_for_register') ?></h4>
                <p class="modal-body__text text-center">
                    <?= lang('welcome_modal_text') ?>
                </p>
                <div class="text-center">
                    <a href="<?php echo site_url('settings/socialmedia/facebook') ?>" class="btn btn-modal btn-modal-facebook"><i class="fa fa-facebook"></i> <?= lang('connect_facebook') ?>
                    </a>
                    <a href="<?php echo site_url('settings/socialmedia/twitter') ?>" class="btn btn-modal btn-modal-twitter"><i class="fa fa-twitter"></i> <?= lang('connect_twitter') ?>
                    </a>
                    <a href="<?php echo site_url('settings/socialmedia/linkedin') ?>" class="btn btn-modal btn-modal-linkedin"><i class="fa fa-linkedin"></i> <?= lang('connect_linkedin') ?>
                    </a>
                </div>
                <p class="black text-center m-tb20">
                    <?= lang('or') ?> <a href="<?php echo site_url('settings/socialmedia') ?>" class="link"><?= lang('go_to_settings') ?></a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>