<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title">Settings</h1>
        </div>
    </div>
</div>
<div class="main_block">
<div class="row">
    <div class="col-xs-12">
        <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
        <ul class="nav nav-tabs settings_tab">
            <li class="setting_item active">
                <a class="setting_link" href="#personal_settings" data-toggle="tab">
                    <i class="ti-user"></i>
                    Personal Settings
                </a>
            </li>
            <li class="setting_item">
                <a class="setting_link" href="#directory_settings" data-toggle="tab">
                    <i class="ti-settings"></i>
                    Directory Settings
                </a>
            </li>
            <li class="setting_item">
                <a class="setting_link" href="#places_keywords" data-toggle="tab">
                    <i class="ti-google"></i>
                    Google Places Keywords
                </a>
            </li>
            <li class="setting_item">
                <a class="setting_link" href="#social_media" data-toggle="tab">
                    <i class="ti-instagram"></i>
                    Social Media
                </a>
            </li>
            <li class="setting_item">
                <a class="setting_link" href="#social_keywords" data-toggle="tab">
                    <i class="ti-layers-alt"></i>
                    Social Keywords
                </a>
            </li>
            <li class="setting_item">
                <a class="setting_link" href="#analytics" data-toggle="tab">
                    <i class="ti-pulse"></i>
                    Analytics
                </a>
            </li>
            <li class="setting_item">
                <a class="setting_link" href="#rss" data-toggle="tab">
                    <i class="ti-rss-alt"></i>
                    Rss
                </a>
            </li>
            <li class="setting_item">
                <a class="setting_link" href="#collaboration" data-toggle="tab">
                    <i class="ti-id-badge"></i>
                    Collaboration Team
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="row">
<div class="col-xs-12">
<div class="tab-content settings_content">
<div class="tab-pane active" id="personal_settings">
    <h4 class="head_tab">Personal Settings</h4>
    <form action="<?php echo site_url('settings/personal'); ?>" method="POST">
    <div class="row">
        <div class="col-sm-4">
            <p class="text_color strong-size">Email *</p>
            <div class="form-group">
                <input class="form-control" class="disabled" name="email" value="<?php echo $email; ?>" disabled/>
            </div>
        </div>
        <div class="col-sm-4">
            <p class="text_color strong-size">First Name *</p>
            <div class="form-group">
                <input class="form-control" value="<?php echo $first_name;?>" name="first_name"/>
            </div>
        </div>
        <div class="col-sm-4">
            <p class="text_color strong-size">Last Name *</p>
            <div class="form-group">
                <input class="form-control" value="<?php echo $last_name;?>" name="last_name"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <p class="text_color strong-size">Old Password *</p>
            <div class="form-group">
                <input class="form-control" type="password" name="old_password"/>
            </div>
        </div>
        <div class="col-sm-4">
            <p class="text_color strong-size">New Password *</p>
            <div class="form-group">
                <input class="form-control" type="password" name="new_password"/>
            </div>
        </div>
        <div class="col-sm-4">
            <p class="text_color strong-size">Confirm New Password *</p>
            <div class="form-group">
                <input class="form-control" type="password" name="confirm_password"/>
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-xs-12">
                <button class="btn btn-save m-tb20 pull-right">Save</button>
            </div>
        </div>
    </form>
</div>
<div class="tab-pane" id="directory_settings">
    <?php echo $this->template->block('directory_settings', '/settings/blocks/directories', array(
                                                                                                    'is_notified' => $is_notified,
                                                                                                    'parsers' =>  $parsers,
                                                                                                    'directories' => $directories,
                                                                                                    'user_directories' => $user_directories,
                                                                                                    'receive_emails' => $receive_emails
                                                                                                    )

    );?>
</div>
<div class="tab-pane" id="places_keywords">
    <?php echo $this->template->block('keywords_settings', '/settings/blocks/keywords', array(
                                                                                                'address_id' => $address_id,
                                                                                                'address_name' => $address_name,
                                                                                                'keywords_names' => $keywords_names,
                                                                                                'keywords_count' => $keywords_count,
                                                                                                )

    );?>
</div>
<div class="tab-pane" id="social_media">
    <?php echo $this->template->block('socialmedia_settings', '/settings/blocks/socialmedia', array(
            'linkedin_token' => $linkedin_token,
            'facebook_token' => $facebook_token,
            'twitter_token' => $twitter_token,
            'youtube_token' => $youtube_token,
            'google_token' => $google_token,
            'instagram_token' => $instagram_token,
            'timezones' => $timezones,
            'current_timezone' => $current_timezone
        )

    );?>
