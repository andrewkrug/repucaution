<div class="row">
    <div class="col-xs-12">
        <div class="tab-content settings_content">
            <div class="tab-pane active">
                <?php echo $this->template->block('post_on', 'social/create/blocks/_post_on', isset($social_post) ? array('social_post' => $social_post) : array()); ?>
                <?php if($isSupportScheduledPosts): ?>
                    <?php echo $this->template->block('schedule', 'social/create/blocks/_schedule_block', isset($social_post) ? array('social_post' => $social_post) : array());?>
                <?php endif;?>
                <div class="row">
                    <div class="col-sm-7">
                        <p class="text_color strong-size p-t10">Select feed</p>
                    </div>
                    <div class="col-sm-5 m-b20">
                        <?php echo form_dropdown('feed', $feeds, array(), 'class="chosen-select"'); ?>
                    </div>
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="feed custom-form">
                                    <?php echo $content; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="pull-right m-t20 m-b40">
            <button id="post-custom-rss-link" type="button" class="btn btn-save">Post update</button>
        </div>
    </div>
</div>