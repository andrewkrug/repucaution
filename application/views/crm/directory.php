<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('crm') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
<div class="row">
    <div class="col-sm-1 col-md-2 col-lg-1">
        <p class="p-t10 strong-size text_color"><?= lang('directory') ?></p>
    </div>
    <form class="directory" action="" >
        <div class="col-sm-3 ">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="<?= lang('search_by_name') ?>" name="username" value="<?php echo $username; ?>" autocomplete="off"/>
                <ul class="search-user autocomplete"></ul>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="<?= lang('search_by_company') ?>" name="company"  value="<?php echo $company; ?>" autocomplete="off"/>
                <ul class="search-user autocomplete"></ul>
            </div>
        </div>
        <div class="col-sm-3">
            <button class="btn btn-save"><?= lang('search') ?></button>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-xs-12 m-t10">
        <div class="b-Bottom clearfix">
            <p class="text_color strong-size pull-sm-left"><?php echo $directory->username;?></p>
            <p class="pull-sm-right">
                <a href="<?php echo site_url('crm/edit/'.$directory->id); ?>" class="link m-r10"><i class="fa fa-pencil-square-o"></i> <?= lang('edit') ?></a>
                <a href="<?php echo site_url('crm/delete/'.$directory->id); ?>" class="remove_link link"><i class="fa fa-remove"></i> <?= lang('remove') ?></a>
            </p>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <table class="table">
            <tbody>
            <tr>
                <td>
                    <p class="gray-color bold"><?= lang('first_name') ?></p>
                </td>
                <td>
                    <p><?php echo $directory->firstname;?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="gray-color bold"><?= lang('last_name') ?></p>
                </td>
                <td>
                    <p><?php echo $directory->lastname;?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="gray-color bold"><?= lang('email') ?></p>
                </td>
                <td>
                    <p><a href="" class="link"><?php echo $directory->email;?></a></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="gray-color bold"><?= lang('company') ?></p>
                </td>
                <td>
                    <p><?php echo $directory->company;?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="gray-color bold"><?= lang('website') ?></p>
                </td>
                <td>
                    <p><a href="" class="link"><?php echo $directory->website;?></a></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="gray-color bold"><?= lang('notes') ?></p>
                </td>
                <td>
                    <p><?php echo $directory->notes;?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="gray-color bold"><?= lang('profile_link') ?></p>
                </td>
                <td>
                    <p class="strong-text">
                        <?php if ($directory->facebook_link) :?>
                            <a class="strong-text" href="<?php echo $directory->facebook_link;?>">
                                <i class="fa fa-facebook-square facebook-color"></i>
                            </a>
                        <?php endif;?>

                        <?php if ($directory->twitter_link) :?>
                            <a class="strong-text" href="<?php echo $directory->twitter_link;?>">
                                <i class="fa fa-twitter-square twitter-color"></i>
                            </a>
                        <?php endif;?>
                        <!--<i class="fa fa-google-plus-square google-color"></i>
                        <i class="fa fa-linkedin linkedin-color"></i>-->
                        <?php if ($directory->instagram_link) :?>
                            <a class="strong-text" href="<?php echo $directory->instagram_link;?>">
                                <i class="fa fa-instagram instagram-color"></i>
                            </a>
                        <?php endif;?>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 m-t10">
        <p class="text_color strong-size"><?= lang('social_activity') ?></p>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <ul class="nav nav-tabs settings_tab">
            <li class="setting_item auto <?php echo (empty($social)) ? 'active' : '';?>">
                <a class="setting_link" href="<?php echo site_url('crm/directory').'/'.$directory->id; ?>" >
                    <i class="ti-thumb-up"></i>
                    <?= lang('all_social') ?>
                </a>
            </li>
            <?php if ($existsSocials['facebook']) :?>
                <li class="setting_item auto <?php echo ($social == 'facebook') ? 'active' : '';?>">
                    <a class="setting_link" href="<?php echo site_url('crm/directory').'/'.$directory->id.'?social=facebook'?>">
                        <i class="ti-facebook"></i>
                        Facebook
                    </a>
                </li>
            <?php endif;?>
            <?php if ($existsSocials['twitter']) :?>
                <li class="setting_item auto <?php echo ($social == 'twitter') ? 'active' : '';?>">
                    <a class="setting_link" href="<?php echo site_url('crm/directory').'/'.$directory->id.'?social=twitter'?>">
                        <i class="ti-twitter"></i>
                        Twitter
                    </a>
                </li>
            <?php endif;?>
            <?php if ($existsSocials['instagram']) :?>
                <li class="setting_item auto <?php echo ($social == 'instagram') ? 'active' : '';?>">
                    <a class="setting_link" href="<?php echo site_url('crm/directory').'/'.$directory->id.'?social=instagram'?>">
                        <i class="ti-instagram"></i>
                        Instagram
                    </a>
                </li>
            <?php endif;?>
            <!--<li class="setting_item auto">
                <a class="setting_link" href="#linkedin" data-toggle="tab">
                    <i class="ti-linkedin"></i>
                    Linkedin
                </a>
            </li>
            <li class="setting_item auto">
                <a class="setting_link" href="#instagram" data-toggle="tab">
                    <i class="ti-instagram"></i>
                    Instagram
                </a>
            </li>-->
        </ul>
    </div>
</div>
<?php if( empty($feed)): ?>
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
        <div class="tab-content settings_content">
            <div class="tab-pane active">
                <div class="web_radar m-t20 pull_border" id="ajax-area">
                    <?php echo $feed;?>
                </div>
            </div>
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