</div>
<div class="tab-pane" id="social_keywords">
    <?php echo $this->template->block('mention_keywords_settings', '/settings/blocks/mention_keywords', array(
        'keywords' => $keywords,
        'config_count' => $config_count
        )

    );?>
</div>
<div class="tab-pane" id="analytics">
    <h4 class="head_tab">Analytics Details</h4>
    <div class="row">
        <div class="col-sm-6">
            <p class="text_color strong-size">Google Analytics </p>
            <p class="black smallText">Select your Google Analytics profile to get statistics</p>
        </div>
        <div class="col-sm-6">
            <div class="pull-sm-right">
                <button class="btn btn-add">Logout</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="b-Top clearfix m-b10 m-t20 p-t20">
                <div class="pull-sm-right">
                    <button class="btn btn-save">Select</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane" id="rss">
    <h4 class="head_tab">Rss</h4>
    <p class="text_color strong-size">
        Select options
    </p>
    <div class="row custom-form">
        <div class="col-xs-12">
            <label class="cb-radio" data-show=".industry" data-hide=".feeds">
                <input type="radio" name='radio-group'>
                Industry
            </label>
            <div class="row industry is-hidden">
                <div class="col-sm-5">
                    <select class="chosen-select">
                        <option value="">Apple Top 10 Songs</option>
                        <option value="">Forbes Popular Stories</option>
                        <option value="">HP News</option>
                        <option value="">Oracle Corporate News</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-save">Save</button>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <label class="cb-radio" data-show=".feeds" data-hide=".industry">
                <input type="radio" name='radio-group'>
                Custom RSS Feeds
            </label>
            <div class="row feeds is-hidden">
                <div class="col-xs-12">
                    <p class="black strong-size">
                        Add new RSS Feed
                    </p>
                </div>
                <div class="col-sm-5">
                    <div class="row hidden more_block">
                        <div class="col-xs-12">
                            <p class="text_color strong-size">Title
                                <a href="" class="pull-right link remove_more">Remove</a>
                            </p>
                            <div class="form-group">
                                <input type="text" class="form-control">
                            </div>
                            <p class="text_color strong-size">Link Url</p>
                            <div class="form-group">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <p class="text_color strong-size">Title</p>
                            <div class="form-group">
                                <input type="text" class="form-control">
                            </div>
                            <p class="text_color strong-size">Link Url</p>
                            <div class="form-group">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <button class="btn btn-add pull-left more-link">+ One more</button>
                        <button class="btn btn-save pull-right">Add all to feed</button>
                    </div>
                </div>
                <div class="col-xs-12 p-t15">
                    <p class="text_color strong-size">List of RSS</p>
                    <p class="black"><span class="bold">CB</span> http://clickbrain.com/feed</p>
                    <a href="" class="link" data-toggle="modal" data-target=".modal">Remove</a>
                    <p class="black p-t10"><span class="bold">Menshealth</span> http://www.menshealth.com/events-promotions/washpofeed</p>
                    <a href="" class="link" data-toggle="modal" data-target=".modal">Remove</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane" id="collaboration">
    <h4 class="head_tab">Collaboration Team</h4>
    <div class="row">
        <div class="col-xs-12">
            <a href="" class="link invite_user">Invite</a>
        </div>
    </div>
    <div class="row invite_block m-t20">
        <div class="col-xs-12">
            <div class="input-group form-group">
                <input type="text" class="form-control" placeholder="Enter email" data-role="tagsinput">
								<span class="input-group-btn">
									<button class="btn btn-save" type="submit">Save</button>
								</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 m-t10">
            <p class="large-size text_color">
                No users
            </p>
        </div>
    </div>
</div>
</div>
</div>
</div>
<!--<div class="row">
    <div class="col-xs-12">
        <button class="btn btn-save m-tb20 pull-right">Save</button>
    </div>
</div>-->
</div>

<div class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <h4 class="head_tab">Remove feed</h4>
                <p class="black">Do you really want to remove Rss Feed <span class="bold">CB ( http://clickbrain.com/feed )</span> from your feeds list?</p>
            </div>
            <div class="modal-footer clearfix">
                <div class="pull-right">
                    <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href="">Close</a>
                    <button type="button" class="btn btn-save">Remove</button>
                </div>
            </div>
        </div>
    </div>
</div>