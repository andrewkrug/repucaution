<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title">Influencers watch</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <!--<ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="" class="breadcrumbs_link">Web Radar</a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link">All mentioned</a>
                </li>
            </ul>-->
            <?php echo $this->template->block('app_breadcrumbs', 'layouts/block/application/breadcrumbs', array('menu' => 'customer.main')); ?>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-sm-8">
            <div class="date_range p-tb10">
                <div class="reportrange" >
                    <i class="fa fa-calendar"></i>
                    <span></span>

                    <b class="caret"></b>
                </div>
            </div>
        </div>
        <div class="col-sm-4">

            <?php echo form_dropdown('keyword', $keywords, $keyword, 'class="chosen-select"'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">

            <?php if (! $hasKeywords || empty($feed)): ?>
                <p class="large-size m-t20 p-b10 b-Bottom text_color">

                    <?php if( ! $hasKeywords): ?>
                        No keywords
                        <a href="<?php echo site_url('settings/mention_keywords'); ?>"
                           class=""
                            >
                            Go to Keywords settings
                        </a>
                    <?php elseif( empty($feed)): ?>
                        No mentions <?php if ( ! $hasRequested): ?>yet<?php endif; ?>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <div id="ajax-area" class="web_radar m-t20 pull_border ">
                    <!-- HERE GOES PARTICULAR SOCIAL BLOCK -->
                    <?php echo $feed; ?>
                    <!-- HERE GOES PARTICULAR SOCIAL BLOCK -->
                    <!-- Modal-->
                    <div id="reply-window" class="modal fade" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <div class="modal-body">
                                    <h4 id="myModalLabel" class="head_tab">Enter Reply Text</h4>
                                    <textarea rows="5" cols="70" class="twitter_reply_textarea"></textarea>
                                </div>
                                <div class="modal-footer clearfix">
                                    <div class="pull-right">
                                        <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href="">Cancel</a>
                                        <button type="button" id="reply" class="btn btn-save">Send</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>


        </div>
    </div>
</div>
</div>