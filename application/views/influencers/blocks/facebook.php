<!--<p class="ment-text">
    <?php /*if($mention->message): */?>
        <?php /*echo make_links_clicable($mention->message); */?>
    <?php /*elseif($story = $mention->other_field('story')): */?>
        <?php /*echo $story; */?>
    <?php /*endif; */?>
</p>
<div class="clear"></div>
<?php /*if (in_array($mention->other_field('type'), array('link', 'photo', 'video'))): */?>
    <div class="clear" style="margin-top: 10px"></div>
    <?php /*if($picture = $mention->other_field('picture')): */?>
        <p>
            <a target="_blank" href="<?php /*echo $mention->other_field('link', '#');*/?>">
                <img class="picture" src="<?php /*echo $picture;*/?>"/>
            </a>
        </p>
    <?php /*else: */?>
        <div>
            <?php /*if($link = $mention->other_field('link')): */?>
            <p>
                <a href="<?php /*echo $link;*/?>" target="_blank">
                    <?php /*echo $link;*/?>
                </a>
            </p>
            <?php /*endif;*/?>
        </div>
    <?php /*endif; */?>
<?php /*endif; */?>
<div class="clear"></div>
<?php /*if ($mention->actions) :*/?>
<div class="action clearfix social-actions radar">
    <?php /*$isLiked = $mention->other_field('i_like', 0) > 0;*/?>
    <?php /*  $likeUrl = $isLiked
                    ? site_url('social/activity/facebook_dislike') 
                    : site_url('social/activity/facebook_like');
            $likeClass = $isLiked ? 'dislike-button' : 'like-button';
        */?>
    <a href="javascript: void(0)" class="<?php /*echo $likeClass */?>"
        data-url="<?php /*echo $likeUrl; */?>"
        data-id="<?php /*echo $mention->original_id; */?>"
    >
        <?php /*echo $isLiked ? 'Unlike' : 'Like'; */?>
    </a>
    
    <?php /*$commentsUrl = site_url('social/activity/facebook_get_comments/'
            . $mention->original_id); 
    */?>
    <a href="javascript: void(0)" class="show-comments" data-type="not_loaded" 
        data-url="<?php /*echo $commentsUrl */?>"
    >
        Comments
    </a>
    <a href="javascript: void(0)" class="remove-influencer"
       data-creator_id="<?php /*echo $mention->creator_id; */?>"
       data-social="<?php /*echo $mention->social; */?>"
       style="display:none"
    >
        Delete from influencers
    </a>
</div>
<?php /*endif;*/?>
--><?php /*echo $this->template->block(
        '_comments', 
        'social/activity/blocks/_facebook_comments', 
        array(
            '_post' => array('id' => $mention->original_id),
            'picture' => $mention->user_image,
        )
    ); 
*/?>

<!--<p class="ment-text">
    <?php /*if($mention->message): */?>
        <?php /*echo make_links_clicable($mention->message); */?>
    <?php /*elseif($story = $mention->other_field('story')): */?>
        <?php /*echo $story; */?>
    <?php /*endif; */?>
</p>
<div class="clear"></div>
<?php /*if (in_array($mention->other_field('type'), array('link', 'photo', 'video'))): */?>
    <div class="clear" style="margin-top: 10px"></div>
    <?php /*if($picture = $mention->other_field('picture')): */?>
        <p>
            <a target="_blank" href="<?php /*echo $mention->other_field('link', '#');*/?>">
                <img class="picture" src="<?php /*echo $picture;*/?>"/>
            </a>
        </p>
    <?php /*else: */?>
        <div>
            <?php /*if($link = $mention->other_field('link')): */?>
            <p>
                <a href="<?php /*echo $link;*/?>" target="_blank">
                    <?php /*echo $link;*/?>
                </a>
            </p>
            <?php /*endif;*/?>
        </div>
    <?php /*endif; */?>
<?php /*endif; */?>
<div class="clear"></div>
<?php /*if ($mention->actions) :*/?>
<div class="action clearfix social-actions radar">
    <?php /*$isLiked = $mention->other_field('i_like', 0) > 0;*/?>
    <?php /*  $likeUrl = $isLiked
                    ? site_url('social/activity/facebook_dislike')
                    : site_url('social/activity/facebook_like');
            $likeClass = $isLiked ? 'dislike-button' : 'like-button';
        */?>
    <a href="javascript: void(0)" class="<?php /*echo $likeClass */?>"
        data-url="<?php /*echo $likeUrl; */?>"
        data-id="<?php /*echo $mention->original_id; */?>"
    >
        <?php /*echo $isLiked ? 'Unlike' : 'Like'; */?>
    </a>

    <?php /*$commentsUrl = site_url('social/activity/facebook_get_comments/'
            . $mention->original_id);
    */?>
    <a href="javascript: void(0)" class="show-comments" data-type="not_loaded"
        data-url="<?php /*echo $commentsUrl */?>"
    >
        Comments
    </a>
    <?php /*if (!$mention->influencer) :*/?>
        <a href="javascript: void(0)" class="add-influencer"
           data-creator_id="<?php /*echo $mention->creator_id; */?>"
           data-social="<?php /*echo $mention->social; */?>"
           style="display:none"
            >
            Add to influencers
        </a>
    <?php /*endif;*/?>
</div>
<?php /*endif;*/?>
--><?php /*echo $this->template->block(
        '_comments',
        'social/activity/blocks/_facebook_comments',
        array(
            '_post' => array('id' => $mention->original_id),
            'picture' => $mention->user_image,
        )
    );
*/?>
<i class="fa fa-facebook-square i-facebook"></i>
<?php if($picture = $mention->other_field('picture')): ?>
    <img class="web_radar_image" src="<?php echo $picture;?>"/>
<?php endif; ?>
<p class="web_radar_text">
    <?php if($mention->message): ?>
        <?php echo make_links_clicable($mention->message); ?>
    <?php elseif($story = $mention->other_field('story')): ?>
        <?php echo $story; ?>
    <?php endif; ?>
</p>
<?php if ($mention->actions) :?>
    <div class="clearfix m-t10">
        <?php $isLiked = $mention->other_field('i_like', 0) > 0;?>
        <?php   $likeUrl = $isLiked
            ? site_url('social/activity/facebook_dislike')
            : site_url('social/activity/facebook_like');
        $likeClass = $isLiked ? 'dislike-button' : 'like-button';
        ?>
        <a href="javascript: void(0)" class="<?php echo $likeClass ?>"
           data-url="<?php echo $likeUrl; ?>"
           data-id="<?php echo $mention->original_id; ?>"
            >
            <?php echo $isLiked ? 'Unlike' : 'Like'; ?>
        </a>

        <?php $commentsUrl = site_url('social/activity/facebook_get_comments/'
            . $mention->original_id);
        ?>
        <a href="javascript: void(0)" class="m-l10 show_comments" data-type="not_loaded"
           data-url="<?php echo $commentsUrl ?>"
            >
            Comments
        </a>
        <a href="javascript: void(0)" class="remove-influencer"
           data-creator_id="<?php echo $mention->creator_id; ?>"
           data-social="<?php echo $mention->social; ?>"
           style="display:none"
            >
            Delete from influencers
        </a>
    </div>
<?php endif;?>
<?php echo $this->template->block(
    '_comments',
    'social/activity/blocks/_facebook_comments',
    array(
        '_post' => array('id' => $mention->original_id),
        'picture' => $mention->user_image,
    )
);
?>