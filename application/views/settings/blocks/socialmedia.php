<!--<div class="span12 box">
<div class="header span12">
    <span>Social Media Settings</span>
</div>
<div class="row-fluid">
    <div class="body span12">
        <form method="POST" action="<?php /*echo site_url('settings/socialmedia/save_facebook_preferences'); */?>">
            <fieldset class="formBox special">
                <div class="title">Timezones <br/><span class="dop">Select your Time zone</span></div>
                <div class="section">
                    <div class="control-group">
                        <div class="controls clearfix">
                            <div class="control select_customer inline">
                                <select class="select_block" id="name-timezone"
                                        data-height="200" data-width="300"
                                    >
                                    <?php /*foreach($timezones as $_timezone_name => $_timezone_value): */?>
                                        <?php
/*                                        // use urlencode, because string has "+" sign and
                                        // http://stackoverflow.com/a/7410522
                                        $_timezone_combined = urlencode($_timezone_value . '^'
                                            . $_timezone_name);
                                        */?>
                                        <option class="podcast"
                                            <?php
/*                                            // use urlencode, because string has "+" sign and
                                            // http://stackoverflow.com/a/7410522
                                            echo (isset($current_timezone)
                                                && (urlencode($current_timezone) == $_timezone_combined))
                                                ? 'selected="selected"'
                                                : '';
                                            */?>
                                                value="<?php /*echo $_timezone_combined; */?>"
                                            >
                                            <?php /*echo $_timezone_name; */?>
                                        </option>
                                    <?php /*endforeach; */?>
                                </select>
                            </div><!-- /select_custom--
                            <input class="black-btn inline special" type="submit"
                                   id="save-timezone" value="Save"
                                >
                        </div>
                    </div>
                </div>
                <div class="title">
                    Facebook gateway <br/>
                    <span class="dop">Setup your Facebook account</span>
                </div>
                <div class="section">
                    <div class="control-group">
                        <?php /*if($facebook_token): */?>
                            <div class="controls clearfix">
                                <div class="control select_customer inline">
                                    <select class="select_block" name="fan_page_id">
                                        <?php /*if(!$selected_fanpage_id): */?>
                                            <option class="podcast" value="0">Please select fanpage</option>
                                        <?php /*endif; */?>
                                        <?php /*foreach( $fb_pages as $_page ): */?>
                                            <?php
