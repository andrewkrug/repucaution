<p class="ment-text">
    <?php if($mention->message): ?>
        <?php echo make_links_clicable($mention->message); ?>
    <?php endif; ?>
</p>
<p class="ment-text">
    tags:
    <?php if($tags = $mention->other_field('tags', false)): ?>
        <?php echo implode(' ', $tags); ?>
    <?php endif; ?>
</p>
<div class="clear"></div>

    <div class="clear" style="margin-top: 10px"></div>
    <p>
        <a target="_blank" href="">
            <img class="picture" src="<?php echo $mention->other_field('low_resolution');?>"/>
        </a>
    </p>

<div class="clear"></div>
<?php if ($mention->actions) :?>
<div class="action clearfix social-actions radar">
    <?php $isLiked = $mention->other_field('i_like', 0) > 0;?>
    <?php   $likeUrl = $isLiked 
                    ? site_url('social/activity/instagramdislike')
                    : site_url('social/activity/instagramlike');
            $likeClass = $isLiked ? 'dislike-button' : 'like-button';
        ?>
    <a href="javascript: void(0)" class="<?php echo $likeClass ?>" 
        data-url="<?php echo $likeUrl; ?>" 
        data-id="<?php echo $mention->original_id; ?>"
    >
        <?php echo $isLiked ? 'Unlike' : 'Like'; ?>
    </a>
    
    <?php $commentsUrl = site_url('social/activity/instagram_get_comments/'
            . $mention->original_id); 
    ?>
    <a href="javascript: void(0)" class="show-comments" data-type="not_loaded" 
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
    'social/activity/blocks/_instagram_comments',
    array(
        '_post' => array('id' => $mention->original_id),
        'picture' => $mention->user_image,
    )
);
?>


<i class="fa fa-instagram i-instagram"></i>
<img src="<?php echo $mention->other_field('low_resolution');?>" class="web_radar_image" alt="">
<p class="web_radar_text">
    <?php if($mention->message): ?>
        <?php echo make_links_clicable($mention->message); ?>
    <?php endif; ?>
</p>
<?php if ($mention->actions) :?>
    <div class="clearfix m-t10">
        <?php $isLiked = $mention->other_field('i_like', 0) > 0;?>
        <?php   $likeUrl = $isLiked
            ? site_url('social/activity/instagramdislike')
            : site_url('social/activity/instagramlike');
        $likeClass = $isLiked ? 'dislike-button' : 'like-button';
        ?>
        <a href="javascript: void(0)" class="<?php echo $likeClass ?>"
           data-url="<?php echo $likeUrl; ?>"
           data-id="<?php echo $mention->original_id; ?>"><?php echo $isLiked ? 'Unlike' : 'Like'; ?></a>
        <?php $commentsUrl = site_url('social/activity/instagram_get_comments/'
            . $mention->original_id);
        ?>
        <a class="m-l10 show_comments" href="javascript: void(0)" data-type="not_loaded"
           data-url="<?php echo $commentsUrl ?>">Comments</a>
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
    'social/activity/blocks/_instagram_comments',
    array(
        '_post' => array('id' => $mention->original_id),
        'picture' => $mention->user_image,
    )
);
?>