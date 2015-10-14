<script id="alert-template" type="text/x-handlebars-template">
    <div class="message-error configure-error"> <i class="icon"></i> <span>{{message}}</span></div>
    <div class="clear clear-20"></div>
</script>

<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('social_media_social_reports') ?></h1>
            <div class="row">
                <div class="col-xs-12">
                    <ul class="breadcrumbs">
                        <li class="breadcrumbs_item">
                            <a href="<?php echo site_url('social');?>" class="breadcrumbs_link"><?= lang('social_media') ?></a>
                        </li>
                        <li class="breadcrumbs_item active">
                            <a href="" class="breadcrumbs_link"><?= lang('social_media_social_reports') ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-sm-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix ">
                        <h2 class="block_content_title pull-sm-left w-100">
							<span class="pull-sm-left">
								<i class="ti-facebook"></i> Facebook
							</span>
							<span class="pull-sm-right">
								<span class="likes_count"><strong id="likes-count">0</strong> <?= lang('likes') ?></span>
							</span>
                        </h2>
                    </div>
                </div>
                <div class="block_content_body">
                    <div class="row">
                        <form id="facebook-dates" action="<?php echo site_url('social/reports/get_chart_data'); ?>">
                            <div class="col-sm-3">
                                <div class="form-group date_calendar">
                                    <input type="text" class="form-control input_date" name="from">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group date_calendar">
                                    <input type="text" class="form-control input_date" name="to">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <input type="hidden" name="type" value="facebook">
                                <input type="submit" class="btn btn-save" value="<?= lang('apply') ?>">
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 m-b10">
                            <span class="link"><?= lang('trending_likes') ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div  class="shedule_report" id="chartbox-facebook" width="640" height="350"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix ">
                        <h2 class="block_content_title pull-sm-left w-100">
							<span class="pull-sm-left">
								<i class="ti-twitter"></i> Twitter
							</span>
							<span class="pull-sm-right">
								<span class="likes_count"><span id="followers-count">0</span> <?= lang('followers') ?></span>
							</span>
                        </h2>
                    </div>
                </div>
                <div class="block_content_body">
                    <div class="row">
                        <form id="twitter-dates" action="<?php echo site_url('social/reports/get_chart_data'); ?>">
                            <div class="col-sm-3">
                                <div class="form-group date_calendar">
                                    <input type="text" class="form-control input_date" name="from">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group date_calendar">
                                    <input type="text" class="form-control input_date" name="to">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <input type="hidden" name="type" value="twitter">
                                <input type="submit" class="btn btn-save" value="<?= lang('apply') ?>">
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 m-b10">
                            <span class="link"><?= lang('trending_followers') ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="shedule_report" id="chartbox-twitter" width="640" height="350"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <h2 class="block_content_title pull-sm-left w-100">
							<span class="pull-sm-left">
								<i class="ti-google"></i> Google
							</span>
							<span class="pull-sm-right">
								<span class="likes_count"><span id="friends-count">0</span> <?= lang('friends') ?></span>
							</span>
                        </h2>
                    </div>
                </div>
                <div class="block_content_body">
                    <div class="row">
                        <form id="google-dates" action="<?php echo site_url('social/reports/get_chart_data'); ?>">
                            <div class="col-sm-3">
                                <div class="form-group date_calendar">
                                    <input type="text" class="form-control input_date" name="from">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group date_calendar">
                                    <input type="text" class="form-control input_date" name="to">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <input type="hidden" name="type" value="google">
                                <input type="submit" class="btn btn-save" value="<?= lang('apply') ?>">
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 m-b10">
                            <span class="link"><?= lang('trending_friends') ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="shedule_report" id="chartbox-google" width="640" height="350"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>