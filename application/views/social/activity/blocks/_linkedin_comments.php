<div class="lnComment web_comments">
    <ul class="comment-list child" >
    </ul>
    <div class="new-comment form-group" style=" display: none">
        <form class="comment-submit-form" method="POST"  action="<?php echo site_url('social/activity/linkedin_comment'); ?>">
            <fieldset>
                <input type="hidden" name="key" value="<?php echo $_post->original_id;?>"/>
                <textarea name="message" class="form-control" placeholder="Write a comment ..."></textarea>
                <input type="submit" class="form-control" value="Post comment">
            </fieldset>
        </form>
    </div>
</div>