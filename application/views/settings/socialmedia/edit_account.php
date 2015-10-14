<?php
/**
 * @var Access_token $token
 * @var array $pages
 * @var integer $selected_fanpage_id
 * @var array $available_configs
 * @var array $not_display_configs
 * @var array $not_display_configs_values
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_social_media_edit_account') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="<?php echo site_url('settings');?>" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item">
                    <a href="<?php echo site_url('settings/socialmedia');?>" class="breadcrumbs_link"><?= lang('settings_social_media') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_social_media_edit_account') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <h4 class="edit-profile-title"><i class="fa fa-<?= $token->type ?>"></i> <?= $token->name ?></h4>
        </div>
    </div>
    <form action="<?php echo site_url('settings/socialmedia/edit_account/'.$token->id);?>" method="post">
        <div class="row custom-form edit_profile">
            <?php if($token->type == 'facebook') : ?>
                <div class="col-sm-6">
                    <div class="choose_account account_<?= $token->type ?>">
                        <h3 class="choose_account_title">
                            <i class="fa fa-<?= $token->type ?>"></i> <?= lang('social_funpage', [ucfirst($token->type)]) ?>
                        </h3>
                        <ul class="choose_account_list">
                            <?php foreach($pages as $page) : ?>
                                <li class="choose_account_list_item">
                                    <label class="cb-radio w-100">
                                        <input
                                            type="radio"
                                            name="page_group"
                                            <?= ($page['id'] == $selected_fanpage_id) ? ' checked="checked"' : '' ?>
                                            value="<?= $page['id'] ?>"
                                            >
                                        <div class="dTable">
                                            <div class="dRow">
                                                <div class="dCell">
                                                    <h3 class="choose_account_list_name"><?= $page['name'] ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            <?php if(!empty($available_configs)) : ?>
                <div class="col-sm-<?= ($token->type == 'facebook') ? '6' : '12' ?>">
                    <div class="choose_account account_<?= $token->type ?>">
                        <h3 class="choose_account_title"><i class="fa fa-<?= $token->type ?>"></i> <?= lang('social_settings', [ucfirst($token->type)]) ?></h3>
                        <?php foreach($available_configs as $available_config) : ?>
                            <?php
                                if(in_array($available_config['key'], $not_display_configs)) {
                                    continue;
                                }
                            ?>
                            <?php if($available_config['type'] == 'text') : ?>
                                <div class="form-group">
                                    <p class="text_color"><?= lang($available_config['key']) ?></p>
                                    <textarea
                                        rows="5"
                                        name="config[<?= $available_config['key'] ?>]"
                                        class="form-control"><?= $available_config['value'] ?></textarea>
                                </div>
                            <?php elseif($available_config['type'] == 'number') : ?>
                                <div class="form-group quantity-form">
                                    <p class="text_color"><?= lang($available_config['key']) ?></p>
                                    <input
                                        type="text"
                                        class="form-control quantity"
                                        placeholder="<?= lang('enter_number') ?>"
                                        name="config[<?= $available_config['key'] ?>]"
                                        value="<?= $available_config['value'] ?>"
                                    >
                                    <i class="fa fa-angle-up decrementBtn"></i>
                                    <i class="fa fa-angle-down incrementBtn"></i>
                                </div>
                            <?php else: ?>
                                <label class="cb-checkbox w-100" data-toggle="#<?= $available_config['key'] ?>-config">
                                    <input
                                        type="checkbox"
                                        name="config[<?= $available_config['key'] ?>]"
                                        <?= ($available_config['value']) ? 'checked="checked"' : '' ?>
                                        >
                                    <?= lang($available_config['key']) ?>
                                </label>
                                <div class="row <?= ($available_config['value']) ? '' : 'is-hidden' ?>" id="<?= $available_config['key'] ?>-config">
                                    <div class="col-xs-12">
                                        <?php if($available_config['key'] == 'auto_send_welcome_message') : ?>
                                            <?php if(isset($not_display_configs_values['welcome_message_text'])) : ?>
                                                <div class="form-group">
                                                    <p class="text_color"><?= lang('welcome_message_text') ?></p>
                                                <textarea
                                                    rows="5"
                                                    name="config[welcome_message_text]"
                                                    class="form-control"><?= $not_display_configs_values['welcome_message_text']['value'] ?></textarea>
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif($available_config['key'] == 'auto_unfollow') : ?>
                                            <?php if(isset($not_display_configs_values['days_before_unfollow'])) : ?>
                                                <div class="form-group quantity-form">
                                                    <p class="text_color"><?= lang('days_before_unfollow') ?></p>
                                                    <input
                                                        type="text"
                                                        class="form-control quantity"
                                                        placeholder="<?= lang('enter_number') ?>"
                                                        name="config[days_before_unfollow]"
                                                        value="<?= $not_display_configs_values['days_before_unfollow']['value'] ?>"
                                                        >
                                                    <i class="fa fa-angle-up decrementBtn"></i>
                                                    <i class="fa fa-angle-down incrementBtn"></i>
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif($available_config['key'] == 'auto_follow_users_by_search') : ?>
                                            <?php if(isset($not_display_configs_values['max_daily_auto_follow_users_by_search'])) : ?>
                                                <div class="form-group quantity-form">
                                                    <p class="text_color"><?= lang('max_daily_auto_follow_users_by_search') ?></p>
                                                    <input
                                                        type="text"
                                                        class="form-control quantity"
                                                        placeholder="<?= lang('enter_number') ?>"
                                                        name="config[max_daily_auto_follow_users_by_search]"
                                                        value="<?= $not_display_configs_values['max_daily_auto_follow_users_by_search']['value'] ?>"
                                                        >
                                                    <i class="fa fa-angle-up decrementBtn"></i>
                                                    <i class="fa fa-angle-down incrementBtn"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="validate blue m-b10">
                                                <div class="validateRow">
                                                    <div class="validateCell">
                                                        <i class="note">!</i>
                                                    </div>
                                                    <div class="validateCell">
                                                        <div class="pull-left">
                                                            <p>
                                                                <?= lang('auto_follow_users_by_search_info') ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if(isset($not_display_configs_values['age_of_account'])) : ?>
                                                <p class="text_color"><?= lang('age_of_account') ?></p>
                                                <select class="chosen-select" name="config[age_of_account]">
                                                    <option value="" <?= ($not_display_configs_values['age_of_account']['value'] == '') ? 'selected' : ''?>>
                                                        <?= lang('age_of_account_any') ?>
                                                    </option>
                                                    <option value="0,3" <?= ($not_display_configs_values['age_of_account']['value'] == '0,3') ? 'selected' : '' ?>>
                                                        <?= lang('age_of_account_less_then_3_months') ?>
                                                    </option>
                                                    <option value="3,6" <?= ($not_display_configs_values['age_of_account']['value'] == '3,6') ? 'selected' : '' ?>>
                                                        <?= lang('age_of_account_3_6_months') ?>
                                                    </option>
                                                    <option value="6,12" <?= ($not_display_configs_values['age_of_account']['value'] == '6,12') ? 'selected' : '' ?>>
                                                        <?= lang('age_of_account_6_12_months') ?>
                                                    </option>
                                                    <option value="12,36" <?= ($not_display_configs_values['age_of_account']['value'] == '12,36') ? 'selected' : '' ?>>
                                                        <?= lang('age_of_account_12_36_months') ?>
                                                    </option>
                                                    <option value="36" <?= ($not_display_configs_values['age_of_account']['value'] == '36') ? 'selected' : '' ?>>
                                                        <?= lang('age_of_account_more_than_36_months') ?>
                                                    </option>
                                                </select>
                                            <?php endif; ?>
                                            <?php if(isset($not_display_configs_values['number_of_tweets'])) : ?>
                                                <p class="text_color"><?= lang('number_of_tweets') ?></p>
                                                <select class="chosen-select" name="config[number_of_tweets]">
                                                    <option value="" <?= ($not_display_configs_values['number_of_tweets']['value'] == '') ? 'selected' : ''?>>
                                                        <?= lang('number_of_tweets_any') ?>
                                                    </option>
                                                    <option value="0,100" <?= ($not_display_configs_values['number_of_tweets']['value'] == '0,100') ? 'selected' : ''?>>
                                                        <?= lang('number_of_tweets_less_than_100') ?>
                                                    </option>
                                                    <option value="101,1000" <?= ($not_display_configs_values['number_of_tweets']['value'] == '101,1000') ? 'selected' : ''?>>
                                                        101-1000
                                                    </option>
                                                    <option value="1001,5000" <?= ($not_display_configs_values['number_of_tweets']['value'] == '1001,5000') ? 'selected' : ''?>>
                                                        1001-5000
                                                    </option>
                                                    <option value="5001,15000" <?= ($not_display_configs_values['number_of_tweets']['value'] == '5001,15000') ? 'selected' : ''?>>
                                                        5001-15000
                                                    </option>
                                                    <option value="15001,30000" <?= ($not_display_configs_values['number_of_tweets']['value'] == '15001,30000') ? 'selected' : ''?>>
                                                        15001-30000
                                                    </option>
                                                    <option value="30000" <?= ($not_display_configs_values['number_of_tweets']['value'] == '30000') ? 'selected' : ''?>>
                                                        <?= lang('number_of_tweets_more_than_30000') ?>
                                                    </option>
                                                </select>
                                            <?php endif; ?>
                                        <?php elseif($available_config['key'] == 'auto_favourite') : ?>
                                            <?php if(isset($not_display_configs_values['auto_favourite_min_favourites_count'])) : ?>
                                                <div class="form-group quantity-form">
                                                    <p class="text_color"><?= lang('min_favourites_count') ?></p>
                                                    <input
                                                        type="text"
                                                        class="form-control quantity"
                                                        placeholder="<?= lang('enter_number') ?>"
                                                        name="config[auto_favourite_min_favourites_count]"
                                                        value="<?= $not_display_configs_values['auto_favourite_min_favourites_count']['value'] ?>"
                                                        >
                                                    <i class="fa fa-angle-up decrementBtn"></i>
                                                    <i class="fa fa-angle-down incrementBtn"></i>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(isset($not_display_configs_values['auto_favourite_max_favourites_count'])) : ?>
                                                <div class="form-group quantity-form">
                                                    <p class="text_color"><?= lang('max_favourites_count') ?></p>
                                                    <input
                                                        type="text"
                                                        class="form-control quantity"
                                                        placeholder="<?= lang('enter_number') ?>"
                                                        name="config[auto_favourite_max_favourites_count]"
                                                        value="<?= $not_display_configs_values['auto_favourite_max_favourites_count']['value'] ?>"
                                                        >
                                                    <i class="fa fa-angle-up decrementBtn"></i>
                                                    <i class="fa fa-angle-down incrementBtn"></i>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(isset($not_display_configs_values['auto_favourite_min_retweets_count'])) : ?>
                                                <div class="form-group quantity-form">
                                                    <p class="text_color"><?= lang('min_retweets_count') ?></p>
                                                    <input
                                                        type="text"
                                                        class="form-control quantity"
                                                        placeholder="<?= lang('enter_number') ?>"
                                                        name="config[auto_favourite_min_retweets_count]"
                                                        value="<?= $not_display_configs_values['auto_favourite_min_retweets_count']['value'] ?>"
                                                        >
                                                    <i class="fa fa-angle-up decrementBtn"></i>
                                                    <i class="fa fa-angle-down incrementBtn"></i>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(isset($not_display_configs_values['auto_favourite_max_retweets_count'])) : ?>
                                                <div class="form-group quantity-form">
                                                    <p class="text_color"><?= lang('max_retweets_count') ?></p>
                                                    <input
                                                        type="text"
                                                        class="form-control quantity"
                                                        placeholder="<?= lang('enter_number') ?>"
                                                        name="config[auto_favourite_max_retweets_count]"
                                                        value="<?= $not_display_configs_values['auto_favourite_max_retweets_count']['value'] ?>"
                                                        >
                                                    <i class="fa fa-angle-up decrementBtn"></i>
                                                    <i class="fa fa-angle-down incrementBtn"></i>
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif($available_config['key'] == 'auto_retweet') : ?>
                                            <?php if(isset($not_display_configs_values['auto_retweet_min_favourites_count'])) : ?>
                                                <div class="form-group quantity-form">
                                                    <p class="text_color"><?= lang('min_favourites_count') ?></p>
                                                    <input
                                                        type="text"
                                                        class="form-control quantity"
                                                        placeholder="<?= lang('enter_number') ?>"
                                                        name="config[auto_retweet_min_favourites_count]"
                                                        value="<?= $not_display_configs_values['auto_retweet_min_favourites_count']['value'] ?>"
                                                        >
                                                    <i class="fa fa-angle-up decrementBtn"></i>
                                                    <i class="fa fa-angle-down incrementBtn"></i>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(isset($not_display_configs_values['auto_retweet_max_favourites_count'])) : ?>
                                                <div class="form-group quantity-form">
                                                    <p class="text_color"><?= lang('max_favourites_count') ?></p>
                                                    <input
                                                        type="text"
                                                        class="form-control quantity"
                                                        placeholder="<?= lang('enter_number') ?>"
                                                        name="config[auto_retweet_max_favourites_count]"
                                                        value="<?= $not_display_configs_values['auto_retweet_max_favourites_count']['value'] ?>"
                                                        >
                                                    <i class="fa fa-angle-up decrementBtn"></i>
                                                    <i class="fa fa-angle-down incrementBtn"></i>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(isset($not_display_configs_values['auto_retweet_min_retweets_count'])) : ?>
                                                <div class="form-group quantity-form">
                                                    <p class="text_color"><?= lang('min_retweets_count') ?></p>
                                                    <input
                                                        type="text"
                                                        class="form-control quantity"
                                                        placeholder="<?= lang('enter_number') ?>"
                                                        name="config[auto_retweet_min_retweets_count]"
                                                        value="<?= $not_display_configs_values['auto_retweet_min_retweets_count']['value'] ?>"
                                                        >
                                                    <i class="fa fa-angle-up decrementBtn"></i>
                                                    <i class="fa fa-angle-down incrementBtn"></i>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(isset($not_display_configs_values['auto_retweet_max_retweets_count'])) : ?>
                                                <div class="form-group quantity-form">
                                                    <p class="text_color"><?= lang('max_retweets_count') ?></p>
                                                    <input
                                                        type="text"
                                                        class="form-control quantity"
                                                        placeholder="<?= lang('enter_number') ?>"
                                                        name="config[auto_retweet_max_retweets_count]"
                                                        value="<?= $not_display_configs_values['auto_retweet_max_retweets_count']['value'] ?>"
                                                        >
                                                    <i class="fa fa-angle-up decrementBtn"></i>
                                                    <i class="fa fa-angle-down incrementBtn"></i>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif;?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="b-Top m-t20 p-t20">
                    <div class="pull-sm-left p-tb10">
                        <a href="<?php echo site_url('settings/socialmedia/');?>" class="link ">
                            <i class="fa fa-long-arrow-left blue-color m-r5"></i>
                            Back to Social Media Settings
                        </a>
                    </div>
                    <div class="pull-sm-right">
                        <button type="submit" class="btn-save m-b20"><?= lang('save') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>