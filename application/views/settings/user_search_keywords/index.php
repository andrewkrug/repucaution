<?php
/**
 * @var bool $autoSearchAndFollowTwitter
 * @var array $keywords
 * @var array $configs
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_user_search_keywords') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <?= lang('settings_user_search_keywords') ?>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
    <div class="row custom-form">
        <?php foreach($configs as $type => $config_array) : ?>
            <?php foreach($config_array as $config) : ?>
                <div class="col-sm-4">
                    <label for="config_checkbox_<?= $config['token']['id'] ?>" class="cb-checkbox">
                        <input type="checkbox"
                               id="config_checkbox_<?= $config['token']['id'] ?>"
                               class="config_checkbox"
                               data-key="<?= $config['config']['key'] ?>"
                               data-id="<?= $config['token']['id'] ?>"
                               <?php if($config['values'][$config['config']['key']]):?>checked="checked"<?php endif; ?>
                        >
                        <cite class="ti-<?= $type; ?> blue-color"></cite>
                        <?= $config['token']['name'] ?>
                    </label>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group quantity-form m-t20">
                                <p class="text_color strong-size">
                                    <?= lang('max_daily_auto_follow_users_by_search') ?>
                                </p>
                                <div class="row">
                                    <div class="col-sm-4 quantity_block">
                                        <input
                                            type="text"
                                            class="form-control quantity config_input"
                                            data-key="max_daily_auto_follow_users_by_search"
                                            data-id="<?= $config['token']['id'] ?>"
                                            value="<?= $config['values']['max_daily_auto_follow_users_by_search'] ?>"
                                        >
                                        <i class="fa fa-angle-up decrementBtn"></i>
                                        <i class="fa fa-angle-down incrementBtn"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    <div class="row custom-form">
        <div class="col-md-10 col-lg-8">
            <form id="user-search-keywords-form" action="<?php echo site_url('settings/user_search_keywords'); ?>" method="POST">
                <input type="hidden" name="submit"/>
                <div class="row" id="user-search-keywords">
                    <?php $i = 1; ?>
                    <?php foreach ($keywords as $keyword): ?>
                        <?php $id = $keyword->id ? $keyword->id : 'new_' . $i; ?>
                        <div class="col-xs-12 m-t20 user_search_keywords_block">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="b-Bottom m-b15">
                                        <div class="form-group">
                                            <input class="form-control m-b10"
                                                   name="keyword[<?php echo $id; ?>]"
                                                   value="<?php echo HTML::chars($keyword->keyword); ?>"
                                                   placeholder="<?= lang('keywords') ?>">
                                            <i class="cb-remove user_search_keywords_delete"></i>
                                            <div class="clearfix">
                                                <label class="cb-checkbox text-size pull-sm-left">
                                                    <input type="checkbox"
                                                           id="keyword_exact_<?php echo $id ?>"
                                                           name="exact[<?php echo $id; ?>]"
                                                           <?php if ($keyword->exact): ?>checked="checked"<?php endif; ?>>
                                                    <?= lang('exact') ?>
                                                </label>
                                                <div class="pull-sm-right">
                                                    <a href="" class="link show_include_exclude"><?= lang('include_exclude') ?></a>
                                                </div>
                                            </div>

                                            <div class="toggle_include_exclude row"  style="display: none;">
                                                <div class="col-sm-6">
                                                    <p class="text_color"><?= lang('include') ?></p>
                                                    <div class="form-group">
                                                        <textarea class="form-control"
                                                                  id="user_search_keywords_include_<?php echo $id ?>"
                                                                  name="include[<?php echo $id; ?>]"
                                                                  placeholder="<?= lang('comma_separated_words') ?>"><?php echo HTML::chars($keyword->get_other_fields('include', TRUE)); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p class="text_color"><?= lang('exclude') ?></p>
                                                    <div class="form-group">
                                                        <textarea class="form-control"
                                                                  id="user_search_keywords_exclude_<?php echo $id ?>"
                                                                  name="exclude[<?php echo $id; ?>]"
                                                                  placeholder="<?= lang('comma_separated_words') ?>"><?php echo HTML::chars($keyword->get_other_fields('exclude', TRUE)); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row m-tb10">
                                                <div class="col-sm-6">
                                                    <a href="" class="link show_followers m-t10"><?= lang('min_max_followers') ?></a>
                                                </div>
                                                <div class="col-sm-6 sm-right">
                                                    <a href="" class="link show_time m-t10"><?= lang('follow_time') ?></a>
                                                </div>
                                            </div>
                                            <div class="followers_block row">
                                                <div class="col-sm-6">
                                                    <div class="form-group quantity-form">
                                                        <p class="text_color"><?= lang('min') ?></p>
                                                        <input type="text" class="form-control quantity"
                                                               id="user_search_keywords_min_followers_<?php echo $id; ?>"
                                                               name="min_followers[<?php echo $id; ?>]"
                                                               value="<?php echo $keyword->min_followers; ?>">
                                                        <i class="fa fa-angle-up decrementBtn"></i>
                                                        <i class="fa fa-angle-down incrementBtn"></i>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group quantity-form">
                                                        <p class="text_color"><?= lang('max') ?></p>
                                                        <input type="text" class="form-control quantity"
                                                               id="user_search_keywords_max_followers_<?php echo $id; ?>"
                                                               name="max_followers[<?php echo $id; ?>]"
                                                               value="<?php echo $keyword->max_followers; ?>">
                                                        <i class="fa fa-angle-up decrementBtn"></i>
                                                        <i class="fa fa-angle-down incrementBtn"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                                $startTime = $keyword->getStartTime();
                                                $endTime = $keyword->getEndTime();
                                            ?>
                                            <div class="time_block row">
                                                <div class="col-sm-12">
                                                    <p class="text_color"><?= lang('start_time') ?></p>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <select class="chosen-select" name="hours_start[<?php echo $id; ?>]" data-height="100">
                                                                <?php for($hour = 1; $hour <= 12; $hour ++ ): ?>
                                                                    <option <?php echo $startTime['hours'] == $hour ? 'selected' : ''; ?> value="<?php echo $hour; ?>"><?php echo $hour; ?> </option>
                                                                <?php endfor;?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <select class="chosen-select"  name="minutes_start[<?php echo $id; ?>]" data-height="100">
                                                                <?php for($minute = 0; $minute < 60; $minute += 5 ): ?>
                                                                    <option <?php echo $startTime['minutes'] == $minute ? 'selected' : ''; ?> value="<?php echo $minute; ?>"><?php echo $minute; ?> </option>
                                                                <?php endfor;?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <select class="chosen-select"  name="am_pm_start[<?php echo $id; ?>]">
                                                                <option <?php echo $startTime['am_pm'] == 'am'  ? 'selected' : ''; ?> value="am">AM</option>
                                                                <option <?php echo $startTime['am_pm'] == 'pm'  ? 'selected' : ''; ?> value="pm">PM</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <p class="text_color"><?= lang('end_time') ?></p>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <select class="chosen-select" name="hours_end[<?php echo $id; ?>]" data-height="100">
                                                                <?php for($hour = 1; $hour <= 12; $hour ++ ): ?>
                                                                    <option <?php echo $endTime['hours'] == $hour ? 'selected' : ''; ?> value="<?php echo $hour; ?>"><?php echo $hour; ?> </option>
                                                                <?php endfor;?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <select class="chosen-select"  name="minutes_end[<?php echo $id; ?>]" data-height="100">
                                                                <?php for($minute = 0; $minute < 60; $minute += 5 ): ?>
                                                                    <option <?php echo $endTime['minutes'] == $minute ? 'selected' : ''; ?> value="<?php echo $minute; ?>"><?php echo $minute; ?> </option>
                                                                <?php endfor;?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <select class="chosen-select"  name="am_pm_end[<?php echo $id; ?>]">
                                                                <option <?php echo $endTime['am_pm'] == 'am'  ? 'selected' : ''; ?> value="am">AM</option>
                                                                <option <?php echo $endTime['am_pm'] == 'pm'  ? 'selected' : ''; ?> value="pm">PM</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $i += 1; ?>
                    <?php endforeach; ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="pull-sm-right">
                            <a class="btn btn-add user_search_keywords_add_btn m-tb20 m-r20"><?=lang('add_keyword') ?></a>
                            <input class="btn btn-save m-tb20 pull-right" type="submit" value="<?= lang('save') ?>"/>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script id="keyword-template" type="text/x-handlebars-template">
    <div class="col-xs-12 m-t20 user_search_keywords_block">
        <div class="row">
            <div class="col-xs-12">
                <div class="b-Bottom m-b15">
                    <div class="form-group">
                        <input class="form-control m-b10"
                               name="keyword[{{ id }}]"
                               placeholder="<?= lang('keywords') ?>">
                        <i class="cb-remove user_search_keywords_delete"></i>
                        <div class="clearfix">
                            <label class="cb-checkbox text-size pull-sm-left">
                                <input type="checkbox"
                                       id="keyword_exact_{{ id }}"
                                       name="exact[{{ id }}]">
                                <?= lang('exact') ?>
                            </label>
                            <div class="pull-sm-right">
                                <a href="" class="link show_include_exclude"><?= lang('include_exclude') ?></a>
                            </div>
                        </div>

                        <div class="toggle_include_exclude row" style="display: none;">
                            <div class="col-sm-6">
                                <p class="text_color"><?= lang('include') ?></p>
                                <div class="form-group">
                                <textarea class="form-control"
                                          id="user_search_keywords_include_{{ id }}"
                                          name="include[{{ id }}]"
                                          placeholder="<?= lang('comma_separated_words') ?>"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <p class="text_color"><?= lang('exclude') ?></p>
                                <div class="form-group">
                                    <textarea class="form-control"
                                              id="user_search_keywords_exclude_{{ id }}"
                                              name="exclude[{{ id }}]"
                                              placeholder="<?= lang('comma_separated_words') ?>"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row m-tb10">
                            <div class="col-sm-6">
                                <a href="" class="link show_followers m-t10"><?= lang('min_max_followers') ?></a>
                            </div>
                            <div class="col-sm-6 sm-right">
                                <a href="" class="link show_time m-t10"><?= lang('follow_time') ?></a>
                            </div>
                        </div>
                        <div class="followers_block row">
                            <div class="col-sm-6">
                                <div class="form-group quantity-form">
                                    <p class="text_color"><?= lang('min') ?></p>
                                    <input type="text" class="form-control quantity"
                                           id="user_search_keywords_min_followers_{{ id }}"
                                           name="min_followers[{{ id }}]">
                                    <i class="fa fa-angle-up decrementBtn"></i>
                                    <i class="fa fa-angle-down incrementBtn"></i>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group quantity-form">
                                    <p class="text_color"><?= lang('max') ?></p>
                                    <input type="text" class="form-control quantity"
                                           id="user_search_keywords_max_followers_{{ id }}"
                                           name="max_followers[{{ id }}]">
                                    <i class="fa fa-angle-up decrementBtn"></i>
                                    <i class="fa fa-angle-down incrementBtn"></i>
                                </div>
                            </div>
                        </div>
                        <div class="time_block row">
                            <div class="col-sm-12">
                                <p class="text_color"><?= lang('start_time') ?></p>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <select class="chosen-select" name="hours_start[{{ id }}]" data-height="100">
                                            <?php for($hour = 1; $hour <= 12; $hour ++ ): ?>
                                                <option value="<?php echo $hour; ?>"><?php echo $hour; ?> </option>
                                            <?php endfor;?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="chosen-select"  name="minutes_start[{{ id }}]" data-height="100">
                                            <?php for($minute = 0; $minute < 60; $minute += 5 ): ?>
                                                <option value="<?php echo $minute; ?>"><?php echo $minute; ?> </option>
                                            <?php endfor;?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="chosen-select"  name="am_pm_start[{{ id }}]">
                                            <option value="am">AM</option>
                                            <option value="pm">PM</option>
                                        </select>
                                    </div>
                                </div>
                                <p class="text_color"><?= lang('end_time') ?></p>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <select class="chosen-select" name="hours_end[{{ id }}]" data-height="100">
                                            <?php for($hour = 1; $hour <= 12; $hour ++ ): ?>
                                                <option value="<?php echo $hour; ?>"><?php echo $hour; ?> </option>
                                            <?php endfor;?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="chosen-select"  name="minutes_end[{{ id }}]" data-height="100">
                                            <?php for($minute = 0; $minute < 60; $minute += 5 ): ?>
                                                <option value="<?php echo $minute; ?>"><?php echo $minute; ?> </option>
                                            <?php endfor;?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="chosen-select"  name="am_pm_end[{{ id }}]">
                                            <option value="am">AM</option>
                                            <option value="pm">PM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
