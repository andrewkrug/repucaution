<?php if(!count($tweets)):?>
    <p class="text-center">There are no tweets.</p>
<?php endif;?>
<?php foreach($tweets as $_tweet): ?>
    <div class="web_radar_content dTable mentions-block">
        <div class="dRow">
            <div class="dCell cellImg">
                <a href="">
                    <img class="web_radar_picture" src="<?php echo $_tweet->user->profile_image_url;?>" alt="">
                </a>
            </div>
            <div class="dCell">
                <div class="author"><a href="<?php echo $radar->getProfileUrl('twitter').$_tweet->user->id_str;?>"><?php echo $_tweet->user->name; ?></a></div>
                <p class="web_radar_date"><?php echo $radar->formatRadarDate(strtotime($_tweet->created_at));?></p>
                <?php $retwittedClass = $_tweet->retweeted ? 'retweet_yet' : ''; ?>
                <?php $retwittedLink = $retwittedClass
                    ? site_url('social/activity/unretweet/'. $_tweet->id_str)
                    : site_url('social/activity/retweet/'. $_tweet->id_str);
                ?>
                <a class="reply" data-id="<?php echo $_tweet->id_str; ?>" title="Reply"
                   href="<?php echo site_url('social/activity/tweet'); ?>"
                   data-toggle="modal" data-target="#reply-window">
                    <i class="fa fa-reply"></i>
                </a>
                <a class="retweet <?php echo ($retwittedClass);?>" title="<?php echo ($retwittedClass) ? 'Unretweet' : 'Retweet';?>"
                   href="<?php echo $retwittedLink; ?>">
                    <i class="fa fa-share"></i>
                </a>
                <?php if ($_tweet->favorited) {
                    $favoritedClass = 'favorite-ment';
                    $followText = 'Unfavorite';
                } else {
                    $favoritedClass = '';
                    $followText = 'Favorite';
                }?>
                <?php $favoritedLink = !$favoritedClass
                    ? site_url('social/activity/favorite/'. $_tweet->id_str)
                    : site_url('social/activity/unfavorite/'. $_tweet->id_str);
                ?>
                <a class="favorite <?php echo $favoritedClass; ?>" title="<?php echo $followText;?>"
                   href="<?php echo $favoritedLink; ?>">
                    <i class="fa fa-star"></i>
                </a>
                <i class="fa fa-twitter-square i-twitter"></i>
                <p class="web_radar_text">
                    <?php echo preg_replace("/\b((http(s?):\/\/)|(www\.))([\w\.]+)([\/\w+\.]+)([\?\w+\.\=]+)([\&\w+\.\=]+)\b/i", "<a href=\"http$3://$4$5$6$7$8\" target=\"_blank\">$2$4$5$6$7$8</a>", $_tweet->text); ?>
                </p>
            </div>
        </div>
    </div>
<?php endforeach; ?>