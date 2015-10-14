<!--<div class="inComment">
    <ul class="comment-list child" >
    </ul>
    <div class="new-comment" style=" display: none">
        <form class="comment-submit-form" method="POST"  action="<?php /*echo site_url('social/activity/instagram_comment/'.$_post['id']); */?>">
            <fieldset>
                <input type="hidden" name="key" value="<?php /*echo $_post['id'];*/?>"/>
                <textarea name="message" placeholder="Write a comment ..."></textarea>
                <input type="submit" value="Post comment">
            </fieldset>
        </form>
    </div>
</div>-->
<div class="web_comments">
    <div class="row">
        <div class="col-xs-12">
            <div class="comment_block dTable">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <form class="comment-submit-form" method="POST"  action="<?php echo site_url('social/activity/instagram_comment/'.$_post['id']); ?>">
                <div class="form-group">
                    <textarea rows="5" name="message" class="form-control" placeholder="Write a comment"></textarea>
                    <input type="hidden" name="key" value="<?php echo $_post['id'];?>"/>
                </div>
                <div class="pull-right">
                    <input type="submit" class="btn btn-save" value="Post comment"/>
                </div>
            </form>
        </div>
    </div>
</div>