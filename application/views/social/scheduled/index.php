
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('social_media_scheduled_posts')?></h1>
            <div class="row">
                <div class="col-xs-12">
                    <?php echo $this->template->block('app_breadcrumbs', 'layouts/block/application/breadcrumbs', array('menu' => 'customer.main')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main_block" id="ajax-container">
    <?php
        echo $this->template->block(
            '_scheduled_post',
            'social/scheduled/blocks/_post',
            array('posts' => $posts)
        );
    ?>
</div>