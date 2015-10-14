<!--<li>
    <div class="photo-comments">
        <img src="<?php /*echo $comment->from->profile_picture; */?>" alt="">
    </div>
    <div class="exposition">
        <div class="caption"><a href="http://instagram/<?php /*echo $comment->from->username;*/?>"><?php /*echo $comment->from->username;*/?></a></div>
        <p>
            <?php /*echo $comment->text; */?>
        </p>
        <div class="comment-panel">
            <div class="scoring">

               <div class="time">
                    <?php /*echo date('M d, Y h:i a', $comment->created_time);*/?>
                </div>
            </div>
            <?php /*if ($comment->from->id == $socializer->getInstanceId()) :*/?>
                <div class="remove-butt" data-url="<?php /*echo site_url('social/activity/instagram_remove_comment/'.$comment->id); */?>">Remove</div>
            <?php /*endif */?>
        </div><!-- /comment-panel
    </div>
</li>-->
<div class="dRow">
    <div class="dCell cellImg">
        <a href="">
            <img class="comment_avatar" src="<?php echo $comment->from->profile_picture; ?>" alt="">
        </a>
    </div>
    <div class="dCell">
        <a class="blue_color comment_title" href="https://instagram.com/<?php echo $comment->from->username;?>">
            <?php echo $comment->from->username;?>
        </a>
        <p class="comment_text">
            <?php echo $comment->text; ?>
        </p>
        <p class="comment_date">
            <cite class="gray-color fa fa-clock-o "></cite><?php echo date('M d, Y h:i a', $comment->created_time);?>
        </p>
        <?php if ($comment->from->id == $socializer->getInstanceId()) :?>
            <div class="remove-butt" data-url="<?php echo site_url('social/activity/instagram_remove_comment/'.$comment->id); ?>">Remove</div>
        <?php endif ?>
    </div>
</div>