<div id="EnterCode" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo site_url('settings/socialmedia/twitter_callback'); ?>" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 class="head_tab"><?= lang('pin_code') ?></h4>
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <input type="text" class="form-control" name="oauth_verifier" id="oauth_verifier">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <div class="pull-right">
                        <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('close') ?></a>
                        <button type="submit" class="btn btn-save"><?= lang('save') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>