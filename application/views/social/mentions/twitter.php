<div id="ajax-area">
    <?php foreach($mentions as $mention): ?>
        <div class="mentions-block clearfix">
            <div class="photoBox">
                <img width="80px" height="80px" src="<?php echo $mention->creator_image_url;?>"
                    onerror="this.className +=' no-avatar'">
            </div>
            <div class="data pull-right">
                <div class="info-line clearfix">
                    <div class="author"><?php echo $mention->creator_name; ?></div>
                    <div class="pull-right date-time">
                        <?php echo date(lang('mentions_format'), $mention->created_at);?>
                    </div>
                </div>
                <p>
                    <?php echo $mention->parse_message_links(); ?>
                </p>
                <div class="clear"></div>
                <div class="action clearfix social-actions-tw">
                    <a class="back" data-id="<?php echo $mention->original_id; ?>" 
                        href="<?php echo site_url('social/activity/tweet'); ?>">
                    </a>
                    <?php $retwitted_class = $mention->other_field('retweeted') ? 'retweet-styled' : ''; ?>
                    <?php $retwitted_link = $retwitted_class
                        ? site_url('social/activity/unretweet/'. $mention->original_id) 
                        : site_url('social/activity/retweet/'. $mention->original_id);
                    ?>
                    <a class="link retweet <?php echo $retwitted_class; ?>" 
                        href="<?php echo $retwitted_link; ?>">
                    </a>
                    <?php $favorited_class = $mention->other_field('favorited') ? 'favorite-styled' : ''; ?>
                    <?php $favorited_link = $favorited_class 
                        ? site_url('social/activity/unfavorite/'. $mention->original_id) 
                        : site_url('social/activity/favorite/'. $mention->original_id);
                    ?>
                    <a class="star favorite <?php echo $favorited_class; ?>" 
                        href="<?php echo $favorited_link; ?>">
                    </a>
                </div>
                <?php if(isset($is_user_tweets)): ?>
                <a class="remove-tweet" href="javascript:void(0)"  
                    data-url="<?php echo site_url('social/activity/remove_tweet/' . $mention->original_id); ?>"
                >
                    Remove
                </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal -->
<div id="reply-window" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Enter Reply Text</h3>
    </div>
    <div class="modal-body">
        <textarea rows="5" cols="10" class="twitter_reply_textarea"></textarea>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" id="cancel-reply-area" aria-hidden="true">Cancel</button>
        <button class="btn btn-primary" id="reply" data-url="" >Send</button>
    </div>
</div>