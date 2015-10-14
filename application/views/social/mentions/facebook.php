<div id="ajax-area" class="main-list">
    <?php foreach($mentions as $mention): ?>
        <div class="mentions-block clearfix">
            <div class="photoBox">
                <img width="80px" height="80px" src="" class="no-avatar" style="height: 48px">
            </div>
            <div class="data pull-right">
                <div class="info-line clearfix">
                    <div class="author"><?php echo $mention->creator_name; ?></div>
                    <div class="pull-right date-time">
                        <?php echo date(lang('mentions_format'), $mention->created_at);?>
                    </div>
                </div>
                <p>
                    <?php if($mention->message): ?>
                        <?php echo make_links_clicable($mention->message); ?>
                    <?php elseif($story = $mention->other_field('story')): ?>
                        <?php echo $story; ?>
                    <?php endif; ?>
                </p>
                <div class="clear"></div>
                <?php if (in_array($mention->other_field('type'), array('link', 'photo', 'video'))): ?>
                    <div class="clear" style="margin-top: 10px"></div>
                    <?php if($picture = $mention->other_field('picture')): ?>
                        <p>
                            <a target="_blank" href="<?php echo $mention->other_field('link', '#');?>">
                                <img class="picture" src="<?php echo $picture;?>"/>
                            </a>
                        </p>
                    <?php else: ?>
                        <div>
                            <?php if($link = $mention->other_field('link')): ?>
                            <p>
                                <a href="<?php echo $link;?>" target="_blank">
                                    <?php echo $link;?>
                                </a>
                            </p>
                            <?php endif;?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="clear"></div>
                <div class="action clearfix social-actions">
                    <div class="like now">
                        <?php echo $mention->other_field('likes', 0); ?>
                    </div>
                    <?php $is_liked = $mention->other_field('i_like', 0) > 0;?>
                    <?php   $like_url = $is_liked 
                                    ? site_url('social/activity/facebook_dislike') 
                                    : site_url('social/activity/facebook_like');
                            $like_class = $is_liked ? 'dislike-button' : 'like-button';
                        ?>
                    <a href="javascript: void(0)" class="<?php echo $like_class ?>" 
                        data-url="<?php echo $like_url; ?>" 
                        data-id="<?php echo $mention->original_id; ?>"
                    >
                        <?php echo $is_liked ? 'Unlike' : 'Like'; ?>
                    </a>
                    <div class="comment">
                        <?php echo $mention->other_field('comments', 0); ?>
                    </div>
                    <?php $comments_url = site_url('social/activity/facebook_get_comments/' 
                            . $mention->original_id); 
                    ?>
                    <a href="javascript: void(0)" class="show-comments" data-type="not_loaded" 
                        data-url="<?php echo $comments_url ?>"
                    >
                        Comments
                    </a>
                </div>
                <?php echo $this->template->block(
                        '_comments', 
                        'social/activity/blocks/_facebook_comments', 
                        array(
                            '_post' => array('id' => $mention->original_id),
                            'picture' => $profile_photo,
                        )
                    ); 
                ?>
            </div>
            
        </div>            
    <?php endforeach; ?>
</div>
