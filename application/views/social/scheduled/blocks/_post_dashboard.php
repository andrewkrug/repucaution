<?php
/**
 * @var Post[] $posts
 */
?>
<?php foreach($posts as $post): ?>
    <div class="calendar_note clearfix">
        <p class="large-size laTo m-b20"><?php echo $post->description; ?></p>
        <p class="regRoboto"><span class="bold"><?= lang('post_on') ?>:</span><?php echo $post->getScheduledDate(lang('date_time_format')); ?></p>
        <?php if(!isset($dont_show_pagination)): ?>
        <a href="<?php echo site_url('social/scheduled/delete/'.$post->id);?>" class="remove">
            <?= lang('remove') ?>
        </a>
        <?php endif;?>
    </div>
<?php endforeach;?>

<?php if(!isset($dont_show_pagination)): ?>
    <div class="row-fluid">
        <div class="pginationBlock post-pg clearfix">
            <a class="prev <?php echo $posts->paged->has_previous ? 'active' : ''; ?>" 
                data-page="<?php echo $posts->paged->previous_page;?>" href="javascript: void(0)"
            >
                &lt;&lt; <?= lang('previous')?>
            </a>
            <div class="pgBody">
                <span><?php echo $posts->paged->current_page; ?></span>
                 / 
                <span><?php echo $posts->paged->total_pages; ?></span>
            </div>
            <a  class="next <?php echo $posts->paged->has_next ? 'active' : ''; ?>" href="javascript: void(0)" 
                data-page="<?php echo $posts->paged->next_page;?>"
            >
                <?= lang('next')?> &gt;&gt;
            </a>
        </div>
    </div>
<?php endif;?>