/*                                            $selected = ($selected_fanpage_id == $_page['id'])
                                                ? 'selected="selected'
                                                : '';
                                            */?>
                                            <option class="podcast" <?php /*echo $selected */?>
                                                    value="<?php /*echo $_page['id']; */?>"
                                                >
                                                <?php /*echo $_page['name']; */?>
                                            </option>
                                        <?php /*endforeach; */?>
                                    </select>
                                </div><!-- /select_custom--
                                <input class="black-btn inline special" type="submit" value="Save">
                            </div>
                            <a href="<?php /*echo site_url('settings/socialmedia/facebook_logout');*/?>">
                                Logout from Facebook
                            </a>
                        <?php /*else: */?>
                            <div class="controls">
                                <a href="<?php /*echo site_url('settings/socialmedia/facebook');*/?>"
                                   class="fb_butt"
                                    >
                                </a>
                            </div>
                        <?php /*endif; */?>

                    </div>
                </div>

                <div class="title">
                    Twitter gateway <br/>
                    <span class="dop">Setup your Twitter account</span>
                </div>
                <div class="section ">
                    <div class="control-group">
                        <div class="controls">
                            <?php /*if($twitter_token): */?>
                                <a href="<?php /*echo site_url('settings/socialmedia/twitter_logout');*/?>">
                                    Logout from Twitter
                                </a>
                            <?php /*else: */?>
                                <a href="<?php /*echo site_url('settings/socialmedia/twitter');*/?>" class="tw_butt"></a>
                                <a class="twitter-btn enter-pin">Enter pin code.</a>
                            <?php /*endif; */?>
                        </div>
                    </div>
                </div>

                <div class="title">
                    Youtube gateway <br/>
                    <span class="dop">Setup your Youtube account</span>
                </div>
                <div class="section last">
                    <div class="control-group">
                        <div class="controls">
                            <?php /*if($youtube_token): */?>
                                <a href="<?php /*echo site_url('settings/socialmedia/youtube_logout');*/?>">
                                    Logout from Youtube
                                </a>
                            <?php /*else: */?>
                                <a href="<?php /*echo site_url('settings/socialmedia/youtube');*/?>" class="yt_butt">                                </a>
                            <?php /*endif; */?>
                        </div>
                    </div>
                </div>

                <div class="title">
                    Linkedin gateway <br/>
                    <span class="dop">Setup your Linkedin account</span>
                </div>
                <div class="section last">
                    <div class="control-group">
                        <div class="controls">
                            <?php /*if($linkedin_token): */?>
                                <a href="<?php /*echo site_url('settings/socialmedia/linkedin_logout');*/?>">
                                    Logout from Linkedin
                                </a>
                            <?php /*else: */?>
                                <a href="<?php /*echo site_url('settings/socialmedia/linkedin');*/?>">
                                    <button class="social-btn linkedin" type="button">
                                        <i class="icon"></i>
                                        <span class="sep"></span>
                                        Connect Linkedin
                                    </button>
                                </a>
                            <?php /*endif; */?>
                        </div>
                    </div>
                </div>

                <div class="title">
                    Google gateway <br/>
                    <span class="dop">Setup your Google account</span>
                </div>
                <div class="section last">
                    <div class="control-group">
                        <div class="controls">
                            <?php /*if($google_token): */?>
                                <a href="<?php /*echo site_url('settings/socialmedia/google_logout');*/?>">
                                    Logout from Google
                                </a>
                            <?php /*else: */?>
                                <div id="gSignInWrapper">
                                    <a href="<?php /*echo site_url('settings/socialmedia/google');*/?>">
                                        <div id="customBtn" class="customGPlusSignIn">
                                            <span class="icon"></span>
                                            <span class="buttonText"> Configure your account</span>
                                        </div>
                                    </a>
                                </div>

                            <?php /*endif; */?>
                        </div>
                    </div>
                </div>

                <div class="title">
                    Instagram gateway <br/>
                    <span class="dop">Setup your Instagram account</span>
                </div>
                <div class="section last">
                    <div class="control-group">
                        <div class="controls">
                            <?php /*if($instagram_token): */?>
                                <a href="<?php /*echo site_url('settings/socialmedia/instagram_logout');*/?>">
                                    Logout from Instagram
                                </a>
                            <?php /*else: */?>
                                <a href="<?php /*echo site_url('settings/socialmedia/instagram');*/?>">
                                    <button class="social-btn instagram" type="button">
                                        <i class="icon"></i>
                                        <span class="sep"></span>
                                        Connect Instagram
                                    </button>
                                </a>

                            <?php /*endif; */?>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
</div>
--><?php /*echo $this->template->block('twitter_pin_modal', 'blocks/modal/settings/socialmedia/twitter'); */?>

<h4 class="head_tab">Social Media</h4>
<div class="row">
    <div class="col-sm-6">
        <div class="row custom-form">
            <div class="col-sm-6">
                <p class="text_color large-size">Facebook gateway</p>
                <p class="black smallText">Setup your Facebook account</p>
            </div>
            <!--<div class="col-sm-6 sm-right">
                <label class="cb-checkbox text-size">
                    <input type="checkbox">
                    Auto-follow Facebook
                </label>
            </div>-->
        </div>
        <?php if($facebook_token): ?>
            <div class="row">
                <form method="POST" action="<?php echo site_url('settings/socialmedia/save_facebook_preferences'); ?>">
                    <div class="col-sm-9">
                        <div class="form-group">
                            <select class="chosen-select" style="display: none;" name="fan_page_id">
                                <?php if(!$selected_fanpage_id): ?>
                                <option value="0">Please select fanpage</option>
                                <?php endif; ?>
                                <?php foreach( $fb_pages as $_page ): ?>
                                <?php
                                                                            $selected = ($selected_fanpage_id == $_page['id'])
                                                                                ? 'selected="selected'
                                                                                : '';
                                                                            ?>
                                <option <?php echo $selected ?>
                                        value="<?php echo $_page['id']; ?>"
                                    >
                                    <?php echo $_page['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-save m-b15">Save</button>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/facebook_logout');?>" class="link">Logout from Facebook</a>
                </div>
            </div>
        <?php else: ?>
        <div class="row">
            <div class="col-xs-12">
                <a href="<?php echo site_url('settings/socialmedia/facebook');?>">
                <button class="btn btn-facebook">Add your account</button>
                </a>
            </div>
        </div>
        <?php endif; ?>

    </div>
    <div class="col-sm-6">
        <div class="row custom-form">
            <div class="col-sm-6">
                <p class="text_color large-size">Twitter gateway</p>
                <p class="black smallText">Setup your Twitter account</p>
            </div>
            <div class="col-sm-6 sm-right">
                <label class="cb-checkbox text-size">
                    <input type="checkbox">
                    Auto-follow Twitter
                </label>
            </div>
        </div>
        <?php if($twitter_token): ?>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/twitter_logout');?>" class="link">Logout from Twitter</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/facebook');?>">
                        <button class="btn btn-twitter">Add your account</button>
                    </a>
                </div>
            </div>
        <?php endif;?>

    </div>
