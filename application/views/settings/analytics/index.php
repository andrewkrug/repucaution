<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_analytics') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_analytics') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
        <div class="col-md-10 col-lg-8">
            <div class="row">
                <div class="col-sm-6">
                    <p class="text_color strong-size"><?= lang('google_analytics') ?></p>
                    <p class="black smallText">
                        <?php if ( ! $access_token->token2) {
                            echo lang('connect_google_analytics');
                        } else {
                            echo lang('select_google_analytics');
                        } ?>
                    </p>
                </div>

                <div class="col-sm-6">
                    <div class="pull-sm-right">
                        <?php if ( ! $access_token->token2): ?>
                            <a href="<?php echo site_url('settings/analytics/connect'); ?>">
                                <button class="btn btn-add"><?= lang('connect') ?></button>
                            </a>
                        <?php else:?>
                            <a href="<?php echo site_url('settings/analytics/logout/'.$access_token->id); ?>">
                                <button class="btn btn-add"><?= lang('logout') ?></button>
                            </a>
                        <?php endif;?>
                    </div>
                </div>
            </div>
                <?php if ($access_token->instance_id): ?>
                    <?php if ( ! empty($account_info)): ?>
                        <table class="table account_analytics m-t20">
                            <tbody>
                            <tr>
                                <td>
                                    <p class="gray-color bold"><?= lang('account') ?></p>
                                </td>
                                <td>
                                    <p><?php echo HTML::chars($account_info['account_name']); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="gray-color bold"><?= lang('webproperty') ?></p>
                                </td>
                                <td>
                                    <p><a href="" class="link"><?php echo HTML::chars($account_info['webproperty_name']); ?></a></p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="gray-color bold"><?= lang('profile') ?></p>
                                </td>
                                <td>
                                    <p><?php echo HTML::chars($account_info['profile_name']); ?></p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="b-Top clearfix m-b10 p-t20">
                                    <div class="pull-sm-right">
                                        <a class="black-btn pull-right" href="<?php echo site_url('settings/analytics/accounts'); ?>">
                                        <button class="btn btn-save"><?= lang('change') ?></button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                <?php else:?>

                <?php endif;?>
        </div>
    </div>
</div>
