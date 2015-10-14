<div class="web_comments">
    <div class="row">
        <div class="row">
            <div class="col-xs-12">
                <div class="comment_block dTable">

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <form class="comment-submit-form" method="POST"  action="<?php echo site_url('social/activity/facebook_comment/'.$_post['id']); ?>">
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
</div>