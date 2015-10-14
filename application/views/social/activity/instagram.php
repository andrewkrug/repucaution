<div class="row-fluid">
    <div class="span12 box not-header radius">
        <div class="row-fluid">
            <div class="body span12">
                <?php echo $this->template->block('tabs', 'social/activity/blocks/tabs'); ?>
                <div id="ajax-area" class="main-list">
                        <?php echo $instagram_html; ?>
                 </div>
                <?php if(isset($data)): ?>
                    <div class="mediaBox">
                        <div class="row-fluid">
                            <div class="pginationBlock">
                                <a style="display: none" class="prev active" min_id="1"
                                   
                                    href="<?php echo site_url('social/activity/instagram'); ?>"
                                >
                                    &lt;&lt; Previous
                                </a>
                                <div class="pgBody">
                                    <span id="pages-counter">1</span>
                                </div>
								
                                <a class="next active"  max_id=""
                                    href="<?php echo site_url('social/activity/instagram'); ?>"
                                >
                                    Next &gt;&gt;
                                </a>
								
                                <input type="hidden" id="page_number" value="1">
                            </div>
                        </div>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>