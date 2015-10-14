<!--<div class="span12 box">
    <div class="header span12">
        <span>CRM</span>
    </div>
    <div class="body contacts-block activity">
        <div class="filter-block clearfix">
            <span>Clients activity</span>
        </div><!-- /filter-block

        <?php /*if (! $hasDirectories || empty($feed)): */?>
            <fieldset class="formBox social-mentions-errorbox" style="margin-bottom: 10px;">
                <div class="title">
                    <?php /*if( ! $hasDirectories): */?>
                        <span class="message-error configure-error">No directories</span>
                        <br/><br/>
                        <a href="<?php /*echo site_url('crm/add'); */?>"
                           class="configure-link"
                            >
                            Add directory
                        </a>
                    <?php /*elseif( empty($feed)): */?>
                        <span class="message-error configure-error">
                                        No activities <?php /*if ( ! $hasRequested): */?>yet<?php /*endif; */?>
                                    </span>
                    <?php /*endif; */?>
                </div>
            </fieldset>
        <?php /*else: */?>
            <div id="ajax-area" class="main-list">
                <!-- HERE GOES PARTICULAR SOCIAL BLOCK -->
                <?php /*echo $feed; */?>
                <!-- HERE GOES PARTICULAR SOCIAL BLOCK
                <!-- Modal
                <div id="reply-window" class="modal radar hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 id="myModalLabel">Enter Reply Text</h3>
                    </div>
                    <div class="modal-body">
                        <textarea rows="5" cols="10" class="twitter_reply_textarea"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button class="white-btn radar" data-dismiss="modal" id="cancel-reply-area" aria-hidden="true">Cancel</button>
                        <button class="black-btn" id="reply" data-url="" >Send</button>
                    </div>
                </div>
            </div>
        <?php /*endif; */?>

    </div>
</div>-->
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('crm') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
<div class="row">
    <div class="col-xs-12">
        <p class="strong-size text_color"><?= lang('client_activity') ?></p>
    </div>
</div>
<?php if (! $hasDirectories || empty($feed)): ?>
    <div class="row">
        <div class="col-xs-12">
            <p class="large-size m-t20 p-b10 b-Bottom text_color">
                <?= lang('no_activities') ?>
            </p>
        </div>
    </div>
<?php else:?>
    <div class="row">
        <div class="col-xs-12">
            <div class="web_radar m-t20 pull_border" id="ajax-area">
                <?php echo $feed;?>
            </div>
        </div>
    </div>
<?php endif;?>
</div>
<div id="reply-window" class="modal fade" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4 id="myModalLabel" class="head_tab"><?= lang('enter_reply_text') ?></h4>
                <textarea rows="5" cols="70" class="twitter_reply_textarea"></textarea>
            </div>
            <div class="modal-footer clearfix">
                <div class="pull-right">
                    <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('cancel') ?></a>
                    <button type="button" id="reply" class="btn btn-save"><?= lang('send') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>