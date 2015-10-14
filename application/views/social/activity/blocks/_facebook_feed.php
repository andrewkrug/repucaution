<?php
/**
 * @var array $feed
 * @var Socializer_Facebook $socializer
 * @var Core\Service\Radar\Radar $radar
 */
?>
<?php foreach( $feed as $_post ): ?>
    <div class="web_radar_content dTable">
        <div class="dRow">
            <div class="dCell cellImg">
                <a href="<?php echo $radar->getProfileUrl('facebook').$_post['from']['id'];?>">
                    <img class="web_radar_picture" src="<?php echo $socializer->get_profile_picture($_post['from']['id']);?>" alt="">
                </a>
            </div>
            <div class="dCell">
                <p class="web_radar_date"><?php echo $radar->formatRadarDate(strtotime($_post['created_time']));?></p>
                <i class="fa fa-facebook-square i-facebook"></i>
                <?php if(!empty($_post['picture'])): ?>
                    <img class="web_radar_image" src="<?php echo $_post['picture'];?>"/>
                <?php endif; ?>
                <p class="web_radar_text">
                    <?php if(!empty($_post['message'])): ?>
                        <?php echo make_links_clicable($_post['message']); ?>
                    <?php elseif(!empty($_post['story'])): ?>
                        <?php echo $_post['story']; ?>
                    <?php endif; ?>
                </p>
                <div class="clearfix social_like">
                    <?php $is_liked = $socializer->is_liked_comment($_post);?>
                    <?php  $like_url = $is_liked
                        ? site_url('social/activity/facebook_dislike')
                        : site_url('social/activity/facebook_like');
                    $like_class = $is_liked ? 'dislike-button' : 'like-button';
                    ?>
                    <a href="javascript: void(0)" class="<?php echo $like_class ?>"
                       data-url="<?php echo $like_url; ?>"
                       data-id="<?php echo $_post['id']; ?>"
                        >
                        <?php echo $is_liked ? '<i class="ti-thumb-down"></i>' : '<i class="ti-thumb-up"></i>';?>
                    </a>
                    <?php $comments_url = site_url('social/activity/facebook_get_comments/'. $_post['id']);?>
                    <a href="javascript: void(0)" class="m-110 show_comments" data-type="not_loaded"
                       data-url="<?php echo $comments_url ?>"
                       title="Comments"
                        >
                        <i class="ti-comment-alt"></i>
                    </a>
                    <a  href="javascript: void(0)"
                        class="remove-post"
                        data-url="<?php echo site_url('social/activity/facebook_remove_comment/'.$_post['id']); ?>"
                        title="Remove"
                        >
                        <i class="ti-close"></i>
                    </a>
                </div>
                <?php echo $this->template->block('_comments', 'social/activity/blocks/_facebook_comments', array('_post' => $_post, 'picture' => $fanpicture)); ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