</div>
<div class="row">
    <div class="col-sm-6 custom-form">
        <div class="row">
            <div class="col-sm-6">
                <p class="text_color large-size m-t20">Youtube gateway</p>
                <p class="black smallText">Setup your Youtube account</p>
            </div>

        </div>
        <?php if($youtube_token):?>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/youtube_logout');?>" class="link">Logout from Youtube</a>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/youtube');?>">
                        <button class="btn btn-youtube">Add your account</button>
                    </a>
                </div>
            </div>
        <?php endif;?>

    </div>

    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-6">
                <p class="text_color large-size m-t20">Instagram gateway</p>
                <p class="black smallText">Setup your Instagram account</p>
            </div>
        </div>
        <?php if($instagram_token):?>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/instagram_logout');?>" class="link">Logout from Instagram</a>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/instagram');?>">
                        <button class="btn btn-instagram">Add your account</button>
                    </a>
                </div>
            </div>
        <?php endif;?>

    </div>
</div>
<div class="row">
    <div class="col-sm-6 custom-form">
        <div class="row">
            <div class="col-sm-6">
                <p class="text_color large-size m-t20">Linkedin gateway</p>
                <p class="black smallText">Setup your Linkedin account</p>
            </div>
        </div>
        <?php if($linkedin_token):?>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/linkedin_logout');?>" class="link">Logout from Linkedin</a>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/linkedin');?>">
                        <button class="btn btn-linkedin">Add your account</button>
                    </a>
                </div>
            </div>
        <?php endif;?>

    </div>
    <div class="col-sm-6 custom-form m-b20">
        <div class="row">
            <div class="col-sm-6">
                <p class="text_color large-size m-t20">Google gateway</p>
                <p class="black smallText">Setup your Google account</p>
            </div>
        </div>
        <?php if($google_token):?>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/google_logout');?>" class="link">Logout from Google</a>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-xs-12">
                    <a href="<?php echo site_url('settings/socialmedia/google');?>">
                        <button class="btn btn-google">Add your account</button>
                    </a>
                </div>
            </div>
        <?php endif;?>

    </div>
</div>
<div class="row">
    <div class="col-sm-8">
        <div class="row">
            <div class="col-xs-12">
                <p class="text_color large-size">Timezones</p>
                <p class="black smallText">Select your Time zone</p>
            </div>
        </div>
        <div class="row" id="timezone-section">
            <div class="col-sm-9">
                <div class="form-group">
                    <select class="chosen-select" id="name-timezone" >
                        <?php foreach($timezones as $_timezone_name => $_timezone_value): ?>
                        <?php
                                                                // use urlencode, because string has "+" sign and
                                                                // http://stackoverflow.com/a/7410522
                                                                $_timezone_combined = urlencode($_timezone_value . '^'
                                                                    . $_timezone_name);
                                                                ?>
                        <option
                            <?php
                                                                        // use urlencode, because string has "+" sign and
                                                                        // http://stackoverflow.com/a/7410522
                                                                        echo (isset($current_timezone)
                                                                            && (urlencode($current_timezone) == $_timezone_combined))
                                                                            ? 'selected="selected"'
                                                                            : '';
                                                                        ?>
                                value="<?php echo $_timezone_combined; ?>"
                            >
                            <?php echo $_timezone_name; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <button class="btn btn-save m-b15" id="save-timezone">Save</button>
            </div>
        </div>
    </div>
</div>