
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('analytics') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <ul class="nav nav-tabs settings_tab">
                <li class="setting_item active auto">
                    <a class="setting_link" href="<?php echo site_url('traffic#web'); ?>">
                        <i class="ti-user"></i>
                        <?= lang('web_traffic') ?>
                    </a>
                </li>
                <li class="setting_item auto">
                    <a class="setting_link" href="<?php echo site_url('traffic#search'); ?>">
                        <i class="ti-search"></i>
                        <?= lang('search_traffic') ?>
                    </a>
                </li>
                <li class="setting_item auto">
                    <a class="setting_link" href="<?php echo site_url('traffic#referral'); ?>">
                        <i class="ti-link"></i>
                        <?= lang('referral_traffic') ?>
                    </a>
                </li>
                <li class="setting_item auto">
                    <a class="setting_link" href="<?php echo site_url('traffic#direct'); ?>">
                        <i class="ti-direction"></i>
                        <?= lang('direct_traffic') ?>
                    </a>
                </li>
                <li class="setting_item auto">
                    <a class="setting_link" href="<?php echo site_url('traffic#adwords'); ?>">
                        <i class="ti-key"></i>
                        <?= lang('adwords_traffic') ?>
                    </a>
                </li>
                <li class="setting_item auto">
                    <a class="setting_link" href="<?php echo site_url('traffic#social'); ?>">
                        <i class="ti-key"></i>
                        <?= lang('social_traffic') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="tab-content settings_content">
                <div class="tab-pane active clearfix" id="web-traffic">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="date_range">
                                <div class="reportrange" >
                                    <i class="fa fa-calendar"></i>
                                    <span></span>
                                    <b class="caret"></b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ga_data">


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script id="web-template" type="text/x-handlebars-template">
    <div class="row">
        <div id="content">

        </div>
    </div>
</script>

<script id="data-template" type="text/x-handlebars-template">
    <div class="row">
        <div class="col-sm-4">
            <div class="traffic_block">
                <p class="gray_text">
                    <i class="ti-user"></i>
                    <span class="gray_text_big">{{visits}}</span> <?= lang('visits') ?>
                </p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="traffic_block">
                <p class="green_text">
                    <i class="ti-alarm-clock"></i>
                    <span class="green_text_big">{{unique_visits}}</span> <?= lang('unique_visits') ?>
                </p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="traffic_block">
                <p class="pink_text">
                    <i class="ti-time"></i>
                    <span class="pink_text_big">0:36:00</span> <?= lang('average_visit_duration') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8">
            <div class="diagrammBox traffic_shedule" id="chartbox">

            </div>
        </div>
        <div class="col-sm-4">
            <div class="traffic_block">
                <p class="blue_text">
                    <span class="blue_text_big">{{bounce_rate}}</span> <?= lang('bounce_rate') ?>
                </p>
                <div class="progress-pie" data-percent="{{bounce_rate}}">
                    <div class="ppc-progress">
                        <div class="ppc-progress-fill"></div>
                    </div>
                    <div class="ppc-percents">
                        <div class="pcc-percents-wrapper"><span>%</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="traffic_block">
                <p class="light_blue_text">
                    <span class="light_blue_text_big">{{new_visits_percent}}%</span> <?= lang('new_visits') ?>
                </p>
                <div class="progress-pie" data-percent="{{new_visits_percent}}">
                    <div class="ppc-progress">
                        <div class="ppc-progress-fill"></div>
                    </div>
                    <div class="ppc-percents">
                        <div class="pcc-percents-wrapper"><span>%</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>

<script id="table-template" type="text/x-handlebars-template">
    <div class="row">
        <div class="col-xs-12">
            <p class="strong-size text_color m-tb20 sectionTitle"></p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 hidden-phone">
            <div class="tablebox-inner loading" style="margin-bottom: 30px;">
                <span style="margin-left: 10px"><?= lang('loading_google_analytics_data') ?></span>
            </div>
        </div>
    </div>
</script>

<script id="table-hidden-phone-template" type="text/x-handlebars-template">
    {{#if result}}
        <table class="responsive-table m-b20 b-Bottom">
            <thead class="table_head">
                {{#each headers}}
                    <th>{{this}}</th>
                {{/each}}
            </thead>
            <tbody>
            {{#each result}}
                <tr>

                    {{#each this}}

                        <td>{{this}}</td>

                    {{/each}}
                </tr>
            {{/each}}


            </tbody>
        </table>

    {{else}}
        <div class="message-error configure-error">
            <?= lang('analytics_no_results') ?>
        </div>
    {{/if}}
</script>

<script id="alert-template" type="text/x-handlebars-template">
    <div class="notification notify_bad">
        <div class="container">
            <p class="notify_text"><i class="fa fa-bad"></i>{{message}}</p>
            <i class="fa fa-remove close_block"></i>
        </div>
    </div>
</script>


