<div class="row-fluid">
    <div class="span12 box">
        <div class="header span12">
            <span>SOCIAL MENTIONS</span>
        </div>
        <div class="row-fluid">
            <div class="body span12">
                <div class="row-fluid">
                    <div class="span12">
                        <ul class="media-list clearfix">
                            <li <?php if($social === 'facebook'): ?>class="active"<?php endif; ?>>
                                <a href="<?php echo site_url('social/mentions/facebook'); ?>">
                                    <i class="fb"></i> 
                                    Facebook 
                                    <span class="marker"></span>
                                </a>
                            </li>
                            <li <?php if($social === 'twitter'): ?>class="active"<?php endif; ?>>
                                <a href="<?php echo site_url('social/mentions/twitter'); ?>">
                                    <i class="tw"></i>
                                    Twitter 
                                    <span class="marker"></span>
                                </a>
                            </li>
							 <li <?php if($social === 'google'): ?>class="active"<?php endif; ?>>
                                <a href="<?php echo site_url('social/mentions/google'); ?>">
                                    <i class="fa fa-google-plus-square"></i>
                                    Google 
                                    <span class="marker"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php if ($has_access && $has_keywords && $has_requested): ?>
                    <fieldset class="formBox special no-border">
                        <div class="control-group" style="float: left">
                            <div class="control select_customer mention-keyword-dropdown">
                                <?php echo form_dropdown('keyword', $keywords, $keyword); ?>
                            </div>
                        </div>
                        <?php if ($has_access && $has_keywords && $has_requested): ?>
                            <div class="traffic_filter_area" style="float: right">
                                <input type="text" class="datepicker datepicker-from">
                                <input type="text" class="datepicker datepicker-to">
                                <input type="submit" class="black-btn filter filter-mentions" value="Apply">
                            </div>
                    <?php endif; ?>
                    </fieldset>
                <?php endif; ?>
                <?php if ( ! $has_access || ! $has_keywords || ! $mentions->exists()): ?>
                    <fieldset class="formBox social-mentions-errorbox" style="margin-bottom: 10px;">
                        <div class="title">
                            <?php if ( ! $has_access): ?>
                                <span class="message-error configure-error">
                                    <?php echo ucfirst($social); ?> not connected
                                </span>
                                <br/><br/>
                                <a href="<?php echo site_url('settings/socialmedia'); ?>" 
                                    class="configure-link"
                                >
                                    Go to Social Media settings
                                </a>
                            <?php elseif( ! $has_keywords): ?>
                                <span class="message-error configure-error">No keywords</span>
                                <br/><br/>
                                <a href="<?php echo site_url('settings/mention_keywords'); ?>" 
                                    class="configure-link"
                                >
                                    Go to Keywords settings
                                </a>
                            <?php elseif( ! $mentions->exists()): ?>
                                <span class="message-error configure-error">
                                    No mentions <?php if ( ! $has_requested): ?>yet<?php endif; ?>
                                </span>    
                            <?php endif; ?>
                        </div>  
                    </fieldset>
                <?php else: ?>

                    <!-- HERE GOES PARTICULAR SOCIAL BLOCK -->
                    <?php echo $this->template->block('_' . $social, 'social/mentions/' . $social); ?>
                    <!-- HERE GOES PARTICULAR SOCIAL BLOCK -->

                    <div class="row-fluid">
                        <div class="mentions-block pginationBlock clearfix">
                            <?php 
                                $prev_class = ($mentions->paged->has_previous) ? 'active' : '';
                                $prev_url = site_url('social/mentions/' . $social . '?page=' 
                                    . ($mentions->paged->current_page - 1))
                                    . $keyword_query_str;
                            ?>
                            <?php if ($prev_class): ?>
                                <a class="prev <?php echo $prev_class; ?>" href="<?php echo $prev_url; ?>">
                                    &lt;&lt; Previous
                                </a>
                            <?php else: ?>
                                <span class="prev">&lt;&lt; Previous</span>
                            <?php endif; ?>
                            <div class="pgBody">
                                <?php for($i = 1; $i <= $mentions->paged->total_pages; $i += 1): ?>
                                    <?php $page_url = site_url('social/mentions/' . $social 
                                        . '?page=' . $i . $keyword_query_str); ?>
                                    <?php if ($i == $mentions->paged->current_page): ?>
                                        <span id="pages-counter"><?php echo $i; ?></span>
                                    <?php else: ?>
                                        <a class="page" href="<?php echo $page_url; ?>">
                                            <span id="pages-counter"><?php echo $i; ?></span>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <?php 
                                $next_class = ($mentions->paged->has_next) ? 'active' : '';
                                $next_url = site_url('social/mentions/' . $social 
                                    . '?page=' . ($mentions->paged->current_page + 1))
                                    . $keyword_query_str;
                            ?>
                            <?php if ($next_class): ?>
                                <a class="prev <?php echo $next_class; ?>" href="<?php echo $next_url; ?>">
                                    Next &gt;&gt;
                                </a>
                            <?php else: ?>
                                <span class="prev">Next &gt;&gt;</span>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>