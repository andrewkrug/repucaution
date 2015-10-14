<?php
/**
 * @var Social_post[] $posts
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="web_radar m-t20 pull_border">
            <?php foreach($posts as $post): ?>

                <div class="post_content dTable">
                    <div class="dRow">
                        <div class="dCell">
                            <div class="clearfix">
                                <p class="pull-sm-left">
                                    <span class="post_date"><?= lang('post_on') ?>: </span><?php echo $post->getScheduledDate(lang('date_time_format')); ?>
                                </p>
                                <p class="pull-sm-right">
<!--                                    <a class="link m-r10 edit" href="javascript: void(0)" data-id="--><?php //echo $post->id; ?><!--"-->
<!--                                       data-category="--><?php //echo $post->category_id; ?><!--" class="edit">Edit</a>-->
                                    <a class="remove_link" href="<?php echo site_url('social/scheduled/delete/'.$post->id);?>"><?= lang('remove')?></a>
                                </p>
                            </div>
                            <p class="web_radar_text ">
                                <?php echo $post->description; ?>
                            </p>
                            <div class="clearfix">
                                <?php
                                    $socials = unserialize($post->post_to_socials);
                                    foreach($socials as &$social) {
                                        $social = ucfirst($social);
                                    }
                                    $postedTo = implode(', ', $socials);
                                ?>
                                <p class="pull-sm-left m-b0"><span class="post_date">To: </span><?php echo $postedTo; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="pull-right">
            <ul class="pagination">
                <li class="prev pagination_item <?php echo $posts->paged->has_previous ? 'active' : 'unactive'; ?>" data-page="<?php echo $posts->paged->previous_page;?>">
                    <a href="javascript: void(0)" class="pagination_link"><?= lang('previous')?></a>
                </li>
                <li class="pagination_item active">
                    <a href="javascript: void(0)" class="pagination_link"><?php echo $posts->paged->current_page; ?></a>
                </li>
                <li class="next pagination_item <?php echo $posts->paged->has_next ? 'active' : 'unactive'; ?>" data-page="<?php echo $posts->paged->next_page;?>">
                    <a href="javascript: void(0)" class="pagination_link"><?= lang('next')?></a>
                </li>
            </ul>
        </div>
    </div>
</div>